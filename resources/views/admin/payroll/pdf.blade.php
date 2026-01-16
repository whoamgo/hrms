<!DOCTYPE html>
<html>
<head>
    <title>Payslip - {{ $payslip->month }} {{ $payslip->year }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .payslip-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .payslip-table th, .payslip-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .payslip-table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PAYSLIP</h2>
        <p>{{ $payslip->month }} {{ $payslip->year }}</p>
    </div>
    
    <div>
        <p><strong>Employee Name:</strong> {{ $payslip->employee->full_name ?? 'N/A' }}</p>
        <p><strong>Employee ID:</strong> {{ $payslip->employee->employee_id ?? 'N/A' }}</p>
        <p><strong>Days Payable:</strong> {{ $payslip->days_payable ?? 0 }}</p>
    </div>
    
    <table class="payslip-table">
        <thead>
            <tr>
                <th>Earnings</th>
                <th class="text-right">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Salary</td>
                <td class="text-right">{{ number_format($payslip->basic_salary, 2) }}</td>
            </tr>
            <tr>
                <td>HRA</td>
                <td class="text-right">{{ number_format($payslip->hra, 2) }}</td>
            </tr>
            <tr>
                <td>Conveyance Allowance</td>
                <td class="text-right">{{ number_format($payslip->conveyance_allowance, 2) }}</td>
            </tr>
            <tr>
                <td>Medical Allowance</td>
                <td class="text-right">{{ number_format($payslip->medical_allowance, 2) }}</td>
            </tr>
            <tr>
                <td>Special Allowance</td>
                <td class="text-right">{{ number_format($payslip->special_allowance, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Earnings</td>
                <td class="text-right">{{ number_format($payslip->total_earnings, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <table class="payslip-table">
        <thead>
            <tr>
                <th>Deductions</th>
                <th class="text-right">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ESI</td>
                <td class="text-right">{{ number_format($payslip->esi, 2) }}</td>
            </tr>
            <tr>
                <td>PF</td>
                <td class="text-right">{{ number_format($payslip->pf, 2) }}</td>
            </tr>
            <tr>
                <td>TDS</td>
                <td class="text-right">{{ number_format($payslip->tds, 2) }}</td>
            </tr>
            <tr>
                <td>Other Deductions</td>
                <td class="text-right">{{ number_format($payslip->other_deductions ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Mobile Deduction</td>
                <td class="text-right">{{ number_format($payslip->mobile_deduction, 2) }}</td>
            </tr>
            <tr>
                <td>Comp Off</td>
                <td class="text-right">{{ number_format($payslip->comp_off, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Deductions</td>
                <td class="text-right">{{ number_format($payslip->total_deductions, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <div style="text-align: right; margin-top: 20px;">
        <h3>Net Payable: ₹{{ number_format($payslip->salary_payable, 2) }}</h3>
    </div>
</body>
</html>

