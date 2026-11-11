<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.certificates.create') }}" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-bold text-white hover:bg-forest/90">{{ __('admin.certificates.add', [], 'id') }}</a>
    </div>

    @if ($certificates->isEmpty())
        <p class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('admin.certificates.empty', [], 'id') }}</p>
    @else
        <div class="overflow-x-auto rounded-2xl border border-forest/10 bg-white shadow-sm">
            <table class="w-full min-w-[42rem] text-left text-sm">
                <thead class="bg-cream text-xs uppercase tracking-wide text-charcoal/60">
                    <tr>
                        <th class="px-5 py-4">{{ __('admin.certificates.name', [], 'id') }}</th>
                        <th class="px-5 py-4">{{ __('admin.certificates.file', [], 'id') }}</th>
                        <th class="px-5 py-4">{{ __('admin.certificates.products_count', [], 'id') }}</th>
                        <th class="px-5 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-forest/10">
                    @foreach ($certificates as $certificate)
                        <tr>
                            <td class="px-5 py-4 font-bold text-charcoal">{{ $certificate->name }}</td>
                            <td class="px-5 py-4">
                                @if ($certificate->file_path)
                                    <a href="{{ asset('storage/'.$certificate->file_path) }}" target="_blank" rel="noopener" class="font-semibold text-forest underline">{{ __('admin.certificates.view_file', [], 'id') }}</a>
                                @else
                                    <span class="text-charcoal/50">{{ __('admin.certificates.file_missing', [], 'id') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-charcoal/65">{{ $certificate->products_count }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.certificates.edit', $certificate) }}" class="font-bold text-forest underline">{{ __('admin.certificates.edit', [], 'id') }}</a>
                                    <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}" onsubmit="return confirm(@js(__('admin.certificates.confirm_delete', [], 'id')))" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold text-red-700 underline">{{ __('admin.certificates.delete', [], 'id') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $certificates->links() }}</div>
    @endif
</x-dashboard-layout>