<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.articles.create') }}" class="rounded-xl bg-harvest px-4 py-2.5 text-sm font-bold text-forest">{{ __('admin.articles.add', [], 'id') }}</a>
    </div>

    @if ($articles->isEmpty())
        <p class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('admin.articles.empty', [], 'id') }}</p>
    @else
        <div class="overflow-x-auto rounded-2xl border border-forest/10 bg-white shadow-sm">
            <table class="w-full min-w-[40rem] text-left text-sm">
                <thead class="bg-cream text-xs uppercase tracking-wide text-charcoal/60"><tr>
                    <th class="px-5 py-4">{{ __('admin.articles.article_title', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.articles.status', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.market_prices.recorded_on', [], 'id') }}</th>
                    <th class="px-5 py-4"></th>
                </tr></thead>
                <tbody class="divide-y divide-forest/10">
                    @foreach ($articles as $article)
                        <tr>
                            <td class="px-5 py-4 font-bold">{{ $article->title }}</td>
                            <td class="px-5 py-4">{{ __('admin.articles.'.$article->status, [], 'id') }}</td>
                            <td class="px-5 py-4">{{ $article->updated_at->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="font-bold text-forest underline">{{ __('admin.articles.edit', [], 'id') }}</a>
                                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm(@js(__('admin.articles.confirm_delete', [], 'id')))" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold text-red-700 underline">{{ __('admin.articles.delete', [], 'id') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $articles->links() }}</div>
    @endif
</x-dashboard-layout>
