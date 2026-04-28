<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('visit_type', 20)->default('other')->after('age');
            $table->index(['booking_date', 'visit_type']);
        });

        Schema::table('clinic_day_configs', function (Blueprint $table) {
            $table->unsignedInteger('strabismus_capacity')->nullable()->after('capacity');
            $table->unsignedInteger('other_capacity')->nullable()->after('strabismus_capacity');
            $table->boolean('is_strabismus_closed')->default(false)->after('is_closed');
            $table->boolean('is_other_closed')->default(false)->after('is_strabismus_closed');
            $table->string('strabismus_close_message', 255)->nullable()->after('close_message');
            $table->string('other_close_message', 255)->nullable()->after('strabismus_close_message');
        });
    }

    public function down(): void
    {
        Schema::table('clinic_day_configs', function (Blueprint $table) {
            $table->dropColumn([
                'strabismus_capacity',
                'other_capacity',
                'is_strabismus_closed',
                'is_other_closed',
                'strabismus_close_message',
                'other_close_message',
            ]);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['booking_date', 'visit_type']);
            $table->dropColumn('visit_type');
        });
    }
};
