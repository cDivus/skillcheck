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
        Schema::create('Exams', function (Blueprint $table) {
            $table->uuid('exam_id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('instructor_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('duration_s');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('instructor_id')->references('user_id')->on('Users')->onDelete('cascade');
        });

        // Add check constraints (SQLite cannot ALTER TABLE ADD CONSTRAINT; skip there)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE Exams ADD CONSTRAINT chk_exam_times CHECK (end_time > start_time)');
            DB::statement('ALTER TABLE Exams ADD CONSTRAINT chk_duration CHECK (duration_s > 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Exams');
    }
};
