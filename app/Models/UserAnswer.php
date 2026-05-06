<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAnswers extends Model
{
    use HasFactory;

    protected $table = 'user_answers';

    protected $fillable = [
        'attempt_id', 'question_id',
        'option_id', 'essay_answer',
        'is_correct', 'points_earned'
    ];

    protected $casts = [
        'is_correct' => 'boolean'
    ];

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}