<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\AiExplanationController;
use App\Http\Controllers\AiQuestionGeneratorController;

// Rute utama langsung ke dashboard
Route::get('/', [QuizController::class, 'dashboard'])->name('home');

// Rute kuis yang bisa diakses PUBLIK (Tanpa Login)
// Ini supaya tombol "Mulai Quiz" kamu tidak memicu error login
Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [QuizController::class, 'dashboard'])->name('dashboard');

    // Gunakan 'except' karena index dan show sudah kita buat manual di atas
    Route::resource('quizzes', QuizController::class)->except(['index', 'show']);

    // Manajemen Pertanyaan
    Route::get('/quizzes/{quiz}/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/quizzes/{quiz}/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    // Jalannya Quiz (Attempt)
    Route::get('/quizzes/{quiz}/start', [QuizAttemptController::class, 'start'])->name('attempts.start');
    Route::post('/attempts/{attempt}/submit', [QuizAttemptController::class, 'submit'])->name('attempts.submit');
    Route::get('/attempts/{attempt}/result', [QuizAttemptController::class, 'result'])->name('attempts.result');

    // Leaderboard & AI Features
    Route::get('/leaderboard/{quiz}', [LeaderboardController::class, 'show'])->name('leaderboard.show');
    Route::post('/attempts/{attempt}/ai-explanations', [AiExplanationController::class, 'generate'])
        ->name('attempts.ai.generate');
    Route::post('/quizzes/{quiz}/ai-generate-questions', [AiQuestionGeneratorController::class, 'generate'])
        ->name('quiz.ai.generate.questions');
});

// Fitur Auth bawaan (Login/Register)
require __DIR__.'/auth.php';