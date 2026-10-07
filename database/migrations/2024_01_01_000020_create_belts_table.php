<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('belts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 50)->nullable();
            $table->unsignedInteger('rank_order')->unique();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('belts');
    }
};
