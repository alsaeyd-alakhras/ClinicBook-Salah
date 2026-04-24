<?php

namespace App\Observers;

use App\Models\ClinicDayConfig;
use App\Services\ActivityLogService;

class ClinicDayConfigObserver
{
    public function created(ClinicDayConfig $clinicDayConfig): void
    {
        ActivityLogService::log('Created', 'ClinicDayConfig', 'تم إضافة إعداد يوم عيادة.', null, $clinicDayConfig->toArray());
    }

    public function updated(ClinicDayConfig $clinicDayConfig): void
    {
        ActivityLogService::log('Updated', 'ClinicDayConfig', 'تم تعديل إعداد يوم عيادة.', $clinicDayConfig->getOriginal(), $clinicDayConfig->getChanges());
    }

    public function deleted(ClinicDayConfig $clinicDayConfig): void
    {
        ActivityLogService::log('Deleted', 'ClinicDayConfig', 'تم حذف إعداد يوم عيادة.', $clinicDayConfig->toArray(), null);
    }
}
