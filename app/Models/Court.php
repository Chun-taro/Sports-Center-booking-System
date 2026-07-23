<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'name',
        'capacity',
        'hourly_rate_override',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'hourly_rate_override' => 'decimal:2',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getEffectiveRateAttribute(): float
    {
        return $this->hourly_rate_override ?? $this->facility->hourly_rate;
    }

    public function isAvailableOnSlot(string $date, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $query = $this->bookings()
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'approved', 'checked_in'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($sub) use ($startTime, $endTime) {
                    $sub->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }
}
