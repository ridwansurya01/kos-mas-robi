<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        'phone',
        'id_card',
        'start_date',
        'due_day',
        'rent_amount',
        'emergency_contact',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_day' => 'integer',
        'rent_amount' => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
