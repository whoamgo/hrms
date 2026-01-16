<div class="payslip-details">
    <div class="row">
        <div class="col-md-12">
            <h5>Payslip Details</h5>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <p><strong>Employee:</strong> {{ $payslip->employee->full_name ?? 'N/A' }}</p>
            <p><strong>Employee ID:</strong> {{ $payslip->employee->employee_id ?? 'N/A' }}</p>
            <p><strong>Month:</strong> {{ $payslip->month }}</p>
            <p><strong>Year:</strong> {{ $payslip->year }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Days Payable:</strong> {{ $payslip->days_payable ?? 0 }}</p>
            <p><strong>Generated At:</strong> {{ $payslip->created_at ? $payslip->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <h6>Earnings</h6>
            <table class="table table-sm">
                <tr>
                    <td>Basic Salary:</td>
                    <td class="text-right">₹{{ number_format($payslip->basic_salary, 2) }}</td>
                </tr>
                <tr>
                    <td>HRA:</td>
                    <td class="text-right">₹{{ number_format($payslip->hra, 2) }}</td>
                </tr>
                <tr>
                    <td>Conveyance Allowance:</td>
                    <td class="text-right">₹{{ number_format($payslip->conveyance_allowance, 2) }}</td>
                </tr>
                <tr>
                    <td>Medical Allowance:</td>
                    <td class="text-right">₹{{ number_format($payslip->medical_allowance, 2) }}</td>
                </tr>
                <tr>
                    <td>Special Allowance:</td>
                    <td class="text-right">₹{{ number_format($payslip->special_allowance, 2) }}</td>
                </tr>
                <tr class="font-weight-bold">
                    <td>Total Earnings:</td>
                    <td class="text-right">₹{{ number_format($payslip->total_earnings, 2) }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6>Deductions</h6>
            <table class="table table-sm">
                <tr>
                    <td>ESI:</td>
                    <td class="text-right">₹{{ number_format($payslip->esi, 2) }}</td>
                </tr>
                <tr>
                    <td>PF:</td>
                    <td class="text-right">₹{{ number_format($payslip->pf, 2) }}</td>
                </tr>
                <tr>
                    <td>TDS:</td>
                    <td class="text-right">₹{{ number_format($payslip->tds, 2) }}</td>
                </tr>
                <tr>
                    <td>Other Deductions:</td>
                    <td class="text-right">₹{{ number_format($payslip->other_deductions ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Mobile Deduction:</td>
                    <td class="text-right">₹{{ number_format($payslip->mobile_deduction, 2) }}</td>
                </tr>
                <tr>
                    <td>Comp Off:</td>
                    <td class="text-right">₹{{ number_format($payslip->comp_off, 2) }}</td>
                </tr>
                <tr class="font-weight-bold">
                    <td>Total Deductions:</td>
                    <td class="text-right">₹{{ number_format($payslip->total_deductions, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12 text-right">
            <h5>Net Payable: ₹{{ number_format($payslip->salary_payable, 2) }}</h5>
        </div>
    </div>
</div>

