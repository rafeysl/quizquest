<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - QuizQuest</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-red-600">Admin Panel: Generate & Validasi</h1>
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">Role: Admin</span>
        </div>

        <div class="flex gap-4 mb-10">
            <input type="text" id="topic" placeholder="Topik (Misal: Normalisasi Basis Data)" class="flex-1 border p-3 rounded-lg outline-none focus:ring-2 focus:ring-red-500">
            <button onclick="generateDraft()" class="bg-red-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-red-700">Minta Draf AI</button>
        </div>

        <div id="draft-container" class="space-y-4">
            <p class="text-center text-gray-400 italic">Draf soal dari Groq akan muncul di sini untuk kamu validasi.</p>
        </div>
    </div>

    <script>
        async function generateDraft() {
            const topic = document.getElementById('topic').value;
            const container = document.getElementById('draft-container');
            container.innerHTML = '<p class="text-center">Groq sedang menyusun draf soal...</p>';

            const response = await fetch(`/api/quizzes/generate?topic=${topic}`);
            const result = await response.json();
            const content = JSON.parse(result.choices[0].message.content);
            const questions = content.questions || content;

            container.innerHTML = '<h3 class="font-bold mb-4">Validasi Soal:</h3>';
            questions.forEach((item, index) => {
                container.innerHTML += `
                    <div class="p-4 border-2 border-gray-200 rounded-lg mb-4 bg-gray-50">
                        <textarea class="w-full font-bold mb-2 p-2 border rounded">${item.question}</textarea>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            ${item.options.map(opt => `<input type="text" value="${opt}" class="p-2 border rounded text-sm">`).join('')}
                        </div>
                        <p class="text-sm text-green-600 font-semibold">Jawaban: ${item.answer}</p>
                        <button class="mt-4 bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">Simpan ke Bank Soal</button>
                    </div>`;
            });
        }
    </script>
</body>
</html>