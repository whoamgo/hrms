<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasEncryptedRouteKey;

class PaymentDisbursement extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'employee_id',
        'role',
        'amount',
        'transaction_id',
        'month',
        'year',
        'disbursement_status',
        'created_by',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'year' => 'integer',
    ];

    /**
     * Get the employee that owns the payment disbursement.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who created the disbursement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
