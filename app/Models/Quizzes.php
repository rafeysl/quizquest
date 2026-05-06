<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quizzes extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'thumbnail',
        'category', 'difficulty', 'time_limit',
        'pass_score', 'is_published',
        'randomize_questions', 'show_result_immediately',
        'attempts_allowed'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'randomize_questions' => 'boolean',
        'show_result_immediately' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function questions()
    {
        return $this->hasMany(Questions::class)->orderBy('sort_order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempts::class);
    }
}