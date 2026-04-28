<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicDayConfig extends Model
{
    protected $fillable = [
        'day_of_week',
        'specific_date',
        'capacity',
        'strabismus_capacity',
        'other_capacity',
        'is_closed',
        'is_strabismus_closed',
        'is_other_closed',
        'close_message',
        'strabismus_close_message',
        'other_close_message',
    ];

    protected $casts = [
        'specific_date' => 'date',
        'is_closed' => 'boolean',
        'is_strabismus_closed' => 'boolean',
        'is_other_closed' => 'boolean',
    ];
}
