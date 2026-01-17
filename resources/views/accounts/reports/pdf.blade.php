<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportType }} - {{ date('Y-m-d') }}</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $reportType }}</h2>
        <p>Generated on: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        @if($reportType == 'Leave Report')
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee Name</th>
                    <th>Employee Type</th>
                    <th>Department</th>
                    <th>Leave Type</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Total Days</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['employee']['full_name'] ?? 'N/A' }}</td>
                    <td>{{ $item['employee']['employee_type'] ?? 'N/A' }}</td>
                    <td>{{ $item['employee']['department'] ?? 'N/A' }}</td>
                    <td>{{ $item['leave_type'] == 'CL' ? 'Casual Leave' : ($item['leave_type'] == 'SL' ? 'Sick Leave' : 'Special Leave') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item['from_date'])->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item['to_date'])->format('d/m/Y') }}</td>
                    <td>{{ $item['total_days'] }}</td>
                    <td>{{ $item['reason'] ?? 'N/A' }}</td>
                    <td>{{ ucfirst($item['status']) }}</td>
                </tr>
                @endforeach
            </tbody>
        @elseif($reportType == 'Employee List')
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Employee Type</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['employee_id'] }}</td>
                    <td>{{ $item['full_name'] }}</td>
                    <td>{{ $item['employee_type'] }}</td>
                    <td>{{ $item['department'] ?? 'N/A' }}</td>
                    <td>{{ $item['designation'] ?? 'N/A' }}</td>
                    <td>{{ ucfirst($item['status']) }}</td>
                </tr>
                @endforeach
            </tbody>
        @elseif($reportType == 'Payroll Report')
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee Name</th>
                    <th>Month</th>
                    <th>Basic Salary</th>
                    <th>Total Earnings</th>
                    <th>Total Deductions</th>
                    <th>Net Pay</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['employee']['full_name'] ?? 'N/A' }}</td>
                    <td>{{ $item['month'] }} {{ $item['year'] }}</td>
                    <td>₹{{ number_format($item['basic_salary'], 2) }}</td>
                    <td>₹{{ number_format($item['total_earnings'], 2) }}</td>
                    <td>₹{{ number_format($item['total_deductions'], 2) }}</td>
                    <td>₹{{ number_format($item['salary_payable'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        @endif
    </table>
</body>
</html>

