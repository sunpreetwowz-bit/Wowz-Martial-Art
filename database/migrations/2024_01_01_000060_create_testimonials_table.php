<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('author_name');
            $table->string('author_title')->nullable();
            $table->text('content');
            $table->string('photo_path')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->string('status', 20)->default('active')->index();
            $table->boolean('is_approved')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
