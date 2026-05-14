<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\ClinicDayConfig;
use App\Models\ClinicSetting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingDateSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 5, 13, 8));
        Model::withoutEvents(function () {
            ClinicSetting::setValue('clinic_days', [4, 5]);
            ClinicSetting::setValue('booking_search_days', 14);
            ClinicSetting::setValue('default_capacity', 2);
            ClinicSetting::setValue('default_strabismus_capacity', 1);
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_status_returns_available_booking_dates(): void
    {
        $response = $this->getJson('/booking/status');

        $response
            ->assertOk()
            ->assertJsonPath('booking_date', '2026-05-14')
            ->assertJsonPath('available_dates.0.date', '2026-05-14')
            ->assertJsonPath('available_dates.1.date', '2026-05-15')
            ->assertJsonPath('available_dates.0.visit_types.other.is_available', true);
    }

    public function test_booking_is_stored_on_selected_available_date(): void
    {
        $response = $this->postJson('/booking', $this->validPayload([
            'booking_date' => '2026-05-15',
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('booking.booking_date', '2026-05-15');

        $this->assertDatabaseHas('bookings', [
            'booking_date' => '2026-05-15 00:00:00',
            'national_id' => '123456789',
        ]);
    }

    public function test_booking_rejects_unavailable_selected_date(): void
    {
        Model::withoutEvents(fn () => ClinicSetting::setValue('clinic_days', [4]));

        $this->postJson('/booking', $this->validPayload([
            'booking_date' => '2026-05-15',
        ]))
            ->assertStatus(422)
            ->assertJsonPath('message', 'التاريخ المختار غير متاح للحجز. يرجى اختيار يوم آخر من الأيام المتاحة.');
    }

    public function test_booking_rejects_full_selected_date(): void
    {
        Model::withoutEvents(function () {
            ClinicSetting::setValue('default_capacity', 1);
            ClinicSetting::setValue('default_strabismus_capacity', 0);
        });

        Booking::query()->create([
            'booking_date' => '2026-05-14',
            'patient_name' => 'Existing Patient',
            'national_id' => '987654321',
            'phone' => '0599999999',
            'age' => 33,
            'visit_type' => 'other',
            'serial_number' => 1,
            'status' => 'pending',
        ]);

        $this->postJson('/booking', $this->validPayload([
            'booking_date' => '2026-05-14',
            'visit_type' => 'other',
        ]))->assertStatus(422);
    }

    public function test_booking_rejects_closed_visit_type_on_selected_date(): void
    {
        Model::withoutEvents(fn () => ClinicDayConfig::query()->create([
            'day_of_week' => 4,
            'specific_date' => '2026-05-14',
            'capacity' => 2,
            'strabismus_capacity' => 1,
            'other_capacity' => 1,
            'is_strabismus_closed' => true,
            'strabismus_close_message' => 'حالات الحول مغلقة لهذا اليوم.',
        ]));

        $this->postJson('/booking', $this->validPayload([
            'booking_date' => '2026-05-14',
            'visit_type' => 'strabismus',
        ]))
            ->assertStatus(422)
            ->assertJsonPath('message', 'حالات الحول مغلقة لهذا اليوم.');
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'patient_name' => 'Test Patient',
            'national_id' => '123456789',
            'phone' => '0512345678',
            'age' => 28,
            'visit_type' => 'other',
            'booking_date' => '2026-05-14',
            'fingerprint' => 'cb_test_device',
        ], $overrides);
    }
}
