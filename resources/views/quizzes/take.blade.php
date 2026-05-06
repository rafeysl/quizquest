<x-app-layout>
    <x-slot name="title">{{ $quiz->title }} — QuizQuest</x-slot>
    <x-slot name="pageTitle">{{ $quiz->title }}</x-slot>
    <x-slot name="pageSubtitle">{{ $quiz->questions->count() }} soal · {{ $quiz->duration }} menit</x-slot>

    <div class="qq-quiz-take-wrap">

        {{-- ===== HEADER: Progress + Timer ===== --}}
        <div class="qq-quiz-take-header">
            <a href="{{ route('quizzes.index') }}" class="qq-btn qq-btn-ghost qq-btn-sm">
                <i class="ti ti-arrow-left"></i>
            </a>

            <div class="qq-quiz-progress-bar">
                <div class="qq-quiz-progress-fill" id="quizProgress" style="width: 0%"></div>
            </div>

            <span class="qq-quiz-counter" id="questionCounter">1 / {{ $quiz->questions->count() }}</span>

            <div class="qq-timer" id="quizTimer">
                <i class="ti ti-clock"></i>
                <span id="timerDisplay">{{ $quiz->duration }}:00</span>
            </div>
        </div>

        {{-- ===== SOAL ===== --}}
        @foreach($quiz->questions as $i => $question)
            <div class="qq-question-card" id="question-{{ $i + 1 }}"
                 style="{{ $i > 0 ? 'display:none;' : '' }} animation: qq-slidein 0.3s ease both;">

                <p class="qq-question-num">Soal {{ $i + 1 }} dari {{ $quiz->questions->count() }}</p>

                <p class="qq-question-text">{{ $question->text }}</p>

                {{-- Gambar soal (opsional) --}}
                @if($question->image)
                    <img src="{{ Storage::url($question->image) }}"
                         alt="Gambar soal"
                         style="width:100%; max-height:240px; object-fit:cover; border-radius:var(--qq-radius-sm); margin-bottom:20px;">
                @endif

                {{-- ===== OPSI JAWABAN ===== --}}
                <div class="qq-options-list">
                    @foreach($question->options as $j => $option)
                        <button class="qq-option"
                                id="opt-{{ $i+1 }}-{{ $j }}"
                                onclick="selectOption({{ $i+1 }}, {{ $j }}, {{ $option->id }}, this)">
                            <span class="qq-option-key">{{ ['A','B','C','D'][$j] ?? $j+1 }}</span>
                            <span>{{ $option->text }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Tombol hint AI --}}
                <div style="margin-top: 16px; display:flex; align-items:center; gap:8px;">
                    <button class="qq-btn qq-btn-ai qq-btn-sm"
                            onclick="getAIHint({{ $question->id }}, '{{ addslashes($question->text) }}')">
                        <i class="ti ti-sparkles"></i>
                        Minta Petunjuk dari AI
                    </button>
                    <span id="hint-{{ $question->id }}" style="font-size:12.5px; color:var(--qq-text-secondary);"></span>
                </div>
            </div>
        @endforeach

        {{-- ===== HASIL / FEEDBACK AI ===== --}}
        <div id="aiHintBox" class="qq-card" style="display:none; border-color:var(--qq-ai-border); margin-top:16px;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <div class="qq-ai-avatar" style="width:30px;height:30px;">
                    <i class="ti ti-sparkles" style="font-size:16px;"></i>
                </div>
                <p style="font-size:13px; font-weight:700; color:var(--qq-ai-primary);">Petunjuk AI</p>
                <button onclick="document.getElementById('aiHintBox').style.display='none'"
                        class="qq-icon-btn" style="margin-left:auto; width:26px;height:26px;">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <p id="aiHintText" style="font-size:14px; color:var(--qq-text-primary); line-height:1.7;"></p>
        </div>

        {{-- ===== NAVIGASI SOAL ===== --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-top:20px;">
            <button class="qq-btn qq-btn-outline" id="prevBtn" onclick="prevQuestion()" style="visibility:hidden;">
                <i class="ti ti-arrow-left"></i>
                Sebelumnya
            </button>

            {{-- Dot navigator --}}
            <div style="display:flex; gap:6px; flex-wrap:wrap; justify-content:center; flex:1; margin:0 16px;">
                @foreach($quiz->questions as $i => $question)
                    <button class="qq-page-btn {{ $i === 0 ? 'active' : '' }}"
                            id="nav-dot-{{ $i+1 }}"
                            onclick="goToQuestion({{ $i+1 }})">
                        {{ $i + 1 }}
                    </button>
                @endforeach
            </div>

            <button class="qq-btn qq-btn-primary" id="nextBtn" onclick="nextQuestion()">
                Selanjutnya
                <i class="ti ti-arrow-right"></i>
            </button>
        </div>

        {{-- Form submit tersembunyi --}}
        <form id="quizSubmitForm" action="{{ route('quizzes.submit', $quiz) }}" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="answers" id="answersInput" value="{}">
            <input type="hidden" name="time_taken" id="timeTakenInput" value="0">
        </form>

    </div>

    @push('scripts')
    <script>
    // ============================
    // QUIZ STATE
    // ============================
    const TOTAL_QUESTIONS = {{ $quiz->questions->count() }};
    const DURATION_SECONDS = {{ $quiz->duration * 60 }};
    let currentQuestion = 1;
    let answers = {};       // { question_index: option_id }
    let timeLeft = DURATION_SECONDS;
    let timerInterval;

    // ============================
    // TIMER
    // ============================
    function startTimer() {
        timerInterval = setInterval(() => {
            timeLeft--;
            const mins = Math.floor(timeLeft / 60);
            const secs = timeLeft % 60;
            document.getElementById('timerDisplay').textContent =
                String(mins).padStart(2,'0') + ':' + String(secs).padStart(2,'0');

            if (timeLeft <= 60) {
                document.getElementById('quizTimer').classList.add('warning');
            }

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                submitQuiz();
            }
        }, 1000);
    }

    startTimer();

    // ============================
    // PILIH JAWABAN
    // ============================
    function selectOption(questionNum, optionIdx, optionId, el) {
        // Hapus selected dari semua opsi di soal ini
        document.querySelectorAll(`[id^="opt-${questionNum}-"]`).forEach(btn => {
            btn.classList.remove('selected');
        });

        // Set selected
        el.classList.add('selected');
        answers[questionNum] = optionId;

        // Update dot navigator
        const dot = document.getElementById(`nav-dot-${questionNum}`);
        if (dot) {
            dot.style.background = 'var(--qq-accent)';
            dot.style.borderColor = 'var(--qq-accent)';
            dot.style.color = '#fff';
        }
    }

    // ============================
    // NAVIGASI SOAL
    // ============================
    function goToQuestion(num) {
        document.getElementById(`question-${currentQuestion}`).style.display = 'none';
        document.getElementById(`nav-dot-${currentQuestion}`).classList.remove('active');

        currentQuestion = num;
        const card = document.getElementById(`question-${currentQuestion}`);
        card.style.display = 'block';
        card.style.animation = 'none';
        void card.offsetHeight; // reflow
        card.style.animation = 'qq-slidein 0.25s ease both';

        document.getElementById(`nav-dot-${currentQuestion}`).classList.add('active');

        updateNavButtons();
        updateProgressBar();
    }

    function nextQuestion() {
        if (currentQuestion < TOTAL_QUESTIONS) {
            goToQuestion(currentQuestion + 1);
        } else {
            submitQuiz();
        }
    }

    function prevQuestion() {
        if (currentQuestion > 1) {
            goToQuestion(currentQuestion - 1);
        }
    }

    function updateNavButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        prevBtn.style.visibility = currentQuestion > 1 ? 'visible' : 'hidden';

        if (currentQuestion === TOTAL_QUESTIONS) {
            nextBtn.innerHTML = '<i class="ti ti-check"></i> Kumpulkan';
            nextBtn.style.background = 'var(--qq-success)';
        } else {
            nextBtn.innerHTML = 'Selanjutnya <i class="ti ti-arrow-right"></i>';
            nextBtn.style.background = '';
        }
    }

    function updateProgressBar() {
        const pct = ((currentQuestion - 1) / TOTAL_QUESTIONS) * 100;
        document.getElementById('quizProgress').style.width = pct + '%';
        document.getElementById('questionCounter').textContent = currentQuestion + ' / ' + TOTAL_QUESTIONS;
    }

    // ============================
    // SUBMIT
    // ============================
    function submitQuiz() {
        clearInterval(timerInterval);
        document.getElementById('answersInput').value = JSON.stringify(answers);
        document.getElementById('timeTakenInput').value = DURATION_SECONDS - timeLeft;
        document.getElementById('quizSubmitForm').submit();
    }

    // ============================
    // AI HINT
    // ============================
    async function getAIHint(questionId, questionText) {
        const hintBox = document.getElementById('aiHintBox');
        const hintText = document.getElementById('aiHintText');

        hintBox.style.display = 'block';
        hintText.textContent = 'Sedang meminta petunjuk dari AI...';

        try {
            const res = await fetch('{{ route("ai.hint") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ question_id: questionId, question_text: questionText })
            });
            const data = await res.json();
            hintText.textContent = data.hint;
        } catch(e) {
            hintText.textContent = 'Maaf, tidak bisa mendapatkan petunjuk saat ini.';
        }

        hintBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    </script>
    @endpush

</x-app-layout>