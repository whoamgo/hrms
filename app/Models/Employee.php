<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasEncryptedRouteKey;

class Employee extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'employee_id',
        'user_id',
        'employee_type',
        'full_name',
        'father_mother_name',
        'dob',
        'gender',
        'mobile_number',
        'email',
        'address',
        'bank_account_number',
        'bank_name',
        'account_holder_name',
        'bank_branch_name',
        'ifsc_code',
        'pan_card_number',
        'department',
        'department_id',
        'designation',
        'designation_id',
        'date_of_joining',
        'employment_status',
        'contract_start_date',
        'contract_end_date',
        'appointment_letter',
        'id_proof',
        'status',
    ];

    protected $casts = [
        'dob' => 'date',
        'date_of_joining' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
    ];

    /**
     * Get the user that owns the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that owns the employee.
     */
    public function departmentRelation()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    /**
     * Get the designation that owns the employee.
     */
    public function designationRelation()
    {
        return $this->belongsTo(\App\Models\Designation::class, 'designation_id');
    }

    /**
     * Generate unique employee ID
     */
    public static function generateEmployeeId()
    {
        $prefix = 'EMP';
        $lastEmployee = self::orderBy('id', 'desc')->first();
        
        if ($lastEmployee) {
            $lastNumber = intval(substr($lastEmployee->employee_id, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the service records for the employee.
     */
    public function serviceRecords()
    {
        return $this->hasMany(\App\Models\ServiceRecord::class);
    }

    /**
     * Get the leaves for the employee.
     */
    public function leaves()
    {
        return $this->hasMany(\App\Models\Leave::class);
    }

    /**
     * Get the attendances for the employee.
     */
    public function attendances()
    {
        return $this->hasMany(\App\Models\Attendance::class);
    }

    /**
     * Get the TA/DA claims for the employee.
     */
    public function tadaClaims()
    {
        return $this->hasMany(\App\Models\TadaClaim::class);
    }

    /**
     * Get the payslips for the employee.
     */
    public function payslips()
    {
        return $this->hasMany(\App\Models\Payslip::class);
    }
}
