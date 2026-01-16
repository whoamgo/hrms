<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasEncryptedRouteKey;

class Payslip extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'basic_salary',
        'hra',
        'conveyance_allowance',
        'medical_allowance',
        'special_allowance',
        'esi',
        'pf',
        'tds',
        'deduction_10_percent',
        'mobile_deduction',
        'comp_off',
        'total_earnings',
        'total_deductions',
        'salary_payable',
        'days_payable',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'hra' => 'decimal:2',
        'conveyance_allowance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'special_allowance' => 'decimal:2',
        'esi' => 'decimal:2',
        'pf' => 'decimal:2',
        'tds' => 'decimal:2',
        'deduction_10_percent' => 'decimal:2',
        'mobile_deduction' => 'decimal:2',
        'comp_off' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'salary_payable' => 'decimal:2',
        'days_payable' => 'decimal:2',
    ];

    /**
     * Get the employee that owns the payslip.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
