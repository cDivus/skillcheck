<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Exam extends Model
{
    use HasUuids;

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
