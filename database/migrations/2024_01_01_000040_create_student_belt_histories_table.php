<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_belt_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('from_belt_id')->nullable()->constrained('belts')->nullOnDelete();
            $table->foreignId('to_belt_id')->constrained('belts')->restrictOnDelete();
            $table->string('source', 40);
            $table->nullableMorphs('source_ref'); // e.g. belt_test_result
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_belt_histories');
    }
};
