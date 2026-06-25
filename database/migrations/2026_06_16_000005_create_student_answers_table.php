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
        Schema::create('Student_Answers', function (Blueprint $table) {
            $table->uuid('answer_id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('attempt_id');
            $table->uuid('question_id');
            $table->uuid('selected_option')->nullable();
            $table->text('text_answer')->nullable();
            $table->decimal('marks_awarded', 5, 2)->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('attempt_id')->references('attempt_id')->on('Exam_Attempts')->onDelete('cascade');
            $table->foreign('question_id')->references('question_id')->on('Questions')->onDelete('cascade');
            $table->foreign('selected_option')->references('option_id')->on('Options')->onDelete('set null');
            $table->unique(['attempt_id', 'question_id']);
        });

        // Add check constraints (SQLite cannot ALTER TABLE ADD CONSTRAINT; skip there)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE Student_Answers ADD CONSTRAINT chk_marks_awarded CHECK (marks_awarded >= 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Student_Answers');
    }
};
