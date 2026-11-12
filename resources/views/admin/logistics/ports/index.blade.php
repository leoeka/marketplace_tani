<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6 flex flex-wrap justify-end gap-3">
        <a href="{{ route('admin.freight-estimates.index') }}" class="rounded-xl border border-forest/20 px-4 py-2.5 text-sm font-bold text-forest">{{ __('admin.logistics.estimates_link', [], 'id') }}</a>
        <a href="{{ route('admin.loading-ports.create') }}" class="rounded-xl bg-harvest px-4 py-2.5 text-sm font-bold text-forest">{{ __('admin.logistics.ports.add', [], 'id') }}</a>
    </div>

    @if ($loadingPorts->isEmpty())
        <p class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('admin.logistics.ports.empty', [], 'id') }}</p>
    @else
        <div class="overflow-x-auto rounded-2xl border border-forest/10 bg-white shadow-sm">
            <table class="w-full min-w-[42rem] text-left text-sm">
                <thead class="bg-cream text-xs uppercase tracking-wide text-charcoal/60"><tr>
                    <th class="px-5 py-4">{{ __('admin.logistics.ports.name', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.logistics.ports.city', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.logistics.ports.status', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.logistics.ports.sort_order', [], 'id') }}</th>
                    <th class="px-5 py-4"><span class="sr-only">{{ __('admin.logistics.ports.edit', [], 'id') }}</span></th>
                </tr></thead>
                <tbody class="divide-y divide-forest/10">
                    @foreach ($loadingPorts as $loadingPort)
                        <tr>
                            <td class="px-5 py-4 font-bold">{{ $loadingPort->name }}</td>
                            <td class="px-5 py-4">{{ $loadingPort->city }}</td>
                            <td class="px-5 py-4">{{ __('admin.logistics.ports.'.($loadingPort->is_active ? 'active' : 'inactive'), [], 'id') }}</td>
                            <td class="px-5 py-4">{{ $loadingPort->sort_order }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.loading-ports.edit', $loadingPort) }}" class="font-bold text-forest underline">{{ __('admin.logistics.ports.edit', [], 'id') }}</a>
                                    <form method="POST" action="{{ route('admin.loading-ports.destroy', $loadingPort) }}" onsubmit="return confirm(@js(__('admin.logistics.ports.confirm_delete', [], 'id')))" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold text-red-700 underline">{{ __('admin.logistics.ports.delete', [], 'id') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $loadingPorts->links() }}</div>
    @endif
</x-dashboard-layout>