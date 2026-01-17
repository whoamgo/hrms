<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ServiceRecordController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\AttendanceController;


// /amitgii/ 

// Route::get('/test-mail', function () {
//     try {
//         Mail::raw('SMTP Brevo test mail', function ($message) {
//             $message->to('amgo@mailinator.com')
//                     ->subject('SMTP Test');
//         });
//         return 'Mail sent! amgo@mailinator.com';
//     } catch (\Exception $e) {
//         return $e->getMessage();
//     }
// });


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Forgot Password Routes
Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('password.update');

// Redirect root to login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard route (redirects based on role)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if (!$user->role) {
            return redirect()->route('login');
        }
        
        $roleSlug = $user->role->slug;
        $routes = [
            'admin' => 'admin.dashboard',
            'hr' => 'hr.dashboard',
            'accounts' => 'accounts.dashboard',
            'employee' => 'employee.dashboard',
        ];
        
        return redirect()->route($routes[$roleSlug] ?? 'login');
    })->name('dashboard');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Role Management
        Route::resource('roles', RoleController::class);
        Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions');
        Route::post('roles/{role}/menu-items', [RoleController::class, 'updateMenuItems'])->name('roles.menu-items');
        
        // Permission Management
        Route::resource('permissions', PermissionController::class);
        
        // User Management
        Route::resource('users', UserController::class);
        Route::get('users-data', [UserController::class, 'getUsers'])->name('users.data');
        
        // Employee Management
        Route::resource('employees', EmployeeController::class);
        Route::get('employees-data', [EmployeeController::class, 'getEmployees'])->name('employees.data');
        Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
        Route::get('employees-autocomplete/employee-id', [EmployeeController::class, 'autocompleteEmployeeId'])->name('employees.autocomplete.employee-id');
        Route::get('employees-autocomplete/name', [EmployeeController::class, 'autocompleteName'])->name('employees.autocomplete.name');
        Route::get('employees-autocomplete/department', [EmployeeController::class, 'autocompleteDepartment'])->name('employees.autocomplete.department');
        
        // Service Records Management
        Route::resource('service-records', ServiceRecordController::class);
        Route::get('service-records-data', [ServiceRecordController::class, 'getServiceRecords'])->name('service-records.data');
        Route::get('service-records-autocomplete/designation', [ServiceRecordController::class, 'autocompleteDesignation'])->name('service-records.autocomplete.designation');
        Route::get('service-records-autocomplete/department', [ServiceRecordController::class, 'autocompleteDepartment'])->name('service-records.autocomplete.department');
        
        // Leave Management
        Route::resource('leaves', LeaveController::class);
        Route::get('leaves-data', [LeaveController::class, 'getLeaves'])->name('leaves.data');
        Route::post('leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
        Route::get('leaves-autocomplete/employee-name', [LeaveController::class, 'autocompleteEmployeeName'])->name('leaves.autocomplete.employee-name');
        
        // Attendance Management
        Route::resource('attendances', AttendanceController::class);
        Route::get('attendances-data', [AttendanceController::class, 'getAttendances'])->name('attendances.data');
        Route::get('attendances-autocomplete/employee-name', [AttendanceController::class, 'autocompleteEmployeeName'])->name('attendances.autocomplete.employee-name');
        
        // Department Management
        Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);
        Route::get('departments-data', [\App\Http\Controllers\Admin\DepartmentController::class, 'getDepartments'])->name('departments.data');
        Route::post('departments/{department}/toggle-status', [\App\Http\Controllers\Admin\DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
        
        // Designation Management
        Route::resource('designations', \App\Http\Controllers\Admin\DesignationController::class);
        Route::get('designations-data', [\App\Http\Controllers\Admin\DesignationController::class, 'getDesignations'])->name('designations.data');
        Route::post('designations/{designation}/toggle-status', [\App\Http\Controllers\Admin\DesignationController::class, 'toggleStatus'])->name('designations.toggle-status');
        
        // Payroll / Honorarium Management
        Route::get('/payroll', [\App\Http\Controllers\Admin\PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payroll-data', [\App\Http\Controllers\Admin\PayrollController::class, 'getPayrolls'])->name('payroll.data');
        Route::post('/payroll', [\App\Http\Controllers\Admin\PayrollController::class, 'store'])->name('payroll.store');
        Route::get('/payroll/{payslip}', [\App\Http\Controllers\Admin\PayrollController::class, 'show'])->name('payroll.show');
        Route::get('/payroll/{payslip}/edit', [\App\Http\Controllers\Admin\PayrollController::class, 'edit'])->name('payroll.edit');
        Route::put('/payroll/{payslip}', [\App\Http\Controllers\Admin\PayrollController::class, 'update'])->name('payroll.update');
        Route::delete('/payroll/{payslip}', [\App\Http\Controllers\Admin\PayrollController::class, 'destroy'])->name('payroll.destroy');
        Route::get('/payroll/{payslip}/pdf', [\App\Http\Controllers\Admin\PayrollController::class, 'generatePdf'])->name('payroll.pdf');
        
        // TA/DA Claim Management
        Route::get('/tada-claims', [\App\Http\Controllers\Admin\TadaClaimController::class, 'index'])->name('tada-claims.index');
        Route::get('/tada-claims-data', [\App\Http\Controllers\Admin\TadaClaimController::class, 'getTadaClaims'])->name('tada-claims.data');
        Route::get('/tada-claims/{tadaClaim}', [\App\Http\Controllers\Admin\TadaClaimController::class, 'show'])->name('tada-claims.show');
        Route::post('/tada-claims/{tadaClaim}/approve', [\App\Http\Controllers\Admin\TadaClaimController::class, 'approve'])->name('tada-claims.approve');
        Route::post('/tada-claims/{tadaClaim}/reject', [\App\Http\Controllers\Admin\TadaClaimController::class, 'reject'])->name('tada-claims.reject');
        
        // Reports
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [\App\Http\Controllers\Admin\ReportController::class, 'generate'])->name('reports.generate');
        Route::post('/reports/export-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::post('/reports/export-excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('reports.export-excel');
        
        // Settings Management
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        
        // Contract Renewal Management
        Route::get('/contract-renewals', [\App\Http\Controllers\Admin\ContractRenewalController::class, 'index'])->name('contract-renewals.index');
        Route::get('/contract-renewals-data', [\App\Http\Controllers\Admin\ContractRenewalController::class, 'getContractRenewals'])->name('contract-renewals.data');
        Route::get('/contract-renewals/{employee}', [\App\Http\Controllers\Admin\ContractRenewalController::class, 'show'])->name('contract-renewals.show');
        Route::post('/contract-renewals/{employee}/renew', [\App\Http\Controllers\Admin\ContractRenewalController::class, 'renew'])->name('contract-renewals.renew');
        Route::post('/contract-renewals/{employee}/close', [\App\Http\Controllers\Admin\ContractRenewalController::class, 'close'])->name('contract-renewals.close');
    });

    // HR Routes
    Route::prefix('hr')->name('hr.')->middleware('role:hr')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\HR\DashboardController::class, 'index'])->name('dashboard');
        
        // Employee Management
        Route::resource('employees', \App\Http\Controllers\HR\EmployeeController::class);
        Route::get('employees-data', [\App\Http\Controllers\HR\EmployeeController::class, 'getEmployees'])->name('employees.data');
        Route::post('employees/{employee}/toggle-status', [\App\Http\Controllers\HR\EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
        Route::get('employees-autocomplete/employee-id', [\App\Http\Controllers\HR\EmployeeController::class, 'autocompleteEmployeeId'])->name('employees.autocomplete.employee-id');
        Route::get('employees-autocomplete/name', [\App\Http\Controllers\HR\EmployeeController::class, 'autocompleteEmployeeName'])->name('employees.autocomplete.name');
        Route::get('employees-autocomplete/department', [\App\Http\Controllers\HR\EmployeeController::class, 'autocompleteDepartment'])->name('employees.autocomplete.department');
        
        // Attendance Management
        Route::resource('attendances', \App\Http\Controllers\HR\AttendanceController::class);
        Route::get('attendances-data', [\App\Http\Controllers\HR\AttendanceController::class, 'getAttendances'])->name('attendances.data');
        Route::get('attendances-autocomplete/employee-name', [\App\Http\Controllers\HR\AttendanceController::class, 'autocompleteEmployeeName'])->name('attendances.autocomplete.employee-name');
        
        // Leave Management
        Route::resource('leaves', \App\Http\Controllers\HR\LeaveController::class);
        Route::get('leaves-data', [\App\Http\Controllers\HR\LeaveController::class, 'getLeaves'])->name('leaves.data');
        Route::post('leaves/{leave}/approve', [\App\Http\Controllers\HR\LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{leave}/reject', [\App\Http\Controllers\HR\LeaveController::class, 'reject'])->name('leaves.reject');
        Route::get('leaves-autocomplete/employee-name', [\App\Http\Controllers\HR\LeaveController::class, 'autocompleteEmployeeName'])->name('leaves.autocomplete.employee-name');
        
        // Contract Renewal Management
        Route::get('/contract-renewals', [\App\Http\Controllers\HR\ContractRenewalController::class, 'index'])->name('contract-renewals.index');
        Route::get('/contract-renewals-data', [\App\Http\Controllers\HR\ContractRenewalController::class, 'getContractRenewals'])->name('contract-renewals.data');
        Route::get('/contract-renewals/{employee}', [\App\Http\Controllers\HR\ContractRenewalController::class, 'show'])->name('contract-renewals.show');
        Route::post('/contract-renewals/{employee}/renew', [\App\Http\Controllers\HR\ContractRenewalController::class, 'renew'])->name('contract-renewals.renew');
        Route::post('/contract-renewals/{employee}/close', [\App\Http\Controllers\HR\ContractRenewalController::class, 'close'])->name('contract-renewals.close');
        
        // Payroll Coordination
        Route::get('/payroll', [\App\Http\Controllers\HR\PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payroll-data', [\App\Http\Controllers\HR\PayrollController::class, 'getPayrolls'])->name('payroll.data');
        Route::get('/payroll/{payslip}', [\App\Http\Controllers\HR\PayrollController::class, 'show'])->name('payroll.show');
        Route::get('/payroll/{payslip}/pdf', [\App\Http\Controllers\HR\PayrollController::class, 'generatePdf'])->name('payroll.pdf');
        
        // Reports
        Route::get('/reports', [\App\Http\Controllers\HR\ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [\App\Http\Controllers\HR\ReportController::class, 'generate'])->name('reports.generate');
        Route::post('/reports/export-pdf', [\App\Http\Controllers\HR\ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::post('/reports/export-excel', [\App\Http\Controllers\HR\ReportController::class, 'exportExcel'])->name('reports.export-excel');
    });

    // Accounts Routes
    Route::prefix('accounts')->name('accounts.')->middleware('role:accounts')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Accounts\DashboardController::class, 'index'])->name('dashboard');
        
        // Monthly Payroll Processing
        Route::get('/payroll', [\App\Http\Controllers\Accounts\PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payroll-data', [\App\Http\Controllers\Accounts\PayrollController::class, 'getPayrolls'])->name('payroll.data');
        Route::get('/payroll/employees-by-role', [\App\Http\Controllers\Accounts\PayrollController::class, 'getEmployeesByRole'])->name('payroll.employees-by-role');
        Route::post('/payroll', [\App\Http\Controllers\Accounts\PayrollController::class, 'store'])->name('payroll.store');
        Route::get('/payroll/{payslip}', [\App\Http\Controllers\Accounts\PayrollController::class, 'show'])->name('payroll.show');
        Route::get('/payroll/{payslip}/pdf', [\App\Http\Controllers\Accounts\PayrollController::class, 'generatePdf'])->name('payroll.pdf');
        
        // Payment Disbursement Management
        Route::get('/payment', [\App\Http\Controllers\Accounts\PaymentDisbursementController::class, 'index'])->name('payment.index');
        Route::get('/payment-data', [\App\Http\Controllers\Accounts\PaymentDisbursementController::class, 'getDisbursements'])->name('payment.data');
        Route::get('/payment/employees-by-role', [\App\Http\Controllers\Accounts\PaymentDisbursementController::class, 'getEmployeesByRole'])->name('payment.employees-by-role');
        Route::get('/payment/{disbursement}', [\App\Http\Controllers\Accounts\PaymentDisbursementController::class, 'show'])->name('payment.show');
        Route::post('/payment', [\App\Http\Controllers\Accounts\PaymentDisbursementController::class, 'store'])->name('payment.store');
        
        // TA/DA Ledger Manager
        Route::get('/tada', [\App\Http\Controllers\Accounts\TadaLedgerController::class, 'index'])->name('tada.index');
        Route::get('/tada-data', [\App\Http\Controllers\Accounts\TadaLedgerController::class, 'getTadaClaims'])->name('tada.data');
        Route::get('/tada/{tadaClaim}', [\App\Http\Controllers\Accounts\TadaLedgerController::class, 'show'])->name('tada.show');
        Route::post('/tada/{tadaClaim}/approve', [\App\Http\Controllers\Accounts\TadaLedgerController::class, 'approve'])->name('tada.approve');
        Route::post('/tada/{tadaClaim}/reject', [\App\Http\Controllers\Accounts\TadaLedgerController::class, 'reject'])->name('tada.reject');
        
        // Reports
        Route::get('/reports', [\App\Http\Controllers\Accounts\ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [\App\Http\Controllers\Accounts\ReportController::class, 'generate'])->name('reports.generate');
        Route::post('/reports/export-pdf', [\App\Http\Controllers\Accounts\ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::post('/reports/export-excel', [\App\Http\Controllers\Accounts\ReportController::class, 'exportExcel'])->name('reports.export-excel');
    });

    // Employee Routes
    Route::prefix('employee')->name('employee.')->middleware('role:employee')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('dashboard');
        
        // Employee Leave Management
        Route::get('/leaves/create', [\App\Http\Controllers\Employee\LeaveController::class, 'create'])->name('leaves.create');
        Route::post('/leaves', [\App\Http\Controllers\Employee\LeaveController::class, 'store'])->name('leaves.store');
        Route::get('/leaves-data', [\App\Http\Controllers\Employee\LeaveController::class, 'getMyLeaves'])->name('leaves.data');
        
        // Employee Attendance
        Route::get('/attendance', [\App\Http\Controllers\Employee\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance-data', [\App\Http\Controllers\Employee\AttendanceController::class, 'getMyAttendance'])->name('attendance.data');
        
        // Employee TA/DA Claims
        Route::get('/tada-claims', [\App\Http\Controllers\Employee\TadaClaimController::class, 'index'])->name('tada-claims.index');
        Route::post('/tada-claims', [\App\Http\Controllers\Employee\TadaClaimController::class, 'store'])->name('tada-claims.store');
        Route::get('/tada-claims-data', [\App\Http\Controllers\Employee\TadaClaimController::class, 'getMyClaims'])->name('tada-claims.data');
        
        // Employee Payslips
        Route::get('/payslips', [\App\Http\Controllers\Employee\PayslipController::class, 'index'])->name('payslips.index');
        Route::get('/payslips-data', [\App\Http\Controllers\Employee\PayslipController::class, 'getMyPayslips'])->name('payslips.data');
        Route::get('/payslips/{id}', [\App\Http\Controllers\Employee\PayslipController::class, 'show'])->name('payslips.show');
        Route::get('/payslips/{id}/pdf', [\App\Http\Controllers\Employee\PayslipController::class, 'pdf'])->name('payslips.pdf');
    });

    // Notifications Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    // Profile Routes (available to all authenticated users)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    // Change Password Routes (available to all authenticated users)
    Route::get('/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'index'])->name('change-password.index');
    Route::post('/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'update'])->name('change-password.update');
});





Route::post('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return response()->json(['status' => 'success']);
})->name('clear.cache');