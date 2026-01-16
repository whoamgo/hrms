<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasEncryptedRouteKey;

class ServiceRecord extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'employee_id',
        'from_date',
        'to_date',
        'designation',
        'department',
        'remarks',
        'status',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    /**
     * Get the employee that owns the service record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
