<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mulai Kuis - Student</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 p-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-t-2xl p-6 border-b-4 border-blue-600 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-800">QuizQuest: Ruang Belajar</h1>
            <p class="text-sm text-gray-500 italic">Disediakan oleh Admin • AI Tutor Aktif</p>
        </div>

        <div id="quiz-list" class="mt-6 space-y-4">
            <!-- Soal dari database atau AI akan muncul di sini -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <p class="font-semibold mb-4 text-lg">1. Apa yang dimaksud dengan Primary Key pada Basis Data?</p>
                <div class="space-y-2">
                    <button onclick="showExpl(0)" class="w-full text-left p-3 border rounded-lg hover:bg-blue-100 transition">A. Kunci cadangan</button>
                    <button onclick="showExpl(0)" class="w-full text-left p-3 border rounded-lg hover:bg-blue-100 transition">B. Identitas unik setiap baris tabel</button>
                </div>
                <div id="expl-0" class="hidden mt-4 p-3 bg-yellow-50 text-yellow-800 text-sm rounded border border-yellow-200">
                    <strong>AI Tutor:</strong> Primary Key adalah atribut unik yang membedakan satu record dengan record lainnya dalam sebuah tabel.
                </div>
            </div>
        </div>
    </div>

    <script>
        function showExpl(id) {
            document.getElementById('expl-'+id).classList.remove('hidden');
        }
    </script>
</body>
</html>