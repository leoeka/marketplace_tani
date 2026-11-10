<x-marketplace.layout>
    <article class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('marketplace.articles.index') }}" class="text-sm font-bold text-forest underline underline-offset-4">← {{ __('marketplace.education.back', [], 'id') }}</a>
        <header class="mt-7 border-b border-forest/10 pb-6">
            <time class="text-sm font-semibold text-forest/60" datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->format('d/m/Y') }}</time>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ $article->title }}</h1>
            @if ($article->excerpt)
                <p class="mt-4 text-lg leading-7 text-charcoal/70">{{ $article->excerpt }}</p>
            @endif
        </header>
        <div class="mt-7 space-y-5 text-base leading-8 text-charcoal/80">
            @foreach (preg_split('/\R\s*\R/u', trim($article->body)) ?: [] as $paragraph)
                @if (trim($paragraph) !== '')
                    <p>{{ trim($paragraph) }}</p>
                @endif
            @endforeach
        </div>
    </article>
</x-marketplace.layout>
