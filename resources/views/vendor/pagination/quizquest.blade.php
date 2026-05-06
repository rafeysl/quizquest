{{-- resources/views/vendor/pagination/quizquest.blade.php --}}
@if ($paginator->hasPages())
    <div class="qq-pagination" role="navigation" aria-label="Pagination">

        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <button class="qq-page-btn" disabled style="opacity:0.4; cursor:not-allowed;">
                <i class="ti ti-chevron-left"></i>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="qq-page-btn" aria-label="Halaman sebelumnya">
                <i class="ti ti-chevron-left"></i>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="qq-page-btn" style="opacity:0.4;">…</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="qq-page-btn active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="qq-page-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="qq-page-btn" aria-label="Halaman berikutnya">
                <i class="ti ti-chevron-right"></i>
            </a>
        @else
            <button class="qq-page-btn" disabled style="opacity:0.4; cursor:not-allowed;">
                <i class="ti ti-chevron-right"></i>
            </button>
        @endif

    </div>
@endif