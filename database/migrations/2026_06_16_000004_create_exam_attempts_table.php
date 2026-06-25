<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Exam_Attempts', function (Blueprint $table) {
            $table->uuid('attempt_id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('exam_id');
            $table->uuid('student_id');
            $table->timestamp('start_time')->useCurrent();
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');

            $table->foreign('exam_id')->references('exam_id')->on('Exams')->onDelete('cascade');
            $table->foreign('student_id')->references('user_id')->on('Users')->onDelete('cascade');
        });

        // Add check constraints (SQLite cannot ALTER TABLE ADD CONSTRAINT; skip there)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE Exam_Attempts ADD CONSTRAINT chk_attempt_times CHECK (end_time >= start_time)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Exam_Attempts');
    }
};
