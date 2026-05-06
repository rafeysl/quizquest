<?php

namespace App\Http\Controllers;

use App\Models\Quizzes;
use App\Models\QuizAttempts;

class LeaderboardController extends Controller
{
    public function show(Quizzes $quiz)
    {
        $topAttempts = QuizAttempts::where('quiz_id', $quiz->id)
            ->where('status', 'completed')
            ->with('user')
            ->orderByDesc('score')
            ->take(10)
            ->get();

        return view('leaderboard.show', compact('quiz', 'topAttempts'));
    }
}