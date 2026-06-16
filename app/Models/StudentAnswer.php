<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StudentAnswer extends Model
{
    use HasUuids;

    protected $table = 'Student_Answers';
    protected $primaryKey = 'answer_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // It has updated_at but not created_at, let's configure Eloquent custom timestamps if needed, or just set public $timestamps = false and handle updated_at manually, or set const UPDATED_AT = 'updated_at'; and const CREATED_AT = null;
    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option',
        'text_answer',
        'marks_awarded',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id', 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(Option::class, 'selected_option', 'option_id');
    }
}
