<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamAttempt extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'Exam_Attempts';
    protected $primaryKey = 'attempt_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'exam_id',
        'student_id',
        'start_time',
        'end_time',
        'status',
        'question_order',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'question_order' => 'array',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class, 'attempt_id', 'attempt_id');
    }

    public function getTotalScoreAttribute()
    {
        return $this->answers()->sum('marks_awarded');
    }

    public function getMaxScoreAttribute()
    {
        return $this->exam ? $this->exam->questions()->sum('marks') : 0;
    }

    public function getStartedAtAttribute()
    {
        return $this->start_time;
    }

    public function getSubmittedAtAttribute()
    {
        return $this->end_time;
    }
}
