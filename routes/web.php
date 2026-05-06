<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\AiExplanationController;
use App\Http\Controllers\AiQuestionGeneratorController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [QuizController::class, 'dashboard'])->name('dashboard');

    Route::resource('quizzes', QuizController::class);

    Route::get('/quizzes/{quiz}/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/quizzes/{quiz}/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    Route::get('/quizzes/{quiz}/start', [QuizAttemptController::class, 'start'])->name('attempts.start');
    Route::post('/attempts/{attempt}/submit', [QuizAttemptController::class, 'submit'])->name('attempts.submit');
    Route::get('/attempts/{attempt}/result', [QuizAttemptController::class, 'result'])->name('attempts.result');

    Route::get('/leaderboard/{quiz}', [LeaderboardController::class, 'show'])->name('leaderboard.show');

    Route::post('/attempts/{attempt}/ai-explanations', [AiExplanationController::class, 'generate'])
        ->name('attempts.ai.generate');

    Route::post('/quizzes/{quiz}/ai-generate-questions', [AiQuestionGeneratorController::class, 'generate'])
        ->name('quiz.ai.generate.questions');
});

require __DIR__.'/auth.php';