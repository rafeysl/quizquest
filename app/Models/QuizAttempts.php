<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizAttempts extends Model
{
    use HasFactory;

    protected $table = 'quiz_attempts';

    protected $fillable = [
        'quiz_id', 'user_id',
        'score', 'total_points',
        'correct_answers', 'wrong_answers', 'skipped_answers',
        'status',
        'started_at', 'finished_at',
        'time_taken'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quizzes::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(UserAnswers::class, 'attempt_id');
    }

    public function aiExplanations()
    {
        return $this->hasMany(AiExplanation::class, 'attempt_id');
    }
}