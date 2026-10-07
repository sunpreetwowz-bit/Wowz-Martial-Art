<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('belt_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('belt_test_application_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('belt_test_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->string('outcome', 20); // passed | failed
            $table->decimal('score', 8, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->date('result_date')->nullable();
            $table->boolean('belt_updated')->default(false);
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['belt_test_id', 'outcome']);
            $table->index(['student_id', 'outcome']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('belt_test_results');
    }
};
