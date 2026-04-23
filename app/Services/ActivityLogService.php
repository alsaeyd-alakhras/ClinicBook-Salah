<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    // Event Type: Created, Updated, Deleted, Login, Logout, Access Denied
    public static function log($eventType, $modelName, $message, $oldData = null, $newData = null,$user_id =null ,$user_name = null)
    {
        // الحصول على ip الجهاز
        $internalIp = session('internal_ip', 'IP not found');


        // حفظ التفاصيل في قاعدة البيانات
        ActivityLog::create([
            'user_id' => $user_id ?? Auth::user()?->id ?? 1,
            'user_name' => $user_name ?? Auth::user()?->name ?? 'Guest',
            'ip_request' => Request::ip(),
            'ip_address' => $internalIp,
            'event_type' => $eventType,
            'model_name' => $modelName,
            'message' => $message,
            'old_data' => json_encode($oldData),
            'new_data' => json_encode($newData),
            'created_at' => now(),
        ]);
    }
}
