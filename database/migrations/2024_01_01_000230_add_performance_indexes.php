<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_forms', function (Blueprint $table) {
            $table->index(['status', 'deadline_at'], 'competition_forms_status_deadline_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['student_id', 'status'], 'payments_student_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('competition_forms', function (Blueprint $table) {
            $table->dropIndex('competition_forms_status_deadline_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_student_status_index');
        });
    }
};
