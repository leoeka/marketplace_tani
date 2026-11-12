<x-export.layout :title="__('export.logistics.meta_title')">
    <section class="bg-[#f4f7f3]">
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16 lg:px-10">
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-forest/65">{{ __('export.logistics.eyebrow') }}</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-forest sm:text-4xl">{{ __('export.logistics.title') }}</h1>
            <p class="mt-3 max-w-2xl text-base leading-7 text-charcoal/65">{{ __('export.logistics.description') }}</p>
        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-6 px-5 py-10 sm:px-8 sm:py-14 lg:grid-cols-2 lg:px-10">
        <article class="rounded-2xl border border-forest/10 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-extrabold text-forest">{{ __('export.logistics.sea_title') }}</h2>
            <p class="mt-3 leading-7 text-charcoal/70">{{ __('export.logistics.sea_description') }}</p>
        </article>
        <article class="rounded-2xl border border-forest/10 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-extrabold text-forest">{{ __('export.logistics.air_title') }}</h2>
            <p class="mt-3 leading-7 text-charcoal/70">{{ __('export.logistics.air_description') }}</p>
        </article>
    </section>

    <section class="mx-auto max-w-7xl px-5 pb-10 sm:px-8 sm:pb-14 lg:px-10">
        <div class="rounded-2xl border border-forest/10 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-extrabold text-forest">{{ __('export.logistics.ports_title') }}</h2>
            @if ($loadingPorts->isEmpty())
                <p class="mt-4 rounded-xl border border-dashed border-forest/20 bg-[#f8faf8] px-5 py-8 text-center text-sm text-charcoal/60">{{ __('export.logistics.ports_empty') }}</p>
            @else
                <ul class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($loadingPorts as $loadingPort)
                        <li class="rounded-xl bg-[#f4f7f3] p-4">
                            <p class="font-bold text-forest">{{ $loadingPort->name }}</p>
                            <p class="mt-1 text-sm text-charcoal/65">{{ __('export.logistics.city') }}: {{ $loadingPort->city }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 pb-12 sm:px-8 sm:pb-16 lg:px-10" x-data="{ mode: '{{ $mode }}' }">
        <div class="rounded-2xl border border-forest/10 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-extrabold text-forest">{{ __('export.logistics.calculator_title') }}</h2>
            <p class="mt-2 text-sm leading-6 text-charcoal/65">{{ __('export.logistics.calculator_description') }}</p>

            <form method="GET" action="{{ route('export.logistics.index') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="mode" class="mb-1.5 block text-sm font-bold">{{ __('export.logistics.mode') }}</label>
                    <select id="mode" name="mode" x-model="mode" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                        <option value="sea">{{ __('export.logistics.sea_option') }}</option>
                        <option value="air">{{ __('export.logistics.air_option') }}</option>
                    </select>
                    @error('mode')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div x-cloak x-show="mode === 'sea'">
                    <label for="loading_port_id" class="mb-1.5 block text-sm font-bold">{{ __('export.logistics.loading_port') }}</label>
                    <select id="loading_port_id" name="loading_port_id" :disabled="mode !== 'sea'" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                        <option value="">{{ __('export.logistics.choose_port') }}</option>
                        @foreach ($loadingPorts as $loadingPort)
                            <option value="{{ $loadingPort->id }}" @selected((string) ($filters['loading_port_id'] ?? '') === (string) $loadingPort->id)>{{ $loadingPort->name }} — {{ $loadingPort->city }}</option>
                        @endforeach
                    </select>
                    @error('loading_port_id')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div x-cloak x-show="mode === 'sea'" class="sm:col-span-2">
                    <label for="destination_sea" class="mb-1.5 block text-sm font-bold">{{ __('export.logistics.destination') }}</label>
                    <select id="destination_sea" name="destination" :disabled="mode !== 'sea'" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                        <option value="">{{ __('export.logistics.choose_destination') }}</option>
                        @foreach ($destinationsByMode['sea'] as $destination)
                            <option value="{{ $destination }}" @selected($mode === 'sea' && ($filters['destination'] ?? '') === $destination)>{{ $destination }}</option>
                        @endforeach
                    </select>
                    @if ($destinationsByMode['sea']->isEmpty())<p class="mt-1 text-sm text-charcoal/60">{{ __('export.logistics.destination_empty') }}</p>@endif
                    @error('destination')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div x-cloak x-show="mode === 'air'" class="sm:col-span-2">
                    <label for="destination_air" class="mb-1.5 block text-sm font-bold">{{ __('export.logistics.destination') }}</label>
                    <select id="destination_air" name="destination" :disabled="mode !== 'air'" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                        <option value="">{{ __('export.logistics.choose_destination') }}</option>
                        @foreach ($destinationsByMode['air'] as $destination)
                            <option value="{{ $destination }}" @selected($mode === 'air' && ($filters['destination'] ?? '') === $destination)>{{ $destination }}</option>
                        @endforeach
                    </select>
                    @if ($destinationsByMode['air']->isEmpty())<p class="mt-1 text-sm text-charcoal/60">{{ __('export.logistics.destination_empty') }}</p>@endif
                    @error('destination')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="weight" class="mb-1.5 block text-sm font-bold">{{ __('export.logistics.weight') }}</label>
                    <input id="weight" name="weight" type="number" step="any" value="{{ $filters['weight'] ?? '' }}" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
                    @error('weight')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="unit" class="mb-1.5 block text-sm font-bold">{{ __('export.logistics.unit') }}</label>
                    <select id="unit" name="unit" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                        @foreach (['kg', 'ton'] as $unit)
                            <option value="{{ $unit }}" @selected(($filters['unit'] ?? 'kg') === $unit)>{{ __('export.logistics.'.$unit) }}</option>
                        @endforeach
                    </select>
                    @error('unit')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white transition hover:bg-forest/90">{{ __('export.logistics.calculate') }}</button>
                </div>
            </form>

            @if ($wasCalculated)
                <section class="mt-8 rounded-xl border border-forest/15 bg-[#f4f7f3] p-5" aria-live="polite">
                    <h3 class="text-lg font-extrabold text-forest">{{ __('export.logistics.result_title') }}</h3>
                    @if ($result)
                        <p class="mt-3 text-sm text-charcoal/65">{{ __('export.logistics.transit_time') }}</p>
                        <p class="mt-1 font-bold text-charcoal">{{ $result['duration'] }}</p>
                        @if ($result['cost'] !== null)
                            <p class="mt-3 font-bold text-forest">{{ __('export.logistics.indicative_cost', ['amount' => $result['cost']]) }}</p>
                        @else
                            <p class="mt-3 text-sm text-charcoal/70">{{ __('export.logistics.rate_unavailable') }}</p>
                        @endif
                    @else
                        <p class="mt-3 text-sm leading-6 text-charcoal/70">{{ __('export.logistics.no_estimate') }}</p>
                    @endif
                    <p class="mt-4 border-t border-forest/10 pt-4 text-xs leading-5 text-charcoal/60">{{ __('export.logistics.disclaimer') }}</p>
                </section>
            @endif
            @if (! $wasCalculated)
                <p class="mt-6 rounded-xl bg-[#f8faf8] p-4 text-xs leading-5 text-charcoal/60">{{ __('export.logistics.disclaimer') }}</p>
            @endif
            <a href="{{ route('export.rfq.create') }}" class="mt-5 inline-flex rounded-lg bg-forest px-4 py-2.5 text-sm font-bold text-white transition hover:bg-forest/90">{{ __('export.logistics.request_quote') }}</a>
        </div>
    </section>
</x-export.layout>