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
        if ($this->isBeforeInitialLaunch()) {
            return null;
        }

        return $this->getAvailableBookingDates()->first();
    }

    public function getAvailableBookingDates(): Collection
    {
        if ($this->isBeforeInitialLaunch()) {
            return collect();
        }

        return $this->getUpcomingClinicDates($this->getBookingSearchDays())
            ->filter(fn (Carbon $date) => $this->hasAvailableBookingSlot($date))
            ->values();
    }

    public function isBookingWindowOpen(Carbon $date): bool
    {
        $dayContext = $this->getDayContext($date);
        if ($dayContext['is_closed']) {
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
            $date = $this->resolveBookingDate($data['booking_date'] ?? null);
            if (! $date) {
                throw new \DomainException($this->getClosedMessage(), 422);
            }

            $dayContext = $this->getDayContext($date);

            if ($dayContext['is_closed']) {
                throw new \DomainException($dayContext['close_message'] ?: $this->getClosedMessage(), 422);
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
                throw new \DomainException('تم الوصول للحد الأعلى من التسجيلات من نفس الشبكة لهذا اليوم. يرجى المحاولة لاحقاً.', 429);
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
        if ($this->isBeforeInitialLaunch()) {
            return 'الحجز مغلق حالياً. يفتح التسجيل يوم الثلاثاء 12/5/2026 الساعة 7 صباحاً.';
        }

        return 'لا توجد مواعيد متاحة حالياً خلال مدة الحجز المحددة. يرجى المحاولة لاحقاً.';
    }

    public function checkDeviceLimit(string $phone, ?string $fingerprint, ?string $ip, Carbon $date): bool
    {
        $dateKey = $date->toDateString();

        if ($ip && Booking::query()->whereDate('booking_date', $dateKey)->where('ip_address', $ip)->count() >= 6) {
            return true;
        }

        return false;
    }

    public function getStatusPayload(?string $fingerprint = null, ?string $legacyFingerprint = null, array $localBookingIds = []): array
    {
        $activeDate = $this->getActiveBookingDate();
        $availableDates = $this->buildAvailableDatesPayload();

        if (! $activeDate) {
            return [
                'booking_date' => null,
                'booking_date_ar' => null,
                'is_open' => false,
                'remaining' => 0,
                'total' => 0,
                'closed_message' => $this->getClosedMessage(),
                'next_open_at' => null,
                'next_opening_ar' => null,
                'available_dates' => $availableDates,
                'my_bookings' => $this->getFutureDeviceBookings($fingerprint, $legacyFingerprint, $localBookingIds),
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

        $myBookings = $this->getFutureDeviceBookings($fingerprint, $legacyFingerprint, $localBookingIds);

        return [
            'booking_date' => $date->toDateString(),
            'booking_date_ar' => $this->formatArabicDate($date),
            'is_open' => $isOpen,
            'remaining' => $remaining,
            'total' => (int) $dayContext['capacity'],
            'visit_types' => $visitTypes,
            'closed_message' => $closedMessage,
            'next_open_at' => null,
            'next_opening_ar' => null,
            'available_dates' => $availableDates,
            'my_bookings' => $myBookings,
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

    private function buildAvailableDatesPayload(): array
    {
        return $this->getAvailableBookingDates()
            ->map(function (Carbon $date) {
                $dayContext = $this->getDayContext($date);

                return [
                    'date' => $date->toDateString(),
                    'date_ar' => $this->formatArabicDate($date),
                    'remaining' => $this->getRemainingSlots($date),
                    'total' => (int) $dayContext['capacity'],
                    'visit_types' => $this->buildVisitTypeStatus($date, $dayContext),
                ];
            })
            ->values()
            ->all();
    }

    private function resolveBookingDate(?string $bookingDate): ?Carbon
    {
        if (! $bookingDate) {
            return $this->getActiveBookingDate()?->copy()->startOfDay();
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $bookingDate)->startOfDay();
        } catch (\Throwable) {
            return null;
        }

        $isAvailable = $this->getAvailableBookingDates()
            ->contains(fn (Carbon $availableDate) => $availableDate->isSameDay($date));

        if (! $isAvailable) {
            throw new \DomainException('التاريخ المختار غير متاح للحجز. يرجى اختيار يوم آخر من الأيام المتاحة.', 422);
        }

        return $date;
    }

    private function isVisitTypeAvailable(Carbon $date, string $visitType, array $dayContext): bool
    {
        if ($dayContext['is_closed']) {
            return false;
        }

        return ! ($dayContext['type_closed'][$visitType] ?? false)
            && $this->getRemainingSlots($date, $visitType) > 0;
    }

    private function hasAvailableBookingSlot(Carbon $date): bool
    {
        $dayContext = $this->getDayContext($date);

        if ($dayContext['is_closed']) {
            return false;
        }

        return collect($this->visitTypes())
            ->contains(fn (string $visitType) => $this->isVisitTypeAvailable($date, $visitType, $dayContext));
    }

    private function getFutureDeviceBookings(?string $fingerprint, ?string $legacyFingerprint, array $localBookingIds): array
    {
        $startDate = now()->copy()->addDay()->toDateString();
        $endDate = now()->copy()->addDays($this->getBookingSearchDays())->toDateString();
        $myBookings = collect();

        if ($fingerprint) {
            $myBookings = Booking::query()
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->where('device_fingerprint', $fingerprint)
                ->orderBy('booking_date')
                ->orderBy('created_at')
                ->get(['id', 'patient_name', 'booking_date', 'visit_type', 'created_at']);
        }

        if ($legacyFingerprint && $localBookingIds) {
            $legacyBookings = Booking::query()
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->where('device_fingerprint', $legacyFingerprint)
                ->whereIn('id', $localBookingIds)
                ->orderBy('booking_date')
                ->orderBy('created_at')
                ->get(['id', 'patient_name', 'booking_date', 'visit_type', 'created_at']);

            $myBookings = $myBookings
                ->merge($legacyBookings)
                ->unique('id')
                ->sortBy([['booking_date', 'asc'], ['created_at', 'asc']])
                ->values();
        }

        return $myBookings->map(function (Booking $booking) {
            $bookingDate = Carbon::parse($booking->booking_date);

            return [
                'id' => $booking->id,
                'patient_name' => $booking->patient_name,
                'visit_type' => $booking->visit_type,
                'visit_type_label' => $this->visitTypeLabel($booking->visit_type),
                'booking_date' => $bookingDate->toDateString(),
                'booking_date_ar' => $this->formatArabicDate($bookingDate),
                'created_at' => optional($booking->created_at)->toISOString(),
            ];
        })->values()->all();
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
        $start = now()->copy()->addDay()->startOfDay();

        $dates = collect();
        for ($i = 0; $i < $days; $i++) {
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

    private function getBookingSearchDays(): int
    {
        return max(1, min(120, (int) ClinicSetting::getValue('booking_search_days', 60)));
    }

    private function isBeforeInitialLaunch(): bool
    {
        return now()->lessThan(Carbon::create(2026, 5, 12, 7, 0, 0));
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
}
