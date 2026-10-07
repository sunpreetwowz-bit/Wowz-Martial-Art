<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->restrictOnDelete();
            $table->string('student_code')->unique();
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->date('joining_date')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->foreignId('current_belt_id')->nullable()->constrained('belts')->nullOnDelete();
            $table->foreignId('primary_service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('status', 20)->default('active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'current_belt_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
