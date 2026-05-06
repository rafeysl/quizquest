<?php

use App\Http\Controllers\QuizController;

Route::get('/quizzes', [QuizController::class, 'index']);
Route::get('/quiz', function () {
    return view('quiz_view');
});
Route::get('/admin/dashboard', function () {
    return view('admin_dashboard');
})->name('admin.dashboard');

Route::get('/student/quiz', function () {
    return view('student_quiz');
})->name('student.quiz');