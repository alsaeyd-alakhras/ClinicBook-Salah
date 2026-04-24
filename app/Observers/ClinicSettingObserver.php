<?php

namespace App\Observers;

use App\Models\ClinicSetting;
use App\Services\ActivityLogService;

class ClinicSettingObserver
{
    public function created(ClinicSetting $clinicSetting): void
    {
        ActivityLogService::log('Created', 'ClinicSetting', 'تم إنشاء إعداد عيادة جديد.', null, $clinicSetting->toArray());
    }

    public function updated(ClinicSetting $clinicSetting): void
    {
        ActivityLogService::log('Updated', 'ClinicSetting', 'تم تعديل إعدادات العيادة.', $clinicSetting->getOriginal(), $clinicSetting->getChanges());
    }

    public function deleted(ClinicSetting $clinicSetting): void
    {
        ActivityLogService::log('Deleted', 'ClinicSetting', 'تم حذف إعداد من إعدادات العيادة.', $clinicSetting->toArray(), null);
    }
}
