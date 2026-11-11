@props(['paginator'])
@if ($paginator->lastPage() > 1)
    <nav aria-label="{{ __('export.pagination.label') }}" class="flex flex-wrap items-center justify-center gap-2 pt-8">
        @if ($paginator->previousPageUrl())
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-lg border border-forest/15 px-3 py-2 text-sm font-semibold text-forest hover:bg-[#f4f7f3]">{{ __('export.pagination.previous') }}</a>
        @endif
        @for ($page = 1; $page <= $paginator->lastPage(); $page++)
            @if ($page === $paginator->currentPage())
                <span aria-current="page" class="rounded-lg bg-forest px-3 py-2 text-sm font-bold text-white">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}" class="rounded-lg border border-forest/15 px-3 py-2 text-sm font-semibold text-forest hover:bg-[#f4f7f3]" aria-label="{{ __('export.pagination.page', ['page' => $page]) }}">{{ $page }}</a>
            @endif
        @endfor
        @if ($paginator->nextPageUrl())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-lg border border-forest/15 px-3 py-2 text-sm font-semibold text-forest hover:bg-[#f4f7f3]">{{ __('export.pagination.next') }}</a>
        @endif
    </nav>
@endif