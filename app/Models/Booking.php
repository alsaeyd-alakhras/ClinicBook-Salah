<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_date',
        'patient_name',
        'national_id',
        'phone',
        'age',
        'device_fingerprint',
        'ip_address',
        'serial_number',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];
}
