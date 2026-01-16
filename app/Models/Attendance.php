<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'working_hours',
        'status',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the employee that owns the attendance.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Calculate working hours
     */
    public function calculateWorkingHours()
    {
        if ($this->check_in && $this->check_out) {
            try {
                // Parse time values (they're stored as time strings like "09:30:00")
                $checkIn = Carbon::createFromFormat('H:i:s', $this->check_in);
                $checkOut = Carbon::createFromFormat('H:i:s', $this->check_out);
                
                // If check_out is before check_in, assume it's next day
                if ($checkOut->lt($checkIn)) {
                    $checkOut->addDay();
                }
                
                $diff = $checkIn->diff($checkOut);
                
                $hours = $diff->h;
                $minutes = $diff->i;
                
                return $hours . ' Hrs:' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ' Min';
            } catch (\Exception $e) {
                // Fallback: try parsing as datetime if stored differently
                try {
                    $checkIn = Carbon::parse($this->check_in);
                    $checkOut = Carbon::parse($this->check_out);
                    $diff = $checkIn->diff($checkOut);
                    return $diff->h . ' Hrs:' . str_pad($diff->i, 2, '0', STR_PAD_LEFT) . ' Min';
                } catch (\Exception $e2) {
                    return '-';
                }
            }
        }
        return '-';
    }
}
