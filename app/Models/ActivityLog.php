<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;


    // تعطيل Timestamps
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'ip_request',
        'ip_address',
        'event_type',
        'model_name',
        'message',
        'old_data',
        'new_data',
        'created_at',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
