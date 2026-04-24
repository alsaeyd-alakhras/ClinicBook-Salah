<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicDayConfig extends Model
{
    protected $fillable = [
        'day_of_week',
        'specific_date',
        'capacity',
        'is_closed',
        'close_message',
    ];

    protected $casts = [
        'specific_date' => 'date',
        'is_closed' => 'boolean',
    ];
}
