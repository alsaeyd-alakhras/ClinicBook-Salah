<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->date('booking_date');
            $table->string('patient_name', 100);
            $table->string('national_id', 20);
            $table->string('phone', 20);
            $table->unsignedTinyInteger('age');
            $table->string('device_fingerprint', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedInteger('serial_number');
            $table->enum('status', ['pending', 'ticket_received'])->default('pending');
            $table->timestamps();

            $table->unique(['booking_date', 'national_id']);
            $table->index(['booking_date', 'phone']);
            $table->index(['booking_date', 'ip_address']);
            $table->index(['booking_date', 'device_fingerprint']);
            $table->index(['booking_date', 'serial_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
