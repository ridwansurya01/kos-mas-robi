<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'room_id',
        'reporter_name',
        'issue_description',
        'report_date',
        'priority',
        'status',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
