<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6 max-w-3xl">
        <p class="text-sm leading-6 text-charcoal/65">{{ __('farmer.profile.description', [], 'id') }}</p>
    </div>

    <form method="POST" action="{{ route('farmer.profile.update') }}" enctype="multipart/form-data" class="max-w-3xl space-y-6 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-8">
        @csrf
        @method('PUT')

        <div>
            <label for="shop_name" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.fields.shop_name', [], 'id') }}</label>
            <input id="shop_name" name="shop_name" type="text" value="{{ old('shop_name', $profile?->shop_name) }}" required maxlength="255" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
            @error('shop_name') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="group_name" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.fields.group_name', [], 'id') }}</label>
            <input id="group_name" name="group_name" type="text" value="{{ old('group_name', $profile?->group_name) }}" maxlength="255" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
            @error('group_name') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="region" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.fields.region', [], 'id') }}</label>
            <input id="region" name="region" type="text" value="{{ old('region', $profile?->region) }}" required maxlength="255" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
            @error('region') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="story" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.fields.story', [], 'id') }}</label>
            <textarea id="story" name="story" rows="5" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">{{ old('story', $profile?->story) }}</textarea>
            @error('story') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        @if ($profile?->photo)
            <div>
                <p class="mb-2 text-sm font-bold text-charcoal">{{ __('farmer.profile.current_photo', [], 'id') }}</p>
                <img src="{{ asset('storage/'.$profile->photo) }}" alt="{{ __('farmer.profile.current_photo', [], 'id') }}" class="h-28 w-28 rounded-2xl object-cover">
            </div>
        @endif

        <div>
            <label for="photo" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.fields.photo', [], 'id') }}</label>
            <input id="photo" name="photo" type="file" accept=".jpg,.png,.webp" class="block w-full text-sm text-charcoal file:mr-4 file:rounded-lg file:border-0 file:bg-cream file:px-4 file:py-2 file:font-semibold file:text-forest">
            <p class="mt-2 text-xs text-charcoal/60">{{ __('farmer.profile.photo_hint', [], 'id') }}</p>
            @error('photo') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end border-t border-forest/10 pt-5">
            <button type="submit" class="rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white transition hover:bg-forest/90">{{ __('farmer.profile.save', [], 'id') }}</button>
        </div>
    </form>
</x-dashboard-layout>