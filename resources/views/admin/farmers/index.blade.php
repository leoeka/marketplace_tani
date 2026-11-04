<x-dashboard-layout :menu="$menu" :title="$title">
    <p class="mb-6 text-sm leading-6 text-charcoal/65">{{ __('admin.farmers.description', [], 'id') }}</p>

    @if ($farmers->isEmpty())
        <div class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('admin.farmers.empty', [], 'id') }}</div>
    @else
        <div class="overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] divide-y divide-forest/10 text-left text-sm">
                    <thead class="bg-cream/70 text-xs uppercase tracking-wide text-charcoal/60">
                        <tr>
                            <th class="px-5 py-4">{{ __('admin.farmers.shop_name', [], 'id') }}</th>
                            <th class="px-5 py-4">{{ __('admin.farmers.farmer_name', [], 'id') }}</th>
                            <th class="px-5 py-4">{{ __('admin.farmers.region', [], 'id') }}</th>
                            <th class="px-5 py-4">{{ __('admin.farmers.status', [], 'id') }}</th>
                            <th class="px-5 py-4" colspan="2"><span class="sr-only">{{ __('admin.farmers.detail', [], 'id') }}</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-forest/10">
                        @foreach ($farmers as $farmerProfile)
                            <tr>
                                <td class="px-5 py-4 font-bold text-charcoal">{{ $farmerProfile->shop_name }}</td>
                                <td class="px-5 py-4 text-charcoal/70">{{ $farmerProfile->user->name }}</td>
                                <td class="px-5 py-4 text-charcoal/70">{{ $farmerProfile->region }}</td>
                                <td class="px-5 py-4">
                                    @if ($farmerProfile->verified_at)
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-900">{{ __('admin.farmers.verified', [], 'id') }}</span>
                                    @else
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">{{ __('admin.farmers.pending', [], 'id') }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.farmers.show', $farmerProfile) }}" class="font-bold text-forest underline underline-offset-4">{{ __('admin.farmers.detail', [], 'id') }}</a>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if ($farmerProfile->verified_at)
                                        <form method="POST" action="{{ route('admin.farmers.revoke', $farmerProfile) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="whitespace-nowrap rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-50">{{ __('admin.farmers.revoke', [], 'id') }}</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.farmers.verify', $farmerProfile) }}">
                                            @csrf
                                            <button type="submit" class="whitespace-nowrap rounded-lg bg-forest px-3 py-2 text-xs font-bold text-white hover:bg-forest/90">{{ __('admin.farmers.verify', [], 'id') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $farmers->links() }}</div>
    @endif
</x-dashboard-layout>