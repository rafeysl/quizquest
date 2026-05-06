<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizQuest - Pembahasan AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-4 md:p-10">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-blue-600 p-6 text-white text-center">
            <h1 class="text-3xl font-bold">QuizQuest</h1>
            <p class="text-blue-100 opacity-80">Belajar $topic Jadi Lebih Mudah</p>
        </div>

        <div class="p-6">
            <div class="flex gap-2 mb-8">
                <input type="text" id="topic" placeholder="Contoh: Normalisasi Basis Data" 
                       class="flex-1 border-2 border-gray-200 p-3 rounded-lg focus:border-blue-500 outline-none transition">
                <button onclick="generateQuiz()" 
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                    Buat Kuis
                </button>
            </div>

            <div id="quiz-container" class="space-y-8">
                <div class="text-center py-10 text-gray-400">
                    <p>Masukkan topik kuliahmu di atas untuk mulai belajar!</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function generateQuiz() {
            const topic = document.getElementById('topic').value;
            const container = document.getElementById('quiz-container');
            
            if(!topic) return alert("Isi topiknya dulu ya!");

            container.innerHTML = `
                <div class="text-center py-10">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-500 italic">Groq sedang menyusun soal dan pembahasan...</p>
                </div>`;

            try {
                const response = await fetch(`/api/quizzes/generate?topic=${topic}`);
                const result = await response.json();
                
                const rawContent = result.choices[0].message.content;
                const parsedData = JSON.parse(rawContent);
                const questions = parsedData.questions || parsedData.soal || parsedData;

                container.innerHTML = '';
                questions.forEach((item, index) => {
                    const safeExpl = (item.explanation || "Tidak ada pembahasan.").replace(/"/g, '&quot;').replace(/'/g, "\\'");
                    
                    container.innerHTML += `
                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <p class="text-lg font-semibold text-gray-800 mb-4">${index + 1}. ${item.question}</p>
                            <div class="grid grid-cols-1 gap-3">
                                ${item.options.map(opt => `
                                    <button onclick="checkAnswer(this, '${opt}', '${item.answer}', ${index}, '${safeExpl}')" 
                                            class="option-btn text-left p-3 border-2 border-gray-100 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition">
                                        ${opt}
                                    </button>
                                `).join('')}
                            </div>
                            <div id="expl-box-${index}" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg hidden">
                                <p class="text-sm text-yellow-800"><strong>Pembahasan:</strong> ${item.explanation}</p>
                            </div>
                        </div>`;
                });
            } catch (error) {
                container.innerHTML = '<p class="text-center text-red-500">Gagal memuat kuis. Cek koneksi atau API Key kamu.</p>';
            }
        }

        function checkAnswer(btn, selected, correct, index, explanation) {
            const parent = btn.parentElement;
            const allBtns = parent.querySelectorAll('.option-btn');
            const explBox = document.getElementById(`expl-box-${index}`);

            // Matikan semua tombol agar tidak bisa klik dua kali
            allBtns.forEach(b => b.disabled = true);

            if (selected === correct) {
                btn.classList.add('bg-green-100', 'border-green-500', 'text-green-700');
            } else {
                btn.classList.add('bg-red-100', 'border-red-500', 'text-red-700');
            }

            // Tampilkan kotak pembahasan
            explBox.classList.remove('hidden');
        }
    </script>
</body>
</html>