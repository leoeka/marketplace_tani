<x-dashboard-layout :menu="$menu" :title="$title">
    <form method="POST" action="{{ $action }}" class="max-w-3xl space-y-5 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif
        <div>
            <label for="name" class="mb-1.5 block text-sm font-bold">{{ __('admin.shipping_services.name', [], 'id') }}</label>
            <input id="name" name="name" value="{{ old('name', $shippingService->name) }}" required maxlength="255" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            @error('name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="type" class="mb-1.5 block text-sm font-bold">{{ __('admin.shipping_services.type', [], 'id') }}</label>
            <select id="type" name="type" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                @foreach (['local_courier', 'cold_chain'] as $type)
                    <option value="{{ $type }}" @selected(old('type', $shippingService->type ?? 'local_courier') === $type)>{{ __('admin.shipping_services.'.$type, [], 'id') }}</option>
                @endforeach
            </select>
            @error('type')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="is_active" class="mb-1.5 block text-sm font-bold">{{ __('admin.shipping_services.is_active', [], 'id') }}</label>
            <select id="is_active" name="is_active" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                @foreach ([1 => 'active', 0 => 'inactive'] as $value => $status)
                    <option value="{{ $value }}" @selected((string) old('is_active', (int) $shippingService->is_active) === (string) $value)>{{ __('admin.shipping_services.'.$status, [], 'id') }}</option>
                @endforeach
            </select>
            @error('is_active')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white">{{ __('admin.shipping_services.save', [], 'id') }}</button>
            <a href="{{ route('admin.shipping-services.index') }}" class="rounded-xl border border-forest/20 px-5 py-3 text-sm font-bold text-forest">{{ __('admin.shipping_services.back', [], 'id') }}</a>
        </div>
    </form>
</x-dashboard-layout>
