<x-app-layout>
    <x-slot name="title">Dashboard — QuizQuest</x-slot>

    <x-slot name="pageTitle">Dashboard</x-slot>
    <x-slot name="pageSubtitle">Selamat datang, {{ auth()->user()->name }}! 👋</x-slot>

    <x-slot name="topbarAction">
        <a href="{{ route('quizzes.create') }}" class="qq-btn qq-btn-primary">
            <i class="ti ti-plus"></i>
            Buat Quiz
        </a>
    </x-slot>

    {{-- ===== STAT CARDS ===== --}}
    <div class="qq-stats-grid">
        <div class="qq-stat-card">
            <div class="qq-stat-icon accent">
                <i class="ti ti-brain"></i>
            </div>
            <p class="qq-stat-label">Quiz Dibuat</p>
            <p class="qq-stat-value">{{ $stats['total_quizzes'] ?? 0 }}</p>
            <p class="qq-stat-change up">
                <i class="ti ti-trending-up"></i>
                +{{ $stats['new_quizzes_this_month'] ?? 0 }} bulan ini
            </p>
        </div>

        <div class="qq-stat-card">
            <div class="qq-stat-icon success">
                <i class="ti ti-checkbox"></i>
            </div>
            <p class="qq-stat-label">Quiz Diselesaikan</p>
            <p class="qq-stat-value">{{ $stats['completed'] ?? 0 }}</p>
            <p class="qq-stat-change up">
                <i class="ti ti-trending-up"></i>
                Tingkat selesai {{ $stats['completion_rate'] ?? 0 }}%
            </p>
        </div>

        <div class="qq-stat-card">
            <div class="qq-stat-icon warning">
                <i class="ti ti-star"></i>
            </div>
            <p class="qq-stat-label">Rata-rata Skor</p>
            <p class="qq-stat-value">{{ $stats['avg_score'] ?? 0 }}<span style="font-size: 14px; color: var(--qq-text-secondary);">%</span></p>
            <p class="qq-stat-change {{ ($stats['score_trend'] ?? 0) >= 0 ? 'up' : 'down' }}">
                <i class="ti ti-{{ ($stats['score_trend'] ?? 0) >= 0 ? 'trending-up' : 'trending-down' }}"></i>
                {{ $stats['score_trend'] ?? 0 }}% dari minggu lalu
            </p>
        </div>

        <div class="qq-stat-card">
            <div class="qq-stat-icon ai">
                <i class="ti ti-sparkles"></i>
            </div>
            <p class="qq-stat-label">AI Digunakan</p>
            <p class="qq-stat-value">{{ $stats['ai_used'] ?? 0 }}</p>
            <p class="qq-stat-change up">
                <i class="ti ti-robot"></i>
                Soal digenerate AI
            </p>
        </div>
    </div>

    {{-- ===== GRID 2 KOLOM ===== --}}
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 18px; align-items: start;">

        {{-- Quiz Terbaru --}}
        <div>
            <div class="qq-section-header">
                <h2 class="qq-section-title">Quiz Terbaru</h2>
                <a href="{{ route('quizzes.index') }}" class="qq-btn qq-btn-ghost qq-btn-sm">
                    Lihat Semua <i class="ti ti-arrow-right"></i>
                </a>
            </div>

            @if($recentQuizzes->isEmpty())
                <div class="qq-card">
                    <div class="qq-empty">
                        <i class="ti ti-brain qq-empty-icon"></i>
                        <p class="qq-empty-title">Belum ada quiz</p>
                        <p class="qq-empty-desc">Mulai buat quiz pertamamu atau jelajahi quiz yang sudah ada.</p>
                        <a href="{{ route('quizzes.create') }}" class="qq-btn qq-btn-primary" style="margin-top: 8px;">
                            <i class="ti ti-plus"></i> Buat Quiz Pertama
                        </a>
                    </div>
                </div>
            @else
                <div class="qq-quiz-grid">
                    @foreach($recentQuizzes as $quiz)
                        <div class="qq-quiz-card">
                            <div class="qq-quiz-cover" style="background: {{ $quiz->cover_color ?? '#F2EFE9' }};">
                                {{ $quiz->cover_emoji ?? '📚' }}
                            </div>
                            <div class="qq-quiz-body">
                                <div>
                                    <h3 class="qq-quiz-title">{{ $quiz->title }}</h3>
                                    <div class="qq-quiz-meta" style="margin-top: 6px;">
                                        <span><i class="ti ti-list-numbers"></i> {{ $quiz->questions_count }} soal</span>
                                        <span><i class="ti ti-clock"></i> {{ $quiz->duration }} menit</span>
                                        <span><i class="ti ti-users"></i> {{ $quiz->attempts_count }}x</span>
                                    </div>
                                </div>
                                <div class="qq-quiz-tags">
                                    @foreach($quiz->tags->take(3) as $tag)
                                        <span class="qq-tag">{{ $tag->name }}</span>
                                    @endforeach
                                    <span class="qq-difficulty {{ $quiz->difficulty }}">
                                        {{ ucfirst($quiz->difficulty) }}
                                    </span>
                                </div>
                                @if($quiz->user_progress)
                                    <div>
                                        <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--qq-text-secondary); margin-bottom:5px;">
                                            <span>Progress</span>
                                            <span>{{ $quiz->user_progress }}%</span>
                                        </div>
                                        <div class="qq-progress">
                                            <div class="qq-progress-fill" style="width: {{ $quiz->user_progress }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="qq-quiz-footer">
                                <span style="font-size:12px; color:var(--qq-text-muted);">
                                    <i class="ti ti-calendar" style="vertical-align:-2px;"></i>
                                    {{ $quiz->created_at->diffForHumans() }}
                                </span>
                                <div style="display:flex; gap:6px;">
                                    <a href="{{ route('quizzes.edit', $quiz) }}" class="qq-btn qq-btn-ghost qq-btn-sm">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="{{ route('quizzes.take', $quiz) }}" class="qq-btn qq-btn-primary qq-btn-sm">
                                        Mulai
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Sidebar kanan --}}
        <div style="display:flex; flex-direction:column; gap:16px;">

            {{-- Leaderboard --}}
            <div class="qq-card">
                <div class="qq-card-header">
                    <div>
                        <p class="qq-card-title">🏆 Leaderboard</p>
                        <p class="qq-card-subtitle">Top performer minggu ini</p>
                    </div>
                    <a href="{{ route('leaderboard') }}" class="qq-btn qq-btn-ghost qq-btn-sm">Semua</a>
                </div>
                @foreach($topUsers as $i => $user)
                    <div class="qq-leaderboard-item">
                        <span class="qq-rank {{ $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : '')) }}">
                            {{ $i < 3 ? ['🥇','🥈','🥉'][$i] : '#'.($i+1) }}
                        </span>
                        <div class="qq-avatar" style="width:30px;height:30px;font-size:11px;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:13px; font-weight:600; color:var(--qq-text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $user->name }}
                            </p>
                            <p style="font-size:11px; color:var(--qq-text-secondary);">{{ $user->total_score }} poin</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Aktivitas Terbaru --}}
            <div class="qq-card">
                <div class="qq-card-header">
                    <div>
                        <p class="qq-card-title">Aktivitas Terbaru</p>
                        <p class="qq-card-subtitle">Riwayat quiz kamu</p>
                    </div>
                </div>
                <div style="display:flex; flex-direction:column; gap:2px;">
                    @forelse($recentActivity as $activity)
                        <div style="display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:0.5px solid var(--qq-border);">
                            <div style="width:32px;height:32px; border-radius:8px; background:var(--qq-accent-light); display:flex; align-items:center; justify-content:center; color:var(--qq-accent); font-size:16px; flex-shrink:0;">
                                <i class="ti ti-{{ $activity->icon ?? 'checkbox' }}"></i>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <p style="font-size:12.5px; font-weight:600; color:var(--qq-text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $activity->title }}
                                </p>
                                <p style="font-size:11px; color:var(--qq-text-secondary);">
                                    Skor: {{ $activity->score }}% · {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p style="font-size:13px; color:var(--qq-text-secondary); padding: 8px 0;">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</x-app-layout>