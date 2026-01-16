<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\TadaClaim;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\NotificationHelper;

class TadaClaimController extends Controller
{
    /**
     * Display a listing of the employee's TA/DA claims.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return redirect()->route('employee.dashboard')->with('error', 'Employee record not found.');
            }

            return view('employee.tada-claims.index', compact('employee'));
        } catch (\Exception $e) {
            return redirect()->route('employee.dashboard')->with('error', 'Error loading TA/DA claims: ' . $e->getMessage());
        }
    }

    /**
     * Get employee's TA/DA claims (for DataTable)
     */
    public function getMyClaims(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return response()->json(['error' => 'Employee record not found.'], 404);
            }

            $query = TadaClaim::where('employee_id', $employee->id);

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('purpose', 'like', "%{$search}%")
                      ->orWhere('travel_date', 'like', "%{$search}%");
                });
            }

            $totalRecords = TadaClaim::where('employee_id', $employee->id)->count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id', 'travel_date', 'purpose', 'amount_claimed', 'status', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $claims = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($claims as $claim) {
                    
                  //Status badge HTML
                   if ($claim->status === 'approved') {
                        $rejection_reason = "N/A";
                       $statusHtml = '<span class="badge badge-success">Approved</span>';
                   } elseif ($claim->status === 'rejected') {
                       $rejection_reason = $claim->rejection_reason; 
                       $statusHtml = '<span class="badge badge-danger">Rejected</span>';
                   } else {
                       $rejection_reason = "N/A";
                       $statusHtml = '<span class="badge badge-warning">Pending</span>';
                   }
 

                $billFilesCount = $claim->bill_files ? count($claim->bill_files) : ($claim->bill_file ? 1 : 0);
                $data[] = [
                    'id' => $claim->id,
                    'travel_date' => $claim->travel_date->format('d-m-Y'),
                    'purpose' => $claim->purpose,
                    'distance' => $claim->distance ?? 'N/A',
                    'rejection_reason' => $rejection_reason ?? 'N/A',
                    'amount_claimed' => '₹' . number_format($claim->amount_claimed, 2),
                    'status' => $statusHtml,
                    'bill_file' => $billFilesCount > 0 ? $billFilesCount . ' file(s)' : 'N/A',
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading claims: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created TA/DA claim.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found.'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'travel_date' => 'required|date',
                'purpose' => 'required|string|max:255',
                'distance' => 'nullable|string|max:50',
                'amount_claimed' => 'required|numeric|min:0',
                'bill_files' => 'nullable|array',
                'bill_files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'employee_id' => $employee->id,
                'travel_date' => $request->travel_date,
                'purpose' => $request->purpose,
                'distance' => $request->distance,
                'amount_claimed' => $request->amount_claimed,
                'status' => 'pending',
            ];

            // Handle multiple bill files
            if ($request->hasFile('bill_files')) {
                $billFiles = [];
                foreach ($request->file('bill_files') as $file) {
                    $billFiles[] = $file->store('tada-bills', 'public');
                }
                $data['bill_files'] = $billFiles;
                // Keep first file in bill_file for backward compatibility
                if (!empty($billFiles)) {
                    $data['bill_file'] = $billFiles[0];
                }
            } elseif ($request->hasFile('bill_file')) {
                // Backward compatibility: single file upload
                $data['bill_file'] = $request->file('bill_file')->store('tada-bills', 'public');
                $data['bill_files'] = [$data['bill_file']];
            }

            $claim = TadaClaim::create($data);

            // Notify Accounts about new TA/DA claim
            NotificationHelper::notifyByRole(
                'accounts',
                'New TA/DA Claim',
                "{$employee->full_name} has submitted a TA/DA claim for {$request->purpose} on {$request->travel_date}. Amount: ₹" . number_format($request->amount_claimed, 2),
                'info',
                route('accounts.tada.index')
            );

            // Notify Admin about new TA/DA claim
            NotificationHelper::notifyByRole(
                'admin',
                'New TA/DA Claim',
                "{$employee->full_name} has submitted a TA/DA claim for {$request->purpose} on {$request->travel_date}. Amount: ₹" . number_format($request->amount_claimed, 2),
                'info',
                route('admin.tada-claims.index')
            );

            return response()->json([
                'success' => true,
                'message' => 'TA/DA claim submitted successfully.',
                'redirect' => route('employee.tada-claims.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting claim: ' . $e->getMessage()
            ], 500);
        }
    }
}
