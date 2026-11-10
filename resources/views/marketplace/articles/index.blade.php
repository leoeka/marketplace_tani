<x-marketplace.layout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-forest/60">{{ __('marketplace.education.eyebrow', [], 'id') }}</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ __('marketplace.education.title', [], 'id') }}</h1>
            <p class="mt-2 text-sm leading-6 text-charcoal/65 sm:text-base">{{ __('marketplace.education.description', [], 'id') }}</p>
        </div>

        @if ($articles->isEmpty())
            <p class="mt-7 rounded-3xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('marketplace.education.empty', [], 'id') }}</p>
        @else
            <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <article class="flex flex-col rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-6">
                        <time class="text-xs font-semibold text-forest/60" datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->format('d/m/Y') }}</time>
                        <h2 class="mt-3 text-xl font-extrabold text-charcoal">{{ $article->title }}</h2>
                        @if ($article->excerpt)
                            <p class="mt-3 flex-1 whitespace-pre-line text-sm leading-6 text-charcoal/70">{{ $article->excerpt }}</p>
                        @endif
                        <a href="{{ route('marketplace.articles.show', $article->slug) }}" class="mt-5 inline-flex font-bold text-forest underline underline-offset-4">{{ __('marketplace.education.read', [], 'id') }} <span class="ml-2" aria-hidden="true">→</span></a>
                    </article>
                @endforeach
            </div>
            <div class="mt-8">{{ $articles->links() }}</div>
        @endif
    </section>
</x-marketplace.layout>
