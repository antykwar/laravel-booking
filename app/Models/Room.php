<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isAvailable($startDate, $endDate): bool
    {
        return !$this->bookings()
            ->where(function ($query) use ($startDate, $endDate) {
                $this->addOverlapCondition($query, $startDate, $endDate);
            })
            ->exists();
    }

    public function scopeAvailableBetween($query, $startDate, $endDate)
    {
        return $query->whereDoesntHave('bookings', function($q) use ($startDate, $endDate) {
            $q->where(function($query) use ($startDate, $endDate) {
                $this->addOverlapCondition($query, $startDate, $endDate);
            });
        });
    }

    public function addOverlapCondition($query, $startDate, $endDate)
    {
        return $query->whereBetween('begin_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orWhere(function($q) use ($startDate, $endDate) {
                $q->where('begin_date', '<=', $startDate)
                    ->where('end_date', '>=', $endDate);
            });
    }
}
