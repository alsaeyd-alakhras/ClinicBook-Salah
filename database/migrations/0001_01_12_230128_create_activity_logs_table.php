<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('ip_request')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('event_type'); // نوع الحدث (إنشاء، تعديل، حذف)
            $table->string('model_name')->nullable(); // اسم النموذج (Model) الذي تم التعديل عليه
            $table->text('message'); // الرسالة الوصفية
            $table->json('old_data')->nullable(); // البيانات القديمة (في حالة التعديل)
            $table->json('new_data')->nullable(); // البيانات الجديدة
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
