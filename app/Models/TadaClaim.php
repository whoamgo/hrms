<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasEncryptedRouteKey;

class TadaClaim extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'employee_id',
        'travel_date',
        'purpose',
        'distance',
        'amount_claimed',
        'bill_file',
        'bill_files',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'amount_claimed' => 'decimal:2',
        'approved_at' => 'datetime',
        'bill_files' => 'array',
    ];


    /**
     * Get the employee that owns the TA/DA claim.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who approved the claim.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
