<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'branch_assignment',
        'google_id',
        'google_token',
        'google_avatar',
        'room_id',
        'branch_preference',
        'room_type_preference',
        'phone',
        'id_card_photo',
        'avatar',
        'birth_date',
        'origin_address',
        'is_profile_completed',
        'password_otp',
        'password_otp_expires_at',
        'password_otp_attempts',
        'password_otp_sent_at',
        'password_otp_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_otp_expires_at' => 'datetime',
            'password_otp_sent_at' => 'datetime',
            'password_otp_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaffOrOwner(): bool
    {
        return in_array($this->role, ['owner', 'admin']);
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    /**
     * Inisial 2 huruf user:
     * - Huruf pertama: huruf ke-1 dari kata ke-1 (kapital).
     * - Huruf kedua: huruf ke-2 dari kata ke-2 (kapital).
     * - Edge cases: jika kata ke-2 tidak ada atau panjang < 2, gunakan huruf ke-2 kata ke-1
     *   (atau huruf ke-1 kata ke-2 jika hanya ada 1 huruf).
     */
    public function getInitialsAttribute(): string
    {
        $rawName = trim((string)($this->name ?? 'Admin'));
        $words = array_values(array_filter(preg_split('/\s+/', $rawName !== '' ? $rawName : 'Admin')));

        if (empty($words)) {
            return 'AD';
        }

        $firstWord = $words[0];
        $firstChar = mb_strtoupper(mb_substr($firstWord, 0, 1));

        if (isset($words[1])) {
            $secondWord = $words[1];
            if (mb_strlen($secondWord) >= 2) {
                $secondChar = mb_strtoupper(mb_substr($secondWord, 1, 1));
            } else {
                $secondChar = mb_strtoupper(mb_substr($secondWord, 0, 1));
            }
        } elseif (mb_strlen($firstWord) >= 2) {
            $secondChar = mb_strtoupper(mb_substr($firstWord, 1, 1));
        } else {
            $secondChar = $firstChar;
        }

        return $firstChar . $secondChar;
    }
}
