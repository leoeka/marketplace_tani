<x-dashboard-layout :menu="$menu" :title="$title">
    <form method="POST" action="{{ $action }}" class="max-w-3xl space-y-5 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif
        <div>
            <label for="title" class="mb-1.5 block text-sm font-bold">{{ __('admin.articles.article_title', [], 'id') }}</label>
            <input id="title" name="title" value="{{ old('title', $article->title) }}" required maxlength="255" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            @error('title')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="excerpt" class="mb-1.5 block text-sm font-bold">{{ __('admin.articles.excerpt', [], 'id') }}</label>
            <textarea id="excerpt" name="excerpt" rows="3" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">{{ old('excerpt', $article->excerpt) }}</textarea>
            @error('excerpt')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="body" class="mb-1.5 block text-sm font-bold">{{ __('admin.articles.body', [], 'id') }}</label>
            <textarea id="body" name="body" rows="12" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">{{ old('body', $article->body) }}</textarea>
            @error('body')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="status" class="mb-1.5 block text-sm font-bold">{{ __('admin.articles.status', [], 'id') }}</label>
            <select id="status" name="status" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                @foreach (['draft', 'published'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $article->status ?? 'draft') === $status)>{{ __('admin.articles.'.$status, [], 'id') }}</option>
                @endforeach
            </select>
            @error('status')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white">{{ __('admin.articles.save', [], 'id') }}</button>
            <a href="{{ route('admin.articles.index') }}" class="rounded-xl border border-forest/20 px-5 py-3 text-sm font-bold text-forest">{{ __('admin.articles.back', [], 'id') }}</a>
        </div>
    </form>
</x-dashboard-layout>
