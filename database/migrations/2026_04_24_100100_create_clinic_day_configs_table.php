<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_day_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_of_week');
            $table->date('specific_date')->nullable();
            $table->unsignedInteger('capacity');
            $table->boolean('is_closed')->default(false);
            $table->string('close_message', 255)->nullable();
            $table->timestamps();

            $table->index(['day_of_week', 'specific_date']);
            $table->unique(['day_of_week', 'specific_date'], 'clinic_day_configs_unique_day_specific_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_day_configs');
    }
};
