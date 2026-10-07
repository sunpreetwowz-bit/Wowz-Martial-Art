<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->string('verification_code', 64)->unique();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('belt_test_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('belt_test_result_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('belt_id')->constrained('belts')->restrictOnDelete();
            $table->string('student_name_snapshot');
            $table->string('student_code_snapshot');
            $table->string('belt_name_snapshot');
            $table->date('test_date')->nullable();
            $table->date('issued_on');
            $table->string('authorized_by_name')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status', 20)->default('issued')->index();
            $table->timestamp('revoked_at')->nullable();
            $table->text('revoke_reason')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
