<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Slip - {{ $payslip->month }} {{ $payslip->year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .company_logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .company_logo img {
            max-width: 100px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .Payslip_Item ul {
            list-style: none;
            padding: 0;
        }
        .Payslip_Item li {
            margin-bottom: 8px;
        }
        .mid_hd {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #f0f0f0;
        }
        .mid_hd_new {
            margin-top: 30px;
        }
        .note_payslip {
            text-align: center;
            margin-top: 30px;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .row {
            display: flex;
            margin-bottom: 20px;
        }
        .col-6 {
            width: 50%;
            padding: 0 10px;
        }
    </style>
</head>
<body>
    <div class="company_logo">
        <img src="{{ public_path('assets/images/logo.png') }}" alt="Logo">
    </div>
    
    <div class="header">
        <h5>राजस्थान सरकार</h5>
        <p>सामाजिक लेखा परीक्षा, जवाबदेही एवं पारदर्शिता सोसायटी (SSAAT), राजस्थान, जयपुर</p>
        <p><strong>राजकीय पारदर्शिता, सामाजिक उतरदायित्व</strong></p>
    </div>
    
    <div class="row">
        <div class="col-6">
            <div class="Payslip_Item">
                <ul>
                    <li><strong>Month </strong>{{ $payslip->month }}, {{ $payslip->year }}</li>
                    <li><strong>Employee Name </strong>{{ $employee->full_name }}</li>
                    <li><strong>Department </strong>{{ $employee->department ?? 'N/A' }}</li>
                    <li><strong>Location </strong>{{ $employee->address ? explode(',', $employee->address)[0] : 'Jaipur, Rajasthan' }}</li>
                </ul>
            </div>
        </div>
        <div class="col-6">
            <div class="Payslip_Item">
                <ul>
                    <li><strong>Date Of Joining </strong>{{ $employee->date_of_joining ? $employee->date_of_joining->format('d/m/Y') : 'N/A' }}</li>
                    <li><strong>Days Payable </strong>{{ number_format($payslip->days_payable, 2) }}</li>
                    <li><strong>Account No </strong>{{ $employee->bank_account_number ?? 'N/A' }}</li>
                    <li><strong>Pan Card No </strong>N/A</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="mid_hd">
        <h5>Visual Examples of Earnings and Deductions</h5>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="Payslip_Item">
                <ul>
                    <li><strong>Basic Salary </strong>{{ number_format($payslip->basic_salary, 2) }}</li>
                    <li><strong>HRA </strong>{{ number_format($payslip->hra, 2) }}</li>
                    <li><strong>Conveyance Allowance </strong>{{ number_format($payslip->conveyance_allowance, 2) }}</li>
                    <li><strong>Medical Allowance </strong>{{ number_format($payslip->medical_allowance, 2) }}</li>
                    <li><strong>Special Allowance </strong>{{ number_format($payslip->special_allowance, 2) }}</li>
                </ul>
            </div>
        </div>
        <div class="col-6">
            <div class="Payslip_Item">
                <ul>
                    <li><strong>ESI </strong>{{ number_format($payslip->esi, 2) }}</li>
                    <li><strong>PF </strong>{{ number_format($payslip->pf, 2) }}</li>
                    <li><strong>TDS </strong>{{ number_format($payslip->tds, 2) }}</li>
                    <li><strong>10 % Deduction </strong>{{ number_format($payslip->deduction_10_percent, 2) }}</li>
                    <li><strong>Mobile Deduction </strong>{{ number_format($payslip->mobile_deduction, 2) }}</li>
                    <li><strong>Comp Off </strong>{{ number_format($payslip->comp_off, 2) }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="mid_hd mid_hd_new">
        <div class="row">
            <div class="col-6">
                <div class="Payslip_Item text-center">
                    <ul>
                        <li><strong>Total </strong>{{ number_format($payslip->total_earnings, 2) }}</li>
                    </ul>
                </div>
            </div>
            <div class="col-6">
                <div class="Payslip_Item text-center">
                    <ul>
                        <li><strong>Salary Payable </strong>{{ number_format($payslip->salary_payable, 2) }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <p class="note_payslip">Note: THIS IS A COMPUTER-GENERATED DOCUMENT AND IT DOES NOT REQUIRE A SIGNATURE.</p>
</body>
</html>


