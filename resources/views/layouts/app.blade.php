<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'QuizQuest' }}</title>

    {{-- Google Fonts: Sora + JetBrains Mono --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    {{-- QuizQuest CSS --}}
    @vite(['resources/css/quizquest.css'])

    @stack('styles')
</head>
<body>

<div class="qq-shell" id="appShell">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="qq-sidebar" id="sidebar">

        {{-- Logo --}}
        <div class="qq-logo">
            <div class="qq-logo-icon">Q</div>
            <span class="qq-logo-text">Quiz<span>Quest</span></span>
        </div>

        {{-- Nav Utama --}}
        <nav class="qq-nav">
            <p class="qq-nav-label">Menu</p>

            <a href="{{ route('dashboard') }}"
               class="qq-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('quizzes.index') }}"
               class="qq-nav-item {{ request()->routeIs('quizzes.*') ? 'active' : '' }}">
                <i class="ti ti-brain"></i>
                <span>Quiz Saya</span>
                @if(auth()->user()->quizzes_count ?? false)
                    <span class="qq-badge">{{ auth()->user()->quizzes_count }}</span>
                @endif
            </a>

            <a href="{{ route('quizzes.explore') }}"
               class="qq-nav-item {{ request()->routeIs('quizzes.explore') ? 'active' : '' }}">
                <i class="ti ti-compass"></i>
                <span>Jelajahi</span>
            </a>

            <a href="{{ route('leaderboard') }}"
               class="qq-nav-item {{ request()->routeIs('leaderboard') ? 'active' : '' }}">
                <i class="ti ti-trophy"></i>
                <span>Leaderboard</span>
            </a>

            <p class="qq-nav-label" style="margin-top: 8px;">Kolaborasi</p>

            <a href="{{ route('teams.index') }}"
               class="qq-nav-item {{ request()->routeIs('teams.*') ? 'active' : '' }}">
                <i class="ti ti-users-group"></i>
                <span>Tim Saya</span>
            </a>

            <a href="{{ route('invitations') }}"
               class="qq-nav-item {{ request()->routeIs('invitations') ? 'active' : '' }}">
                <i class="ti ti-mail-opened"></i>
                <span>Undangan</span>
            </a>
        </nav>

        {{-- Tombol AI Assistant --}}
        <div class="qq-ai-trigger" id="aiTriggerBtn" onclick="toggleAIPanel()">
            <div class="qq-ai-pulse"></div>
            <i class="ti ti-sparkles"></i>
            <span>AI Assistant</span>
        </div>

        {{-- User Info --}}
        <div class="qq-sidebar-footer">
            <div class="qq-user-chip">
                <div class="qq-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="qq-user-info">
                    <p class="qq-user-name">{{ auth()->user()->name }}</p>
                    <p class="qq-user-role">{{ auth()->user()->role ?? 'Peserta' }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="qq-icon-btn" title="Pengaturan Profil">
                    <i class="ti ti-settings"></i>
                </a>
            </div>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="qq-main">

        {{-- Topbar --}}
        <header class="qq-topbar">
            {{-- Mobile: hamburger --}}
            <button class="qq-hamburger" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Toggle menu">
                <i class="ti ti-menu-2"></i>
            </button>

            {{-- Page title slot --}}
            <div class="qq-topbar-title">
                @isset($pageTitle)
                    <h1 class="qq-page-title">{{ $pageTitle }}</h1>
                    @isset($pageSubtitle)
                        <p class="qq-page-sub">{{ $pageSubtitle }}</p>
                    @endisset
                @endisset
            </div>

            {{-- Kanan: theme toggle + notif + action --}}
            <div class="qq-topbar-right">

                {{-- Dark/Light Mode Toggle --}}
                <button class="qq-theme-toggle" id="themeToggleBtn" onclick="toggleTheme()" aria-label="Toggle tema">
                    <i class="ti ti-sun" id="themeIconSun"></i>
                    <i class="ti ti-moon" id="themeIconMoon" style="display:none"></i>
                    <div class="qq-toggle-track" id="toggleTrack">
                        <div class="qq-toggle-thumb" id="toggleThumb"></div>
                    </div>
                </button>

                {{-- Notifikasi --}}
                <button class="qq-icon-btn qq-notif-btn" aria-label="Notifikasi">
                    <i class="ti ti-bell"></i>
                    <span class="qq-notif-dot"></span>
                </button>

                {{-- Action slot (e.g. tombol buat quiz) --}}
                @isset($topbarAction)
                    {{ $topbarAction }}
                @endisset
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="qq-alert qq-alert-success">
                <i class="ti ti-circle-check"></i>
                {{ session('success') }}
                <button onclick="this.parentElement.remove()" class="qq-alert-close"><i class="ti ti-x"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="qq-alert qq-alert-danger">
                <i class="ti ti-alert-circle"></i>
                {{ session('error') }}
                <button onclick="this.parentElement.remove()" class="qq-alert-close"><i class="ti ti-x"></i></button>
            </div>
        @endif

        {{-- Page Content --}}
        <main class="qq-content">
            {{ $slot }}
        </main>
    </div>

    {{-- ===== AI ASSISTANT PANEL ===== --}}
    <div class="qq-ai-panel" id="aiPanel">
        <div class="qq-ai-header">
            <div class="qq-ai-header-left">
                <div class="qq-ai-avatar">
                    <i class="ti ti-sparkles"></i>
                </div>
                <div>
                    <p class="qq-ai-title">AI Assistant</p>
                    <p class="qq-ai-status"><span class="qq-status-dot"></span>Siap membantu</p>
                </div>
            </div>
            <button class="qq-icon-btn" onclick="toggleAIPanel()" aria-label="Tutup AI Panel">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div class="qq-ai-messages" id="aiMessages">
            {{-- Pesan selamat datang --}}
            <div class="qq-ai-bubble qq-ai-bubble--bot">
                <p>Halo! 👋 Aku AI Assistant QuizQuest. Aku bisa bantu kamu:</p>
                <ul style="margin: 8px 0 0 16px; font-size: 13px; line-height: 1.8;">
                    <li>Buat soal quiz otomatis dari topik apapun</li>
                    <li>Jelaskan jawaban yang salah</li>
                    <li>Rekomendasikan quiz sesuai levelmu</li>
                    <li>Evaluasi performa belajarmu</li>
                </ul>
            </div>

            {{-- Quick actions --}}
            <div class="qq-ai-chips">
                <button class="qq-ai-chip" onclick="sendAIMessage('Buatkan 5 soal tentang Matematika kelas 10')">
                    ✨ Buat soal Matematika
                </button>
                <button class="qq-ai-chip" onclick="sendAIMessage('Rekomendasikan quiz untuk pemula')">
                    📚 Rekomendasi quiz
                </button>
                <button class="qq-ai-chip" onclick="sendAIMessage('Evaluasi performa belajar saya')">
                    📊 Evaluasi performa
                </button>
            </div>
        </div>

        <div class="qq-ai-input-area">
            <input
                type="text"
                class="qq-ai-input"
                id="aiInput"
                placeholder="Tanya sesuatu..."
                onkeypress="if(event.key==='Enter') sendAIMessage()"
            >
            <button class="qq-ai-send" id="aiSendBtn" onclick="sendAIMessage()">
                <i class="ti ti-send"></i>
            </button>
        </div>
    </div>

    {{-- Overlay untuk mobile sidebar --}}
    <div class="qq-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

</div>{{-- end .qq-shell --}}

@vite(['resources/js/quizquest.js'])

<script>
// ============================
// THEME TOGGLE
// ============================
const html = document.documentElement;
const themeKey = 'quizquest_theme';

function applyTheme(theme) {
    html.setAttribute('data-theme', theme);
    const isDark = theme === 'dark';
    document.getElementById('themeIconSun').style.display = isDark ? 'none' : '';
    document.getElementById('themeIconMoon').style.display = isDark ? '' : 'none';
    document.getElementById('toggleTrack').classList.toggle('on', isDark);
    document.getElementById('toggleThumb').classList.toggle('on', isDark);
}

function toggleTheme() {
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    localStorage.setItem(themeKey, next);
    applyTheme(next);
    // Kirim ke server (simpan di DB via AJAX)
    fetch('{{ route("theme.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ theme: next })
    });
}

// Init tema dari localStorage atau preferensi user
(function() {
    const saved = localStorage.getItem(themeKey) || '{{ auth()->user()->theme_preference ?? "light" }}';
    applyTheme(saved);
})();

// ============================
// SIDEBAR TOGGLE (Mobile)
// ============================
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('active');
}

// ============================
// AI ASSISTANT PANEL
// ============================
function toggleAIPanel() {
    document.getElementById('aiPanel').classList.toggle('open');
    if (document.getElementById('aiPanel').classList.contains('open')) {
        document.getElementById('aiInput').focus();
    }
}

async function sendAIMessage(presetMessage = null) {
    const input = document.getElementById('aiInput');
    const message = presetMessage || input.value.trim();
    if (!message) return;

    const messagesEl = document.getElementById('aiMessages');

    // Tampilkan pesan user
    messagesEl.innerHTML += `
        <div class="qq-ai-bubble qq-ai-bubble--user">${escapeHtml(message)}</div>
    `;
    input.value = '';
    messagesEl.scrollTop = messagesEl.scrollHeight;

    // Loading indicator
    const loadingId = 'ai-loading-' + Date.now();
    messagesEl.innerHTML += `
        <div class="qq-ai-bubble qq-ai-bubble--bot qq-ai-loading" id="${loadingId}">
            <span></span><span></span><span></span>
        </div>
    `;
    messagesEl.scrollTop = messagesEl.scrollHeight;

    // Kirim ke backend
    try {
        const res = await fetch('{{ route("ai.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message })
        });
        const data = await res.json();

        document.getElementById(loadingId).remove();
        messagesEl.innerHTML += `
            <div class="qq-ai-bubble qq-ai-bubble--bot">${data.reply}</div>
        `;
    } catch (e) {
        document.getElementById(loadingId).remove();
        messagesEl.innerHTML += `
            <div class="qq-ai-bubble qq-ai-bubble--bot qq-ai-error">
                Maaf, terjadi kesalahan. Coba lagi ya.
            </div>
        `;
    }
    messagesEl.scrollTop = messagesEl.scrollHeight;
}

function escapeHtml(text) {
    return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Alert auto-dismiss
document.querySelectorAll('.qq-alert').forEach(el => {
    setTimeout(() => el.style.opacity = '0', 4000);
    setTimeout(() => el.remove(), 4500);
});
</script>

@stack('scripts')
</body>
</html>