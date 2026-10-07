<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('black_belts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rank')->default('1st Dan');
            $table->date('promoted_on')->nullable();
            $table->string('branch')->nullable();
            $table->text('biography')->nullable();
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('black_belts');
    }
};
