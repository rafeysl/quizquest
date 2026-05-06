<x-app-layout>
    <x-slot name="title">Quiz Saya — QuizQuest</x-slot>
    <x-slot name="pageTitle">Quiz Saya</x-slot>
    <x-slot name="pageSubtitle">Kelola dan buat quiz baru</x-slot>

    <x-slot name="topbarAction">
        <a href="{{ route('quizzes.create') }}" class="qq-btn qq-btn-primary">
            <i class="ti ti-plus"></i>
            Buat Quiz
        </a>
    </x-slot>

    {{-- ===== FILTER & SEARCH ===== --}}
    <div class="qq-card" style="margin-bottom: 20px; padding: 14px 18px;">
        <form method="GET" action="{{ route('quizzes.index') }}"
              style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">

            {{-- Search --}}
            <div style="position:relative; flex:1; min-width:200px;">
                <i class="ti ti-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--qq-text-muted); font-size:17px;"></i>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari quiz..."
                       class="qq-input"
                       style="padding-left: 38px;">
            </div>

            {{-- Filter Kategori --}}
            <select name="category" class="qq-select" style="width:auto; min-width:150px;">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Difficulty --}}
            <select name="difficulty" class="qq-select" style="width:auto; min-width:130px;">
                <option value="">Semua Level</option>
                <option value="mudah" {{ request('difficulty') === 'mudah' ? 'selected' : '' }}>Mudah</option>
                <option value="sedang" {{ request('difficulty') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="sulit" {{ request('difficulty') === 'sulit' ? 'selected' : '' }}>Sulit</option>
            </select>

            {{-- Sort --}}
            <select name="sort" class="qq-select" style="width:auto; min-width:140px;">
                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Terpopuler</option>
                <option value="score" {{ request('sort') === 'score' ? 'selected' : '' }}>Skor Tertinggi</option>
            </select>

            <button type="submit" class="qq-btn qq-btn-outline qq-btn-sm">
                <i class="ti ti-filter"></i>
                Filter
            </button>

            @if(request()->hasAny(['search','category','difficulty','sort']))
                <a href="{{ route('quizzes.index') }}" class="qq-btn qq-btn-ghost qq-btn-sm">
                    <i class="ti ti-x"></i>
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- ===== AI GENERATE QUIZ ===== --}}
    <div class="qq-card" style="margin-bottom: 24px; border-color: var(--qq-ai-border); background: var(--qq-ai-light);">
        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
            <div class="qq-ai-avatar">
                <i class="ti ti-sparkles"></i>
            </div>
            <div style="flex:1; min-width:200px;">
                <p style="font-size:14px; font-weight:700; color:var(--qq-ai-primary);">Generate Quiz dengan AI</p>
                <p style="font-size:12.5px; color:var(--qq-text-secondary); margin-top:2px;">
                    Masukkan topik dan biarkan AI membuat soal quiz untukmu secara otomatis!
                </p>
            </div>
            <div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
                <input type="text"
                       id="aiTopicInput"
                       placeholder="Contoh: Fotosintesis, Pythagoras..."
                       class="qq-input"
                       style="width: 240px;"
                       onkeypress="if(event.key==='Enter') generateQuizAI()">
                <button class="qq-btn qq-btn-ai" onclick="generateQuizAI()">
                    <i class="ti ti-wand"></i>
                    Generate
                </button>
            </div>
        </div>
    </div>

    {{-- ===== DAFTAR QUIZ ===== --}}
    @if($quizzes->isEmpty())
        <div class="qq-card">
            <div class="qq-empty">
                <i class="ti ti-brain qq-empty-icon"></i>
                <p class="qq-empty-title">Belum ada quiz</p>
                <p class="qq-empty-desc">
                    @if(request()->hasAny(['search','category','difficulty']))
                        Tidak ada quiz yang cocok dengan filtermu. Coba ubah kriteria pencarian.
                    @else
                        Kamu belum membuat quiz apapun. Mulai sekarang dan bagikan ke teman-temanmu!
                    @endif
                </p>
                @if(!request()->hasAny(['search','category','difficulty']))
                    <a href="{{ route('quizzes.create') }}" class="qq-btn qq-btn-primary" style="margin-top:8px;">
                        <i class="ti ti-plus"></i> Buat Quiz Pertama
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="qq-quiz-grid">
            @foreach($quizzes as $quiz)
                <div class="qq-quiz-card">
                    {{-- Cover --}}
                    <div class="qq-quiz-cover" style="background: {{ $quiz->cover_color ?? '#F2EFE9' }};">
                        {{ $quiz->cover_emoji ?? '📚' }}
                        @if($quiz->is_collaborative)
                            <span style="position:absolute; top:8px; right:8px; z-index:1; background:var(--qq-ai-primary); color:#fff; font-size:10px; font-weight:700; padding:3px 8px; border-radius:var(--qq-radius-pill); display:flex; align-items:center; gap:3px;">
                                <i class="ti ti-users" style="font-size:12px;"></i>
                                Kolaborasi
                            </span>
                        @endif
                    </div>

                    {{-- Body --}}
                    <div class="qq-quiz-body">
                        <div>
                            <h3 class="qq-quiz-title">{{ $quiz->title }}</h3>
                            <p style="font-size:12.5px; color:var(--qq-text-secondary); margin-top:4px; line-height:1.5;">
                                {{ Str::limit($quiz->description, 80) }}
                            </p>
                        </div>

                        <div class="qq-quiz-meta">
                            <span><i class="ti ti-list-numbers"></i> {{ $quiz->questions_count }} soal</span>
                            <span><i class="ti ti-clock"></i> {{ $quiz->duration }}m</span>
                            <span><i class="ti ti-users"></i> {{ $quiz->attempts_count }}x dicoba</span>
                        </div>

                        <div style="display:flex; align-items:center; justify-content:space-between;">
                            <div class="qq-quiz-tags">
                                @foreach($quiz->tags->take(2) as $tag)
                                    <span class="qq-tag">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                            <span class="qq-difficulty {{ $quiz->difficulty }}">{{ ucfirst($quiz->difficulty) }}</span>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="qq-quiz-footer">
                        {{-- Kiri: Author --}}
                        <div style="display:flex; align-items:center; gap:6px;">
                            <div class="qq-avatar" style="width:22px;height:22px;font-size:9px;">
                                {{ strtoupper(substr($quiz->user->name ?? 'U', 0, 2)) }}
                            </div>
                            <span style="font-size:11.5px; color:var(--qq-text-secondary);">
                                {{ $quiz->user->name ?? 'Anonim' }}
                            </span>
                        </div>

                        {{-- Kanan: Actions --}}
                        <div style="display:flex; gap:5px;">
                            @can('update', $quiz)
                                <a href="{{ route('quizzes.edit', $quiz) }}"
                                   class="qq-btn qq-btn-ghost qq-btn-sm"
                                   title="Edit Quiz">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button class="qq-btn qq-btn-ghost qq-btn-sm"
                                        onclick="confirmDelete({{ $quiz->id }}, '{{ addslashes($quiz->title) }}')"
                                        title="Hapus Quiz"
                                        style="color:var(--qq-danger);">
                                    <i class="ti ti-trash"></i>
                                </button>
                            @endcan
                            <a href="{{ route('quizzes.take', $quiz) }}" class="qq-btn qq-btn-primary qq-btn-sm">
                                Mulai <i class="ti ti-player-play"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="qq-pagination">
            {{ $quizzes->withQueryString()->links('vendor.pagination.quizquest') }}
        </div>
    @endif

    {{-- ===== MODAL KONFIRMASI HAPUS ===== --}}
    <div class="qq-modal-backdrop" id="deleteModal" style="display:none;" onclick="if(event.target===this) closeDeleteModal()">
        <div class="qq-modal">
            <div class="qq-modal-header">
                <div>
                    <p class="qq-modal-title">Hapus Quiz?</p>
                    <p style="font-size:13px; color:var(--qq-text-secondary); margin-top:4px;">
                        Quiz "<span id="deleteQuizName" style="font-weight:600;"></span>" akan dihapus permanen.
                    </p>
                </div>
                <button class="qq-icon-btn" onclick="closeDeleteModal()">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <p style="font-size:13.5px; color:var(--qq-text-secondary); line-height:1.6;">
                Semua soal, jawaban, dan riwayat attempt akan ikut terhapus. Tindakan ini tidak bisa dibatalkan.
            </p>

            <div class="qq-modal-footer">
                <button class="qq-btn qq-btn-outline" onclick="closeDeleteModal()">Batal</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="qq-btn qq-btn-primary" style="background:var(--qq-danger); border-color:var(--qq-danger);">
                        <i class="ti ti-trash"></i>
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // Delete modal
    function confirmDelete(quizId, quizName) {
        document.getElementById('deleteQuizName').textContent = quizName;
        document.getElementById('deleteForm').action = `/quizzes/${quizId}`;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // AI Generate Quiz
    async function generateQuizAI() {
        const topic = document.getElementById('aiTopicInput').value.trim();
        if (!topic) {
            document.getElementById('aiTopicInput').focus();
            return;
        }

        // Buka AI panel dan kirim pesan
        document.getElementById('aiPanel').classList.add('open');
        sendAIMessage(`Buatkan 10 soal quiz pilihan ganda tentang: ${topic}. Format: nomor soal, pertanyaan, dan 4 opsi jawaban (A, B, C, D) dengan satu jawaban benar.`);
    }
    </script>
    @endpush

</x-app-layout>