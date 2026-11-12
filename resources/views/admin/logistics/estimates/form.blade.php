<x-dashboard-layout :menu="$menu" :title="$title">
    <form method="POST" action="{{ $action }}" x-data="{ mode: '{{ old('mode', $freightEstimate->mode) }}' }" class="max-w-3xl space-y-5 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif
        <div>
            <label for="mode" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.estimates.mode', [], 'id') }}</label>
            <select id="mode" name="mode" x-model="mode" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                @foreach (['sea', 'air'] as $mode)
                    <option value="{{ $mode }}" @selected(old('mode', $freightEstimate->mode) === $mode)>{{ __('admin.logistics.estimates.'.$mode, [], 'id') }}</option>
                @endforeach
            </select>
            @error('mode')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="destination" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.estimates.destination', [], 'id') }}</label>
            <input id="destination" name="destination" value="{{ old('destination', $freightEstimate->destination) }}" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            @error('destination')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div x-cloak x-show="mode === 'sea'">
            <label for="loading_port_id" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.estimates.loading_port', [], 'id') }}</label>
            <select id="loading_port_id" name="loading_port_id" :disabled="mode !== 'sea'" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                <option value="">{{ __('admin.logistics.estimates.choose_port', [], 'id') }}</option>
                @foreach ($loadingPorts as $loadingPort)
                    <option value="{{ $loadingPort->id }}" @selected((string) old('loading_port_id', $freightEstimate->loading_port_id) === (string) $loadingPort->id)>{{ $loadingPort->name }} — {{ $loadingPort->city }}</option>
                @endforeach
            </select>
            @error('loading_port_id')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="transit_days_min" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.estimates.transit_days_min', [], 'id') }}</label>
                <input id="transit_days_min" name="transit_days_min" type="number" step="1" value="{{ old('transit_days_min', $freightEstimate->transit_days_min) }}" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
                @error('transit_days_min')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="transit_days_max" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.estimates.transit_days_max', [], 'id') }}</label>
                <input id="transit_days_max" name="transit_days_max" type="number" step="1" value="{{ old('transit_days_max', $freightEstimate->transit_days_max) }}" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
                @error('transit_days_max')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label for="rate_usd_per_kg" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.estimates.rate_usd_per_kg', [], 'id') }}</label>
            <input id="rate_usd_per_kg" name="rate_usd_per_kg" type="number" step="0.01" value="{{ old('rate_usd_per_kg', $freightEstimate->rate_usd_per_kg) }}" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            @error('rate_usd_per_kg')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="note" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.estimates.note', [], 'id') }}</label>
            <input id="note" name="note" value="{{ old('note', $freightEstimate->note) }}" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            @error('note')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="is_active" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.estimates.status', [], 'id') }}</label>
            <select id="is_active" name="is_active" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                @foreach ([1 => 'active', 0 => 'inactive'] as $value => $status)
                    <option value="{{ $value }}" @selected((string) old('is_active', (int) $freightEstimate->is_active) === (string) $value)>{{ __('admin.logistics.estimates.'.$status, [], 'id') }}</option>
                @endforeach
            </select>
            @error('is_active')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white">{{ __('admin.logistics.estimates.save', [], 'id') }}</button>
            <a href="{{ route('admin.freight-estimates.index') }}" class="rounded-xl border border-forest/20 px-5 py-3 text-sm font-bold text-forest">{{ __('admin.logistics.estimates.title', [], 'id') }}</a>
        </div>
    </form>
</x-dashboard-layout>