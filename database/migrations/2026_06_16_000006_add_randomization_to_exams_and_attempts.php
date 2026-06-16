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
            $table->boolean('randomize_questions')->default(false)->after('duration_s');
        });

        Schema::table('Exam_Attempts', function (Blueprint $table) {
            $table->json('question_order')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Exams', function (Blueprint $table) {
            $table->dropColumn('randomize_questions');
        });

        Schema::table('Exam_Attempts', function (Blueprint $table) {
            $table->dropColumn('question_order');
        });
    }
};
