<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasEncryptedRouteKey;

class Leave extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'employee_id',
        'leave_type',
        'day_type',
        'from_date',
        'to_date',
        'total_days',
        'reason',
        'subject',
        'message',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the employee that owns the leave.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who approved the leave.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    /**
     * Calculate total days between from_date and to_date
     */
    public function calculateTotalDays()
    {
        if ($this->from_date && $this->to_date) {
            return $this->from_date->diffInDays($this->to_date) + 1;
        }
        return 0;
    }
}
