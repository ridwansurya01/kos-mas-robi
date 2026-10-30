<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'room_id',
        'date',
        'type',
        'category',
        'amount',
        'description',
        'payment_method',
        'timezone',
        'cash_date',
        'proof_image',
    ];

    protected $casts = [
        'date' => 'date',
        'cash_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_payment_time',
        'formatted_payment_full',
    ];

    /**
     * Get Indonesian timezone formatted payment time (WIB / WITA / WIT)
     */
    public function getFormattedPaymentTimeAttribute(): string
    {
        $tz = strtoupper($this->timezone ?: 'WIB');
        $ianaTz = match($tz) {
            'WITA' => 'Asia/Makassar',
            'WIT' => 'Asia/Jayapura',
            default => 'Asia/Jakarta',
        };

        $timeObj = $this->created_at ? $this->created_at->copy()->setTimezone($ianaTz) : now()->setTimezone($ianaTz);
        return $timeObj->format('H:i:s') . ' ' . $tz;
    }

    /**
     * Get full date, time, and Indonesian timezone
     */
    public function getFormattedPaymentFullAttribute(): string
    {
        $tz = strtoupper($this->timezone ?: 'WIB');
        $ianaTz = match($tz) {
            'WITA' => 'Asia/Makassar',
            'WIT' => 'Asia/Jayapura',
            default => 'Asia/Jakarta',
        };

        $timeObj = $this->created_at ? $this->created_at->copy()->setTimezone($ianaTz) : now()->setTimezone($ianaTz);
        return $timeObj->translatedFormat('d F Y, H:i:s') . ' ' . $tz;
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
