<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ClinicDayConfig;
use App\Models\ClinicSetting;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('view', ClinicSetting::class);

        $settings = [
            'clinic_name' => ClinicSetting::getValue('clinic_name', 'عيادة العيون - د.عمر أبو عمارة'),
            'clinic_logo' => ClinicSetting::getValue('clinic_logo', null),
            'default_capacity' => (int) ClinicSetting::getValue('default_capacity', 65),
            'default_strabismus_capacity' => (int) ClinicSetting::getValue('default_strabismus_capacity', 0),
            'default_other_capacity' => (int) ClinicSetting::getValue('default_other_capacity', (int) ClinicSetting::getValue('default_capacity', 65)),
            'booking_open_hour' => (int) ClinicSetting::getValue('booking_open_hour', 15),
            'booking_close_hour' => (int) ClinicSetting::getValue('booking_close_hour', 7),
            'clinic_days' => ClinicSetting::getValue('clinic_days', [0, 3]),
        ];

        $weeklyConfigs = ClinicDayConfig::query()
            ->whereNull('specific_date')
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        $exceptions = ClinicDayConfig::query()
            ->whereNotNull('specific_date')
            ->orderByDesc('specific_date')
            ->get();

        return view('dashboard.settings.index', compact('settings', 'weeklyConfigs', 'exceptions'));
    }

    public function update(Request $request)
    {
        $this->authorize('update', ClinicSetting::class);

        $validated = $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'clinic_logo' => ['nullable', 'image', 'max:2048'],
            'default_capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'default_strabismus_capacity' => ['required', 'integer', 'min:0', 'max:500'],
            'default_other_capacity' => ['required', 'integer', 'min:0', 'max:500'],
            'booking_open_hour' => ['required', 'integer', 'min:0', 'max:23'],
            'booking_close_hour' => ['required', 'integer', 'min:0', 'max:23'],
            'clinic_days' => ['required', 'array', 'min:1'],
            'clinic_days.*' => ['integer', 'between:0,6'],
            'day_capacities' => ['nullable', 'array'],
            'day_capacities.*' => ['nullable', 'integer', 'min:1', 'max:500'],
            'day_strabismus_capacities' => ['nullable', 'array'],
            'day_strabismus_capacities.*' => ['nullable', 'integer', 'min:0', 'max:500'],
            'day_other_capacities' => ['nullable', 'array'],
            'day_other_capacities.*' => ['nullable', 'integer', 'min:0', 'max:500'],
        ]);

        $clinicDays = collect($validated['clinic_days'])->map(fn ($d) => (int) $d)->unique()->sort()->values()->all();
        $defaultTypeCapacities = $this->normalizeTypeCapacities(
            (int) $validated['default_capacity'],
            (int) $validated['default_strabismus_capacity'],
            (int) $validated['default_other_capacity'],
            'default_capacity'
        );

        ClinicSetting::setValue('clinic_name', $validated['clinic_name']);
        ClinicSetting::setValue('default_capacity', (int) $validated['default_capacity']);
        ClinicSetting::setValue('default_strabismus_capacity', $defaultTypeCapacities['strabismus']);
        ClinicSetting::setValue('default_other_capacity', $defaultTypeCapacities['other']);
        ClinicSetting::setValue('booking_open_hour', (int) $validated['booking_open_hour']);
        ClinicSetting::setValue('booking_close_hour', (int) $validated['booking_close_hour']);
        ClinicSetting::setValue('clinic_days', $clinicDays);

        if ($request->hasFile('clinic_logo')) {
            $oldLogo = ClinicSetting::getValue('clinic_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('clinic_logo')->store('clinic', 'public');
            ClinicSetting::setValue('clinic_logo', $path);
        }

        $dayCapacities = $request->input('day_capacities', []);
        $dayStrabismusCapacities = $request->input('day_strabismus_capacities', []);
        $dayOtherCapacities = $request->input('day_other_capacities', []);
        foreach ($clinicDays as $day) {
            $capacity = (int) ($dayCapacities[$day] ?? $validated['default_capacity']);
            $typeCapacities = $this->normalizeTypeCapacities(
                $capacity,
                $dayStrabismusCapacities[$day] ?? $defaultTypeCapacities['strabismus'],
                $dayOtherCapacities[$day] ?? max(0, $capacity - (int) ($dayStrabismusCapacities[$day] ?? $defaultTypeCapacities['strabismus'])),
                'day_capacities.'.$day
            );

            ClinicDayConfig::query()->updateOrCreate(
                ['day_of_week' => $day, 'specific_date' => null],
                [
                    'capacity' => $capacity,
                    'strabismus_capacity' => $typeCapacities['strabismus'],
                    'other_capacity' => $typeCapacities['other'],
                    'is_closed' => false,
                    'is_strabismus_closed' => false,
                    'is_other_closed' => false,
                    'close_message' => null,
                    'strabismus_close_message' => null,
                    'other_close_message' => null,
                ]
            );
        }

        ClinicDayConfig::query()
            ->whereNull('specific_date')
            ->whereNotIn('day_of_week', $clinicDays)
            ->delete();

        return redirect()->route('dashboard.settings.index')->with('success', 'تم تحديث إعدادات العيادة بنجاح.');
    }

    public function updateDayConfig(Request $request)
    {
        $this->authorize('update', ClinicDayConfig::class);

        if ($request->filled('delete_id')) {
            ClinicDayConfig::query()->whereKey($request->delete_id)->whereNotNull('specific_date')->delete();

            return redirect()->route('dashboard.settings.index')->with('success', 'تم حذف الاستثناء بنجاح.');
        }

        $validated = $request->validate([
            'specific_date' => ['required', 'date'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:500'],
            'strabismus_capacity' => ['nullable', 'integer', 'min:0', 'max:500'],
            'other_capacity' => ['nullable', 'integer', 'min:0', 'max:500'],
            'is_closed' => ['nullable', 'boolean'],
            'is_strabismus_closed' => ['nullable', 'boolean'],
            'is_other_closed' => ['nullable', 'boolean'],
            'close_message' => ['nullable', 'string', 'max:255'],
            'strabismus_close_message' => ['nullable', 'string', 'max:255'],
            'other_close_message' => ['nullable', 'string', 'max:255'],
        ]);

        $date = Carbon::parse($validated['specific_date']);
        $defaultCapacity = (int) ClinicSetting::getValue('default_capacity', 65);
        $capacity = (int) ($validated['capacity'] ?? $defaultCapacity);
        $typeCapacities = $this->normalizeTypeCapacities(
            $capacity,
            $validated['strabismus_capacity'] ?? ClinicSetting::getValue('default_strabismus_capacity', 0),
            $validated['other_capacity'] ?? max(0, $capacity - (int) ($validated['strabismus_capacity'] ?? ClinicSetting::getValue('default_strabismus_capacity', 0))),
            'capacity'
        );

        ClinicDayConfig::query()->updateOrCreate(
            ['specific_date' => $date->toDateString()],
            [
                'day_of_week' => $date->dayOfWeek,
                'capacity' => $capacity,
                'strabismus_capacity' => $typeCapacities['strabismus'],
                'other_capacity' => $typeCapacities['other'],
                'is_closed' => (bool) ($validated['is_closed'] ?? false),
                'is_strabismus_closed' => (bool) ($validated['is_strabismus_closed'] ?? false),
                'is_other_closed' => (bool) ($validated['is_other_closed'] ?? false),
                'close_message' => $validated['close_message'] ?? null,
                'strabismus_close_message' => $validated['strabismus_close_message'] ?? null,
                'other_close_message' => $validated['other_close_message'] ?? null,
            ]
        );

        return redirect()->route('dashboard.settings.index')->with('success', 'تم حفظ إعداد اليوم المخصص بنجاح.');
    }

    private function normalizeTypeCapacities(int $total, mixed $strabismus, mixed $other, string $field): array
    {
        $strabismus = max(0, (int) $strabismus);
        $other = max(0, (int) $other);

        if (($strabismus + $other) !== $total) {
            throw ValidationException::withMessages([
                $field => 'مجموع حالات الحول والأخرى يجب أن يساوي إجمالي اليوم.',
            ]);
        }

        return [
            'strabismus' => $strabismus,
            'other' => $other,
        ];
    }
}
