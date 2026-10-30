<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'floor',
        'type',
        'price',
        'status',
        'facilities',
        'image',
        'images',
        'custom_interior_request',
        'custom_interior_budget',
        'custom_interior_status',
        'custom_interior_admin_notes',
        'booked_by_user_id',
        'booked_by_name',
        'booked_by_phone',
    ];

    protected $casts = [
        'facilities' => 'array',
        'images' => 'array',
        'floor' => 'integer',
        'price' => 'decimal:2',
        'custom_interior_budget' => 'decimal:2',
    ];

    protected $appends = [
        'images_list',
    ];

    /**
     * Get 3 slide images for this room.
     */
    public function getImagesListAttribute(): array
    {
        $imgs = $this->images;
        if (!empty($imgs) && is_array($imgs)) {
            $filtered = array_values(array_filter($imgs));
            if (!empty($filtered)) {
                while (count($filtered) < 3) {
                    $filtered[] = $filtered[0] ?? 'assets/images/room_sample.jpg';
                }
                return array_slice($filtered, 0, 3);
            }
        }
        $main = $this->image ?: 'assets/images/room_sample.jpg';
        return [
            $main,
            'assets/images/rooms/room_2.jpg',
            'assets/images/rooms/room_3.jpg',
        ];
    }

    public function bookedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by_user_id');
    }

    public function activeTenant(): HasOne
    {
        return $this->hasOne(Tenant::class)->where('status', 'active');
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function finances(): HasMany
    {
        return $this->hasMany(Finance::class);
    }

    public function maintenanceTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class);
    }
}
