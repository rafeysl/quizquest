<?php

namespace App\Http\Controllers;

use App\Models\Quizzes;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\QuizStoreRequest;
use App\Http\Requests\QuizUpdateRequest;

class QuizController extends Controller
{
    public function dashboard()
{
    // Cara penulisan ternary yang benar (tanpa terputus titik koma di tengah)
    $myQuizzes = Auth::check() 
        ? Quizzes::where('user_id', Auth::id())->latest()->get() 
        : collect();

    $publishedQuizzes = Quizzes::where('is_published', true)
        ->latest()
        ->take(6)
        ->get();

    return view('dashboard', compact('myQuizzes', 'publishedQuizzes'));
}

    public function index()
    {
        $quizzes = Quizzes::where('user_id', Auth::id())->latest()->get();
        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('quizzes.create');
    }

    public function store(QuizStoreRequest $request)
    {
        $quiz = Quizzes::create([
            'user_id' => Auth::id(),
            ...$request->validated(),
            'is_published' => $request->boolean('is_published'),
            'randomize_questions' => $request->boolean('randomize_questions'),
            'show_result_immediately' => $request->boolean('show_result_immediately'),
        ]);

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Quiz berhasil dibuat.');
    }

    public function show(Quizzes $quiz)
    {
        if (!$quiz->is_published && $quiz->user_id !== Auth::id()) {
            abort(403);
        }

        $quiz->load('questions.options');

        return view('quizzes.show', compact('quiz'));
    }

    public function edit(Quizzes $quiz)
    {
        if ($quiz->user_id !== Auth::id()) {
            abort(403);
        }

        return view('quizzes.edit', compact('quiz'));
    }

    public function update(QuizUpdateRequest $request, Quizzes $quiz)
    {
        if ($quiz->user_id !== Auth::id()) {
            abort(403);
        }

        $quiz->update([
            ...$request->validated(),
            'is_published' => $request->boolean('is_published'),
            'randomize_questions' => $request->boolean('randomize_questions'),
            'show_result_immediately' => $request->boolean('show_result_immediately'),
        ]);

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Quiz berhasil diperbarui.');
    }

    public function destroy(Quizzes $quiz)
    {
        if ($quiz->user_id !== Auth::id()) {
            abort(403);
        }

        $quiz->delete();

        return redirect()->route('quizzes.index')->with('success', 'Quiz berhasil dihapus.');
    }
}