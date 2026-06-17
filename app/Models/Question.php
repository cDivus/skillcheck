<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'Questions';
    protected $primaryKey = 'question_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'exam_id',
        'order_index',
        'question_text',
        'image_url',
        'type',
        'time_limit_s',
        'marks',
        'is_locked',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function options()
    {
        return $this->hasMany(Option::class, 'question_id', 'question_id')->orderBy('order_index');
    }

    protected static function booted()
    {
        static::deleting(function ($question) {
            if ($question->image_url && !filter_var($question->image_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($question->image_url);
            }
        });
    }
}
