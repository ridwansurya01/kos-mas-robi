<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'job',
        'branch',
        'room',
        'survey_date',
        'survey_time',
        'survey_method',
        'message',
        'status',
        'is_read',
        'admin_notes',
    ];

    protected $casts = [
        'survey_date' => 'date',
        'is_read' => 'boolean',
    ];
}

