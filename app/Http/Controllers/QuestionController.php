<?php

namespace App\Http\Controllers;

use App\Models\Quizzes;
use App\Models\Questions;
use App\Models\Options;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\QuestionStoreRequest;
use App\Http\Requests\QuestionUpdateRequest;

class QuestionController extends Controller
{
    public function create(Quizzes $quiz)
    {
        if ($quiz->user_id !== Auth::id()) abort(403);
        return view('questions.create', compact('quiz'));
    }

    public function store(QuestionStoreRequest $request, Quizzes $quiz)
    {
        if ($quiz->user_id !== Auth::id()) abort(403);

        $question = Questions::create([
            'quiz_id' => $quiz->id,
            'question_text' => $request->question_text,
            'type' => $request->type,
            'points' => $request->points,
            'sort_order' => $request->sort_order,
            'explanation' => $request->explanation,
        ]);

        if ($request->type !== 'essay') {
            foreach ($request->options as $index => $optionText) {
                Options::create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => ($request->correct_option == $index),
                    'sort_order' => $index
                ]);
            }
        }

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Questions $question)
    {
        $quiz = $question->quiz;
        if ($quiz->user_id !== Auth::id()) abort(403);

        $question->load('options');
        return view('questions.edit', compact('quiz', 'question'));
    }

    public function update(QuestionUpdateRequest $request, Questions $question)
    {
        $quiz = $question->quiz;
        if ($quiz->user_id !== Auth::id()) abort(403);

        $question->update([
            'question_text' => $request->question_text,
            'type' => $request->type,
            'points' => $request->points,
            'sort_order' => $request->sort_order,
            'explanation' => $request->explanation,
        ]);

        $question->options()->delete();

        if ($request->type !== 'essay') {
            foreach ($request->options as $index => $optionText) {
                Options::create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => ($request->correct_option == $index),
                    'sort_order' => $index
                ]);
            }
        }

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Questions $question)
    {
        $quiz = $question->quiz;
        if ($quiz->user_id !== Auth::id()) abort(403);

        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus.');
    }
}