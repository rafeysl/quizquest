<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuizController;

// URL-nya nanti: http://127.0.0.1:8000/api/quizzes
Route::get('/quizzes', [QuizController::class, 'index']);
// Ubah POST jadi GET supaya bisa diakses langsung di browser
Route::get('/quizzes/generate', [QuizController::class, 'generateWithAI']);