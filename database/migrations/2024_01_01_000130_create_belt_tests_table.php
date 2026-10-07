<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('belt_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('target_belt_id')->constrained('belts')->restrictOnDelete();
            $table->date('test_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->timestamp('application_opens_at')->nullable();
            $table->timestamp('application_closes_at')->nullable(); // null => auto start - 30 min
            $table->decimal('fee_amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('INR');
            $table->text('instructions')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'test_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('belt_tests');
    }
};
