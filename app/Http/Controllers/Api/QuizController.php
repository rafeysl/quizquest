<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QuizController extends Controller
{
    public function generateWithAI(Request $request)
    {
        $topic = $request->input('topic', 'Sistem Basis Data');
        $apiKey = env('GROQ_API_KEY');

        $response = Http::withToken($apiKey)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Kamu adalah dosen Informatika. Berikan 5 soal pilihan ganda dalam format JSON murni. Setiap soal WAJIB memiliki key: "question", "options" (array), "answer", dan "explanation" (pembahasan singkat).'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Buatkan soal kuis tentang: $topic"
                    ]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

        return response()->json($response->json());
    }
}