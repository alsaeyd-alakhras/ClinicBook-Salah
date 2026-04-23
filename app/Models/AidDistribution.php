<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AidDistribution extends Model
{
    protected $fillable = [
        'family_id',
        'office_id',
        'institution_id',
        'project_id',
        'aid_mode',
        'aid_item_id',
        'quantity',
        'cash_amount',
        'distributed_at',
        'created_by',
        'status',
        'cancelled_at',
        'cancelled_by',
        'notes',
    ];

    protected $casts = [
        'cash_amount'    => 'decimal:2',
        'quantity'       => 'decimal:2',
        'distributed_at' => 'datetime',
        'cancelled_at'   => 'datetime',
    ];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function aidItem(): BelongsTo
    {
        return $this->belongsTo(AidItem::class, 'aid_item_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeOfficeEmployee($query)
    {
        $user = Auth::user();
        if($user && $user->user_type == 'employee') {
            return $query->where('office_id', $user?->office_id);
        }
        return $query;
    }
}
