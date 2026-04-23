<?php

namespace App\Observers;

use App\Models\Constant;
use App\Services\ActivityLogService;

class ConstantObserver
{
    /**
     * Handle the Constant "created" event.
     */
    public function created(Constant $constant): void
    {
        ActivityLogService::log(
            'Created',
            'Constant',
            "تم إضافة ثابت.",
            null,
            $constant->toArray()
        );
    }

    /**
     * Handle the Constant "updated" event.
     */
    public function updated(Constant $constant): void
    {
        ActivityLogService::log(
            'Updated',
            'Constant',
            "تم تعديل ثابت.",
            $constant->getOriginal(),
            $constant->getChanges()
        );
    }

    /**
     * Handle the Constant "deleted" event.
     */
    public function deleted(Constant $constant): void
    {
        ActivityLogService::log(
            'Deleted',
            'Constant',
            "تم حذف ثابت.",
            $constant->toArray(),
            null
        );
    }

    /**
     * Handle the Constant "restored" event.
     */
    public function restored(Constant $constant): void
    {
        //
    }

    /**
     * Handle the Constant "force deleted" event.
     */
    public function forceDeleted(Constant $constant): void
    {
        //
    }
}
