<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('Exams', function (Blueprint $table) {
            $table->string('timer_type', 50)->default('whole_exam')->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Exams', function (Blueprint $table) {
            $table->dropColumn('timer_type');
        });
    }
};
