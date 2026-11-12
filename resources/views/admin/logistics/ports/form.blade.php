<x-dashboard-layout :menu="$menu" :title="$title">
    <form method="POST" action="{{ $action }}" class="max-w-3xl space-y-5 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif
        <div>
            <label for="name" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.ports.name', [], 'id') }}</label>
            <input id="name" name="name" value="{{ old('name', $loadingPort->name) }}" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            @error('name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="city" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.ports.city', [], 'id') }}</label>
            <input id="city" name="city" value="{{ old('city', $loadingPort->city) }}" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            @error('city')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="is_active" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.ports.status', [], 'id') }}</label>
                <select id="is_active" name="is_active" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                    @foreach ([1 => 'active', 0 => 'inactive'] as $value => $status)
                        <option value="{{ $value }}" @selected((string) old('is_active', (int) $loadingPort->is_active) === (string) $value)>{{ __('admin.logistics.ports.'.$status, [], 'id') }}</option>
                    @endforeach
                </select>
                @error('is_active')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="sort_order" class="mb-1.5 block text-sm font-bold">{{ __('admin.logistics.ports.sort_order', [], 'id') }}</label>
                <input id="sort_order" name="sort_order" type="number" step="1" value="{{ old('sort_order', $loadingPort->sort_order) }}" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
                @error('sort_order')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white">{{ __('admin.logistics.ports.save', [], 'id') }}</button>
            <a href="{{ route('admin.loading-ports.index') }}" class="rounded-xl border border-forest/20 px-5 py-3 text-sm font-bold text-forest">{{ __('admin.logistics.ports.back', [], 'id') }}</a>
        </div>
    </form>
</x-dashboard-layout>