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
        Schema::create('Questions', function (Blueprint $table) {
            $table->uuid('question_id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('exam_id');
            $table->integer('order_index');
            $table->text('question_text');
            $table->string('image_url', 255)->nullable();
            $table->enum('type', ['multiple_choice', 'true_false', 'question_answer', 'essay']);
            $table->integer('time_limit_s')->nullable();
            $table->decimal('marks', 5, 2);

            $table->foreign('exam_id')->references('exam_id')->on('Exams')->onDelete('cascade');
            $table->unique(['exam_id', 'order_index']);
        });

        // Add check constraints (SQLite cannot ALTER TABLE ADD CONSTRAINT; skip there)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE Questions ADD CONSTRAINT chk_time_limit CHECK (time_limit_s > 0)');
            DB::statement('ALTER TABLE Questions ADD CONSTRAINT chk_marks CHECK (marks >= 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Questions');
    }
};
