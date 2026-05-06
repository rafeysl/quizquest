<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard Quiz App 🎯
        </h2>
    </x-slot>

    <div class="p-6 space-y-6">
        {{-- Welcome Card --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
            <h3 class="text-2xl font-bold mb-2 dark:text-white">
                Selamat datang, {{ Auth::user()->name ?? 'Rafeyfa' }} 👋
            </h3>
            <p class="text-gray-600 dark:text-gray-400">Siap menguji pengetahuanmu hari ini?</p>

            <a href="{{ route('quizzes.index') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg">
                Mulai Quiz 🚀
            </a>
        </div>

        {{-- Statistik & Daftar Quiz (Gunakan kode kamu yang tadi) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Statistik items... --}}
        </div>
    </div>
</x-app-layout>