<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ClinicSetting;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService) {}

    public function show()
    {
        $clinicName = ClinicSetting::getValue('clinic_name', 'عيادة العيون - د.عمر أبو عمارة');
        $clinicLogo = ClinicSetting::getValue('clinic_logo');

        return view('booking.index', compact('clinicName', 'clinicLogo'));
    }

    public function status(Request $request): JsonResponse
    {
        $fingerprint = (string) $request->header('X-Device-Fingerprint', '');

        return response()->json($this->bookingService->getStatusPayload($fingerprint ?: null));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_name' => ['required', 'string', 'max:100'],
            'national_id' => ['required', 'regex:/^\d{9,10}$/'],
            'phone' => ['required', 'regex:/^05\d{8}$/'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'visit_type' => ['required', 'in:strabismus,other'],
            'fingerprint' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $booking = $this->bookingService->createBooking([
                ...$validated,
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => sprintf(
                    'تم تسجيل حجزك ليوم %s. يرجى الحضور من الساعة 8 صباحاً لاستلام دور الفحص. ملاحظة: لا يوجد أرقام أدوار، الفحص حسب ترتيب الحضور.',
                    $this->bookingService->formatArabicDate(Carbon::parse($booking->booking_date))
                ),
                'booking' => [
                    'id' => $booking->id,
                    'booking_date' => optional($booking->booking_date)->toDateString(),
                    'patient_name' => $booking->patient_name,
                    'visit_type' => $booking->visit_type,
                    'visit_type_label' => $this->bookingService->visitTypeLabel($booking->visit_type),
                    'created_at' => optional($booking->created_at)->toISOString(),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'field' => $this->inferFieldFromError($e->getMessage()),
            ], $e->getCode() >= 400 ? $e->getCode() : 422);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'هذه الهوية محجوزة مسبقاً لهذا اليوم.',
                'field' => 'national_id',
            ], 409);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ غير متوقع أثناء التسجيل. حاول مرة أخرى.',
            ], 500);
        }
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $fingerprint = (string) $request->header('X-Device-Fingerprint', '');
        if ($fingerprint === '') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تنفيذ الإجراء بدون تعريف الجهاز.',
            ], 422);
        }

        $booking = Booking::query()->findOrFail($id);

        if ($booking->device_fingerprint !== $fingerprint) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إلغاء هذا الحجز من هذا الجهاز.',
            ], 403);
        }

        $closeHour = (int) \App\Models\ClinicSetting::getValue('booking_close_hour', 7);
        $cutoff = Carbon::parse($booking->booking_date)->setTime($closeHour, 0);
        if (now()->greaterThanOrEqualTo($cutoff)) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إلغاء الحجز بعد انطلاق العيادة.',
            ], 422);
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح. المكان أصبح متاحاً.',
        ]);
    }

    private function inferFieldFromError(string $message): ?string
    {
        if (str_contains($message, 'هوية')) {
            return 'national_id';
        }
        if (str_contains($message, 'الجوال')) {
            return 'phone';
        }
        if (str_contains($message, 'حالات')) {
            return 'visit_type';
        }

        return null;
    }
}
