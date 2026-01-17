<!DOCTYPE html>
<html lang="hi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Salary Slip - {{ $payslip->month }} {{ $payslip->year }}</title>

    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            margin: 20px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .text-center {
            text-align: center;
        }

        .company_logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .company_logo img {
            max-width: 100px;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 6px;
        }

        .mid_hd {
            text-align: center;
            padding: 10px;
            background-color: #f0f0f0;
            margin: 20px 0;
            font-weight: bold;
        }

        .note_payslip {
            text-align: center;
            margin-top: 30px;
            font-style: italic;
        }
    </style>
</head>

<body>

    <!-- Logo -->
    <div class="company_logo">
        <img src="{{ public_path('assets/images/logo.png') }}" alt="Logo">
    </div>

    <!-- Header -->
    <div class="text-center">
        <h4>Government of Rajasthan</h4>
        <p>Social Audit, Accountability and Transparency Society (SSAAT), Rajasthan, Jaipur</p>
        <p><strong>Government Transparency and Social Accountability</strong></p>
    </div>

    <!-- Employee Details -->
    <table>
        <tr>
            <td width="50%" valign="top">
                <ul>
                    <li><strong>Month :</strong> {{ $payslip->month }}, {{ $payslip->year }}</li>
                    <li><strong>Employee Name :</strong> {{ $employee->full_name }}</li>
                    <li><strong>Department :</strong> {{ $employee->department ?? 'N/A' }}</li>
                    <li><strong>Location :</strong>
                        {{ $employee->address ? explode(',', $employee->address)[0] : 'Jaipur, Rajasthan' }}
                    </li>
                </ul>
            </td>

            <td width="50%" valign="top">
                <ul>
                    <li><strong>Date Of Joining :</strong>
                        {{ $employee->date_of_joining ? $employee->date_of_joining->format('d/m/Y') : 'N/A' }}
                    </li>
                    <li><strong>Days Payable :</strong> {{ number_format($payslip->days_payable, 2) }}</li>
                    <li><strong>Account No :</strong> {{ $employee->bank_account_number ?? 'N/A' }}</li>
                    <li><strong>Pan Card No :</strong> N/A</li>
                </ul>
            </td>
        </tr>
    </table>

    <!-- Earnings / Deductions Heading -->
    <div class="mid_hd">Earnings and Deductions</div>

    <!-- Earnings & Deductions -->
    <table>
        <tr>
            <td width="50%" valign="top">
                <ul>
                    <li><strong>Basic Salary :</strong> &#8377;{{ number_format($payslip->basic_salary, 2) }}</li>
                    <li><strong>HRA :</strong> &#8377;{{ number_format($payslip->hra, 2) }}</li>
                    <li><strong>Conveyance Allowance :</strong> &#8377;{{ number_format($payslip->conveyance_allowance, 2) }}</li>
                    <li><strong>Medical Allowance :</strong> &#8377;{{ number_format($payslip->medical_allowance, 2) }}</li>
                    <li><strong>Special Allowance :</strong> &#8377;{{ number_format($payslip->special_allowance, 2) }}</li>
                </ul>
            </td>

            <td width="50%" valign="top">
                <ul>
                    <li><strong>ESI :</strong> &#8377;{{ number_format($payslip->esi, 2) }}</li>
                    <li><strong>PF :</strong> &#8377;{{ number_format($payslip->pf, 2) }}</li>
                    <li><strong>TDS :</strong> &#8377;{{ number_format($payslip->tds, 2) }}</li>
                    <li><strong>10% Deduction :</strong> &#8377;{{ number_format($payslip->deduction_10_percent, 2) }}</li>
                    <li><strong>Mobile Deduction :</strong> &#8377;{{ number_format($payslip->mobile_deduction, 2) }}</li>
                    <li><strong>Comp Off :</strong> &#8377;{{ number_format($payslip->comp_off, 2) }}</li>
                </ul>
            </td>
        </tr>
    </table>

    <!-- Total & Payable -->
    <table class="mid_hd">
        <tr>
            <td width="50%" align="center">
                <strong>Total Earnings :</strong>
                &#8377;{{ number_format($payslip->total_earnings, 2) }}
            </td>
            <td width="50%" align="center">
                <strong>Salary Payable :</strong>
                &#8377;{{ number_format($payslip->salary_payable, 2) }}
            </td>
        </tr>
    </table>

    <!-- Footer Note -->
    <p class="note_payslip">
        Note: THIS IS A COMPUTER-GENERATED DOCUMENT AND IT DOES NOT REQUIRE A SIGNATURE.
    </p>

</body>
</html>
