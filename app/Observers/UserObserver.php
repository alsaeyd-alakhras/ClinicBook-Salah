<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ActivityLogService;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        ActivityLogService::log(
            'Created',
            'User',
            "تم إضافة المستخدم : {$user->name}.",
            null,
            $user->toArray()
        );
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        
        
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        ActivityLogService::log(
            'Deleted',
            'User',
            "تم حذف المستخدم : {$user->name}.",
            $user->toArray(),
            null
        );
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
