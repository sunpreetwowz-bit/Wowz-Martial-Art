<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('belt_test_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('belt_test_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            // Snapshots at submission time (preserve history if test config changes later)
            $table->foreignId('current_belt_id')->nullable()->constrained('belts')->nullOnDelete();
            $table->foreignId('target_belt_id')->constrained('belts')->restrictOnDelete();
            $table->json('form_data')->nullable();
            $table->string('status', 40)->default('submitted')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Race-safe: one application per student per test
            $table->unique(['belt_test_id', 'student_id']);
            $table->index(['student_id', 'status']);
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('belt_test_applications');
    }
};
