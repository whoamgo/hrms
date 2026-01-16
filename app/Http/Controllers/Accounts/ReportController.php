<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payslip;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportController extends Controller
{
    public function index()
    {
        try {
            $departments = Employee::distinct()->pluck('department')->filter();
            return view('accounts.reports.index', compact('departments'));
        } catch (\Exception $e) {
            return redirect()->route('accounts.dashboard')->with('error', 'Error loading reports: ' . $e->getMessage());
        }
    }

    public function generate(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'report_type' => 'required|in:Employee List,Leave Report,Payroll Report',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = null;
            $reportType = $request->report_type;

            if ($reportType == 'Leave Report') {
                $query = Leave::with('employee')
                    ->whereBetween('from_date', [$request->from_date, $request->to_date]);

                if ($request->filled('employee_type')) {
                    $query->whereHas('employee', function($q) use ($request) {
                        $q->where('employee_type', $request->employee_type);
                    });
                }

                if ($request->filled('department')) {
                    $query->whereHas('employee', function($q) use ($request) {
                        $q->where('department', $request->department);
                    });
                }

                $data = $query->get();
            } elseif ($reportType == 'Employee List') {
                $query = Employee::query();

                if ($request->filled('employee_type')) {
                    $query->where('employee_type', $request->employee_type);
                }

                if ($request->filled('department')) {
                    $query->where('department', $request->department);
                }

                $data = $query->get();
            } elseif ($reportType == 'Payroll Report') {
                $query = Payslip::with('employee');

                if ($request->filled('employee_type')) {
                    $query->whereHas('employee', function($q) use ($request) {
                        $q->where('employee_type', $request->employee_type);
                    });
                }

                if ($request->filled('department')) {
                    $query->whereHas('employee', function($q) use ($request) {
                        $q->where('department', $request->department);
                    });
                }

                $data = $query->get();
            }

            // Convert to array for JSON response
            $dataArray = [];
            foreach ($data as $item) {
                $dataArray[] = is_object($item) ? $item->toArray() : $item;
            }

            return response()->json([
                'success' => true,
                'data' => $dataArray,
                'report_type' => $reportType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $data = json_decode($request->data, true);
            $reportType = $request->report_type;

            // Convert data to array format for PDF
            $pdfData = [];
            if (is_array($data)) {
                foreach ($data as $item) {
                    $pdfData[] = is_array($item) ? $item : (is_object($item) ? $item->toArray() : $item);
                }
            }

            $pdf = PDF::loadView('accounts.reports.pdf', ['data' => $pdfData, 'reportType' => $reportType]);
            return $pdf->download('report-' . strtolower(str_replace(' ', '-', $reportType)) . '-' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting PDF: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $data = json_decode($request->data, true);
            $reportType = $request->report_type;

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($reportType);

            // Set headers
            $headers = [];
            if ($reportType == 'Leave Report') {
                $headers = ['#', 'Employee Name', 'Employee Type', 'Department', 'Leave Type', 'From Date', 'To Date', 'Total Days', 'Reason', 'Status'];
            } elseif ($reportType == 'Employee List') {
                $headers = ['#', 'Employee ID', 'Name', 'Employee Type', 'Department', 'Designation', 'Status'];
            } elseif ($reportType == 'Payroll Report') {
                $headers = ['#', 'Employee Name', 'Month', 'Basic Salary', 'Total Earnings', 'Total Deductions', 'Net Pay'];
            }

            // Style header row
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];

            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
                $col++;
            }

            // Add data
            $row = 2;
            foreach ($data as $index => $item) {
                $col = 'A';
                if ($reportType == 'Leave Report') {
                    $sheet->setCellValue($col++, $index + 1);
                    $sheet->setCellValue($col++, $item['employee']['full_name'] ?? 'N/A');
                    $sheet->setCellValue($col++, $item['employee']['employee_type'] ?? 'N/A');
                    $sheet->setCellValue($col++, $item['employee']['department'] ?? 'N/A');
                    $sheet->setCellValue($col++, $item['leave_type'] == 'CL' ? 'Casual Leave' : ($item['leave_type'] == 'SL' ? 'Sick Leave' : 'Special Leave'));
                    $sheet->setCellValue($col++, \Carbon\Carbon::parse($item['from_date'])->format('d/m/Y'));
                    $sheet->setCellValue($col++, \Carbon\Carbon::parse($item['to_date'])->format('d/m/Y'));
                    $sheet->setCellValue($col++, $item['total_days']);
                    $sheet->setCellValue($col++, $item['reason'] ?? 'N/A');
                    $sheet->setCellValue($col++, ucfirst($item['status']));
                } elseif ($reportType == 'Employee List') {
                    $sheet->setCellValue($col++, $index + 1);
                    $sheet->setCellValue($col++, $item['employee_id']);
                    $sheet->setCellValue($col++, $item['full_name']);
                    $sheet->setCellValue($col++, $item['employee_type']);
                    $sheet->setCellValue($col++, $item['department'] ?? 'N/A');
                    $sheet->setCellValue($col++, $item['designation'] ?? 'N/A');
                    $sheet->setCellValue($col++, ucfirst($item['status']));
                } elseif ($reportType == 'Payroll Report') {
                    $sheet->setCellValue($col++, $index + 1);
                    $sheet->setCellValue($col++, $item['employee']['full_name'] ?? 'N/A');
                    $sheet->setCellValue($col++, $item['month'] . ' ' . $item['year']);
                    $sheet->setCellValue($col++, $item['basic_salary']);
                    $sheet->setCellValue($col++, $item['total_earnings']);
                    $sheet->setCellValue($col++, $item['total_deductions']);
                    $sheet->setCellValue($col++, $item['salary_payable']);
                }
                $row++;
            }

            // Auto-size columns
            foreach (range('A', $col) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'report-' . strtolower(str_replace(' ', '-', $reportType)) . '-' . date('Y-m-d') . '.xlsx';
            $filePath = storage_path('app/temp/' . $filename);
            
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            $writer->save($filePath);

            return response()->download($filePath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting Excel: ' . $e->getMessage());
        }
    }
}
