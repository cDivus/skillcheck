<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'Exams';
    protected $primaryKey = 'exam_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false; // Only has created_at default in DB

    protected $fillable = [
        'instructor_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'duration_s',
        'randomize_questions',
        'viewable_responses',
    ];

    protected $casts = [
        'randomize_questions' => 'boolean',
        'viewable_responses' => 'boolean',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id', 'user_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'exam_id', 'exam_id')->orderBy('order_index');
    }
}
