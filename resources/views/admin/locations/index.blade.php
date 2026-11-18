<x-dashboard-layout :menu="$menu" :title="$title">
    <p class="mb-6 text-sm leading-6 text-charcoal/65">{{ __('admin.locations.description') }}</p>

    @if ($users->isEmpty())
        <div class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('admin.locations.empty') }}</div>
    @else
        <div class="overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] divide-y divide-forest/10 text-left text-sm">
                    <thead class="bg-cream/70 text-xs uppercase tracking-wide text-charcoal/60">
                        <tr>
                            <th class="px-5 py-4">{{ __('admin.locations.name') }}</th>
                            <th class="px-5 py-4">{{ __('admin.locations.role') }}</th>
                            <th class="px-5 py-4">{{ __('admin.locations.consent') }}</th>
                            <th class="px-5 py-4">{{ __('admin.locations.last_location') }}</th>
                            <th class="px-5 py-4">{{ __('admin.locations.captured_at') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-forest/10">
                        @foreach ($users as $u)
                            @php $loc = $u->latestLocation; @endphp
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-bold text-charcoal">{{ $u->name }}</p>
                                    <p class="text-xs text-charcoal/60">{{ $u->email }}</p>
                                </td>
                                <td class="px-5 py-4 text-charcoal/70">{{ __('admin.locations.roles.' . $u->role) }}</td>
                                <td class="px-5 py-4">
                                    @if ($u->location_consent === 'granted')
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-900">{{ __('admin.locations.granted') }}</span>
                                    @elseif ($u->location_consent === 'denied')
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">{{ __('admin.locations.denied') }}</span>
                                    @else
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">{{ __('admin.locations.not_asked') }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if ($loc)
                                        <a href="https://www.google.com/maps?q={{ $loc->latitude }},{{ $loc->longitude }}" target="_blank" rel="noopener noreferrer" class="font-bold text-forest underline underline-offset-4">
                                            {{ number_format($loc->latitude, 5, '.', '') }}, {{ number_format($loc->longitude, 5, '.', '') }}
                                        </a>
                                    @else
                                        <span class="text-charcoal/40">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-charcoal/70">
                                    {{ $loc ? $loc->captured_at->timezone(config('app.timezone'))->format('d/m/Y H:i') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $users->links() }}</div>
    @endif
</x-dashboard-layout>