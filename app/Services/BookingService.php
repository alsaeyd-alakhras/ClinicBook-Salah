<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ClinicDayConfig;
use App\Models\ClinicSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public const VISIT_TYPE_STRABISMUS = 'strabismus';
    public const VISIT_TYPE_OTHER = 'other';

    public function getActiveBookingDate(): ?Carbon
    {
        $now = now();

        foreach ($this->getUpcomingClinicDates(21) as $date) {
            if ($this->isWithinWindow($date, $now)) {
                return $date;
            }
        }

        return null;
    }

    public function isBookingWindowOpen(Carbon $date): bool
    {
        $dayContext = $this->getDayContext($date);
        if ($dayContext['is_closed']) {
            return false;
        }

        if (! $this->isWithinWindow($date, now())) {
            return false;
        }

        return collect($this->visitTypes())
            ->contains(fn (string $visitType) => $this->isVisitTypeAvailable($date, $visitType, $dayContext));
    }

    public function getRemainingSlots(Carbon $date, ?string $visitType = null): int
    {
        $dayContext = $this->getDayContext($date);

        if ($visitType) {
            $capacity = (int) ($dayContext['type_capacities'][$visitType] ?? 0);
            $booked = Booking::query()
                ->whereDate('booking_date', $date->toDateString())
                ->where('visit_type', $visitType)
                ->count();

            return max(0, $capacity - $booked);
        }

        $capacity = (int) ($dayContext['capacity'] ?? 0);
        $booked = Booking::query()->whereDate('booking_date', $date->toDateString())->count();

        return max(0, $capacity - $booked);
    }

    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $activeDate = $this->getActiveBookingDate();
            if (! $activeDate) {
                throw new \DomainException($this->getClosedMessage(), 422);
            }

            $date = $activeDate->copy()->startOfDay();
            $dayContext = $this->getDayContext($date);

            if ($dayContext['is_closed']) {
                throw new \DomainException($dayContext['close_message'] ?: $this->getClosedMessage(), 422);
            }

            if (! $this->isWithinWindow($date, now())) {
                throw new \DomainException($this->getClosedMessage(), 422);
            }

            $dateKey = $date->toDateString();
            $visitType = $this->normalizeVisitType($data['visit_type'] ?? null);

            if (Booking::query()->whereDate('booking_date', $dateKey)->where('national_id', $data['national_id'])->lockForUpdate()->exists()) {
                throw new \DomainException('هذه الهوية محجوزة مسبقاً لهذا اليوم.', 409);
            }

            if (Booking::query()->whereDate('booking_date', $dateKey)->where('phone', $data['phone'])->lockForUpdate()->count() >= 2) {
                throw new \DomainException('رقم الجوال وصل الحد الأعلى للحجز في هذا اليوم.', 429);
            }

            if ($this->checkDeviceLimit($data['phone'], $data['fingerprint'] ?? null, $data['ip_address'] ?? null, $date)) {
                throw new \DomainException('لقد سجلت الحد المسموح به من الحالات من هذا الجهاز. إذا كنت تريد تسجيل حالة إضافية، يرجى المحاولة من جهاز آخر.', 429);
            }

            if ($dayContext['type_closed'][$visitType] ?? false) {
                throw new \DomainException($this->getVisitTypeClosedMessage($visitType, $dayContext), 422);
            }

            $capacity = (int) ($dayContext['type_capacities'][$visitType] ?? 0);
            $currentCount = Booking::query()
                ->whereDate('booking_date', $dateKey)
                ->where('visit_type', $visitType)
                ->lockForUpdate()
                ->count();
            if ($currentCount >= $capacity) {
                throw new \DomainException('عذراً، امتلأت حالات '.$this->visitTypeLabel($visitType).' لهذا اليوم.', 409);
            }

            $serial = ((int) Booking::query()->whereDate('booking_date', $dateKey)->lockForUpdate()->max('serial_number')) + 1;

            return Booking::query()->create([
                'booking_date' => $dateKey,
                'patient_name' => $data['patient_name'],
                'national_id' => $data['national_id'],
                'phone' => $data['phone'],
                'age' => (int) $data['age'],
                'visit_type' => $visitType,
                'device_fingerprint' => $data['fingerprint'] ?? null,
                'ip_address' => $data['ip_address'] ?? null,
                'serial_number' => $serial,
                'status' => 'pending',
            ]);
        }, 3);
    }

    public function getClosedMessage(): string
    {
        $nextOpenAt = $this->getNextOpeningTime();
        if ($nextOpenAt) {
            return 'الحجز مغلق حالياً. يفتح التسجيل في '.$this->formatArabicDateTime($nextOpenAt).'.';
        }

        return 'الحجز مغلق حالياً. يرجى المحاولة لاحقاً.';
    }

    public function checkDeviceLimit(string $phone, ?string $fingerprint, ?string $ip, Carbon $date): bool
    {
        $dateKey = $date->toDateString();

        if ($fingerprint && Booking::query()->whereDate('booking_date', $dateKey)->where('device_fingerprint', $fingerprint)->count() >= 2) {
            return true;
        }

        if ($ip && Booking::query()->whereDate('booking_date', $dateKey)->where('ip_address', $ip)->count() >= 3) {
            return true;
        }

        return Booking::query()->whereDate('booking_date', $dateKey)->where('phone', $phone)->count() >= 2;
    }

    public function getStatusPayload(?string $fingerprint = null): array
    {
        $nextOpenAt = $this->getNextOpeningTime();

        $activeDate = $this->getActiveBookingDate();

        if (! $activeDate) {
            return [
                'booking_date' => null,
                'booking_date_ar' => null,
                'is_open' => false,
                'remaining' => 0,
                'total' => 0,
                'closed_message' => $this->getClosedMessage(),
                'next_open_at' => $nextOpenAt?->toISOString(),
                'next_opening_ar' => $nextOpenAt ? $this->formatArabicDateTime($nextOpenAt) : null,
                'my_bookings' => [],
            ];
        }

        $date = $activeDate->copy()->startOfDay();
        $dayContext = $this->getDayContext($date);
        $remaining = $this->getRemainingSlots($date);
        $isOpen = $this->isBookingWindowOpen($date);
        $visitTypes = $this->buildVisitTypeStatus($date, $dayContext);

        $closedMessage = null;
        if (! $isOpen) {
            if ($dayContext['is_closed']) {
                $closedMessage = $dayContext['close_message'] ?: 'الطبيب إعتذر هذا اليوم يرجى اعادة تسجيل حالاتك في الموعد المسموح به';
            } elseif (collect($visitTypes)->every(fn (array $type) => $type['is_closed'])) {
                $closedMessage = collect($visitTypes)
                    ->pluck('closed_message')
                    ->filter()
                    ->implode(' ');
            } elseif (collect($visitTypes)->every(fn (array $type) => $type['remaining'] <= 0 || $type['is_closed'])) {
                $closedMessage = 'عذراً، امتلأت حالات اليوم.';
            } else {
                $closedMessage = $this->getClosedMessage();
            }
        }

        $myBookings = collect();
        if ($fingerprint) {
            $myBookings = Booking::query()
                ->whereDate('booking_date', $date->toDateString())
                ->where('device_fingerprint', $fingerprint)
                ->orderBy('created_at')
                ->get(['id', 'patient_name', 'booking_date', 'visit_type', 'created_at']);
        }

        return [
            'booking_date' => $date->toDateString(),
            'booking_date_ar' => $this->formatArabicDate($date),
            'is_open' => $isOpen,
            'remaining' => $remaining,
            'total' => (int) $dayContext['capacity'],
            'visit_types' => $visitTypes,
            'closed_message' => $closedMessage,
            'next_open_at' => $nextOpenAt?->toISOString(),
            'next_opening_ar' => $nextOpenAt ? $this->formatArabicDateTime($nextOpenAt) : null,
            'my_bookings' => $myBookings->map(function (Booking $booking) {
                return [
                    'id' => $booking->id,
                    'patient_name' => $booking->patient_name,
                    'visit_type' => $booking->visit_type,
                    'visit_type_label' => $this->visitTypeLabel($booking->visit_type),
                    'booking_date' => optional($booking->booking_date)->toDateString(),
                    'created_at' => optional($booking->created_at)->toISOString(),
                ];
            })->values()->all(),
        ];
    }

    public function getCapacityForDate(Carbon $date): int
    {
        $dayContext = $this->getDayContext($date);

        return (int) ($dayContext['capacity'] ?? 0);
    }

    public function visitTypeLabel(?string $visitType): string
    {
        return $visitType === self::VISIT_TYPE_STRABISMUS ? 'حول' : 'أخرى';
    }

    public function formatArabicDate(Carbon $date): string
    {
        return sprintf(
            '%s %d/%d/%d',
            $this->arabicDayName($date->dayOfWeek),
            $date->day,
            $date->month,
            $date->year
        );
    }

    private function formatArabicDateTime(Carbon $date): string
    {
        $period = $date->hour >= 12 ? 'مساءً' : 'صباحاً';

        return sprintf(
            '%s %d/%d/%d الساعة %s %s',
            $this->arabicDayName($date->dayOfWeek),
            $date->day,
            $date->month,
            $date->year,
            $this->toArabicHourLabel($date->hour),
            $period
        );
    }

    private function getDayContext(Carbon $date): array
    {
        $defaultCapacity = (int) ClinicSetting::getValue('default_capacity', 65);
        $defaultStrabismusCapacity = (int) ClinicSetting::getValue('default_strabismus_capacity', 0);

        $specific = ClinicDayConfig::query()
            ->whereDate('specific_date', $date->toDateString())
            ->first();

        $weekly = ClinicDayConfig::query()
            ->whereNull('specific_date')
            ->where('day_of_week', $date->dayOfWeek)
            ->latest('id')
            ->first();

        $config = $specific ?: $weekly;
        $capacity = (int) ($config?->capacity ?: $defaultCapacity);
        $strabismusCapacity = $config?->strabismus_capacity;
        $otherCapacity = $config?->other_capacity;

        if ($strabismusCapacity === null && $otherCapacity === null) {
            $strabismusCapacity = min($capacity, $defaultStrabismusCapacity);
            $otherCapacity = max(0, $capacity - $strabismusCapacity);
        } elseif ($strabismusCapacity === null) {
            $strabismusCapacity = max(0, $capacity - (int) $otherCapacity);
        } elseif ($otherCapacity === null) {
            $otherCapacity = max(0, $capacity - (int) $strabismusCapacity);
        }

        $strabismusCapacity = max(0, (int) $strabismusCapacity);
        $otherCapacity = max(0, (int) $otherCapacity);

        return [
            'capacity' => $capacity,
            'type_capacities' => [
                self::VISIT_TYPE_STRABISMUS => $strabismusCapacity,
                self::VISIT_TYPE_OTHER => $otherCapacity,
            ],
            'is_closed' => (bool) ($config?->is_closed ?? false),
            'type_closed' => [
                self::VISIT_TYPE_STRABISMUS => (bool) ($config?->is_strabismus_closed ?? false),
                self::VISIT_TYPE_OTHER => (bool) ($config?->is_other_closed ?? false),
            ],
            'close_message' => $config?->close_message,
            'type_close_messages' => [
                self::VISIT_TYPE_STRABISMUS => $config?->strabismus_close_message,
                self::VISIT_TYPE_OTHER => $config?->other_close_message,
            ],
        ];
    }

    private function buildVisitTypeStatus(Carbon $date, array $dayContext): array
    {
        return collect($this->visitTypes())->mapWithKeys(function (string $visitType) use ($date, $dayContext) {
            $isClosed = (bool) ($dayContext['type_closed'][$visitType] ?? false);
            $remaining = $this->getRemainingSlots($date, $visitType);

            return [$visitType => [
                'value' => $visitType,
                'label' => $this->visitTypeLabel($visitType),
                'capacity' => (int) ($dayContext['type_capacities'][$visitType] ?? 0),
                'remaining' => $remaining,
                'is_closed' => $isClosed,
                'is_available' => ! $dayContext['is_closed'] && ! $isClosed && $remaining > 0,
                'closed_message' => $isClosed ? $this->getVisitTypeClosedMessage($visitType, $dayContext) : null,
            ]];
        })->all();
    }

    private function isVisitTypeAvailable(Carbon $date, string $visitType, array $dayContext): bool
    {
        return ! ($dayContext['type_closed'][$visitType] ?? false)
            && $this->getRemainingSlots($date, $visitType) > 0;
    }

    private function getVisitTypeClosedMessage(string $visitType, array $dayContext): string
    {
        return $dayContext['type_close_messages'][$visitType]
            ?: 'اعتذر الطبيب عن استقبال حالات '.$this->visitTypeLabel($visitType).' لهذا اليوم.';
    }

    private function normalizeVisitType(?string $visitType): string
    {
        return in_array($visitType, $this->visitTypes(), true) ? $visitType : self::VISIT_TYPE_OTHER;
    }

    private function visitTypes(): array
    {
        return [self::VISIT_TYPE_STRABISMUS, self::VISIT_TYPE_OTHER];
    }

    private function getUpcomingClinicDates(int $days = 21): Collection
    {
        $clinicDays = $this->getClinicDays();
        $start = now()->copy()->startOfDay();

        $dates = collect();
        for ($i = 0; $i <= $days; $i++) {
            $date = $start->copy()->addDays($i);
            if (in_array($date->dayOfWeek, $clinicDays, true)) {
                $dates->push($date);
            }
        }

        return $dates;
    }

    private function getClinicDays(): array
    {
        $days = ClinicSetting::getValue('clinic_days', [0, 3]);
        if (is_string($days)) {
            $decoded = json_decode($days, true);
            $days = is_array($decoded) ? $decoded : [0, 3];
        }

        if (! is_array($days) || empty($days)) {
            return [0, 3];
        }

        return collect($days)
            ->map(fn ($day) => (int) $day)
            ->filter(fn ($day) => $day >= 0 && $day <= 6)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function isWithinWindow(Carbon $date, Carbon $now): bool
    {
        $openAt = $this->getOpeningTime($date);
        $closeAt = $date->copy()->setTime($this->getCloseHour(), 0);

        return $now->greaterThanOrEqualTo($openAt) && $now->lessThan($closeAt);
    }

    private function getOpeningTime(Carbon $date): Carbon
    {
        $previousClinicDate = $this->getPreviousClinicDate($date);

        return $previousClinicDate->setTime($this->getOpenHour(), 0);
    }

    private function getNextOpeningTime(): ?Carbon
    {
        $now = now();

        foreach ($this->getUpcomingClinicDates(45) as $date) {
            $dayContext = $this->getDayContext($date);
            if ($dayContext['is_closed']) {
                continue;
            }

            $openingTime = $this->getOpeningTime($date);
            if ($openingTime->greaterThan($now)) {
                return $openingTime;
            }
        }

        return null;
    }

    private function getPreviousClinicDate(Carbon $date): Carbon
    {
        $clinicDays = $this->getClinicDays();

        for ($i = 1; $i <= 14; $i++) {
            $candidate = $date->copy()->subDays($i);
            if (in_array($candidate->dayOfWeek, $clinicDays, true)) {
                return $candidate;
            }
        }

        return $date->copy()->subDay();
    }

    private function getOpenHour(): int
    {
        return max(0, min(23, (int) ClinicSetting::getValue('booking_open_hour', 15)));
    }

    private function getCloseHour(): int
    {
        return max(0, min(23, (int) ClinicSetting::getValue('booking_close_hour', 7)));
    }

    private function arabicDayName(int $day): string
    {
        $map = [
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];

        return $map[$day] ?? '';
    }

    private function toArabicHourLabel(int $hour): string
    {
        $normalized = $hour % 12;
        if ($normalized === 0) {
            $normalized = 12;
        }

        return (string) $normalized;
    }
}
