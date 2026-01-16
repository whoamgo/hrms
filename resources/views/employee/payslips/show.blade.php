@extends('layouts.app')

@section('title', 'Salary Slip')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="button_print justify-content-end d-flex mb-3">
                <a href="{{ route('employee.payslips.index') }}" class="btn btn-primary mr-2">Back</a>
                <a href="{{ route('employee.payslips.pdf', $payslip->id) }}" class="btn btn-primary" target="_blank">Print</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6 m-auto">
        <div class="card-box">
            <div class="Salary_box">
                <div class="company_logo text-center mb-3">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-width: 100px;">
                </div>
                
                <div class="text-center mb-3">
                    <h5>राजस्थान सरकार</h5>
                    <p>सामाजिक लेखा परीक्षा, जवाबदेही एवं पारदर्शिता सोसायटी (SSAAT), राजस्थान, जयपुर</p>
                    <p><strong>राजकीय पारदर्शिता, सामाजिक उतरदायित्व</strong></p>
                </div>
                
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="Payslip_Item">
                            <ul>
                                <li><strong>Month </strong>{{ $payslip->month }}, {{ $payslip->year }}</li>
                                <li><strong>Employee Name </strong>{{ $employee->full_name }}</li>
                                <li><strong>Department </strong>{{ $employee->department ?? 'N/A' }}</li>
                                <li><strong>Location </strong>{{ $employee->address ? explode(',', $employee->address)[0] : 'Jaipur, Rajasthan' }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 d-flex justify-content-end">
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
                    <div class="col-lg-6 col-md-6">
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
                    <div class="col-lg-6 col-md-6 d-flex justify-content-end">
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
                        <div class="col-lg-6">
                            <div class="Payslip_Item text-center">
                                <ul>
                                    <li><strong>Total </strong>{{ number_format($payslip->total_earnings, 2) }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="Payslip_Item text-center">
                                <ul>
                                    <li><strong>Salary Payable </strong>{{ number_format($payslip->salary_payable, 2) }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="note_payslip">Note: THIS IS A COMPUTER-GENERATED DOCUMENT AND IT DOES NOT REQUIRE A SIGNATURE.</p>
            </div>
        </div>
    </div>
</div>
@endsection


