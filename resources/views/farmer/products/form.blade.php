<x-dashboard-layout :menu="$menu" :title="$title">
    @php($verified = auth()->user()->farmerProfile()->whereNotNull('verified_at')->exists())

    @unless ($verified)
        <div class="mb-6 rounded-2xl border border-harvest/50 bg-amber-50 px-5 py-4 text-sm font-semibold text-charcoal" role="status">
            {{ __('farmer.dashboard.verification_pending', [], 'id') }}. {{ __('farmer.products.verified_notice', [], 'id') }}
        </div>
    @endunless

    <form method="POST" action="{{ $isEditing ? route('farmer.products.update', $product) : route('farmer.products.store') }}" enctype="multipart/form-data" class="max-w-4xl space-y-6 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-8">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="name" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.fields.name', [], 'id') }}</label>
                <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required maxlength="255" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
                @error('name') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="category_id" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.fields.category', [], 'id') }}</label>
                <select id="category_id" name="category_id" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                    <option value="">—</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="description" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.fields.description', [], 'id') }}</label>
            <textarea id="description" name="description" rows="5" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">{{ old('description', $product->description) }}</textarea>
            @error('description') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <div>
                <label for="unit" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.fields.unit', [], 'id') }}</label>
                <select id="unit" name="unit" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                    <option value="kg" @selected(old('unit', $product->unit ?? 'kg') === 'kg')>{{ __('farmer.products.unit_kg', [], 'id') }}</option>
                    <option value="ton" @selected(old('unit', $product->unit) === 'ton')>{{ __('farmer.products.unit_ton', [], 'id') }}</option>
                </select>
                @error('unit') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="price_idr" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.fields.price', [], 'id') }}</label>
                <input id="price_idr" name="price_idr" type="number" min="0" step="1" value="{{ old('price_idr', $product->price_idr) }}" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
                @error('price_idr') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="stock" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.fields.stock', [], 'id') }}</label>
                <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product->stock) }}" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
                @error('stock') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="space-y-4 rounded-xl border border-forest/10 bg-cream/40 p-4">
            <label for="is_preorder" class="flex items-start gap-3 text-sm font-bold text-charcoal">
                <input id="is_preorder" name="is_preorder" type="checkbox" value="1" @checked(old('is_preorder', $product->isPreorder())) class="mt-0.5 rounded border-forest/25 text-forest focus:ring-forest">
                <span>{{ __('farmer.products.preorder_option', [], 'id') }}</span>
            </label>
            <div>
                <label for="harvest_date" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.products.preorder_date', [], 'id') }}</label>
                <input id="harvest_date" name="harvest_date" type="date" min="{{ today()->toDateString() }}" value="{{ old('harvest_date', $product->isPreorder() ? $product->harvest_date->format('Y-m-d') : '') }}" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest sm:max-w-xs">
                @error('harvest_date') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
        </div>

        @if ($product->images->isNotEmpty())
            <div>
                <p class="mb-3 text-sm font-bold text-charcoal">{{ __('farmer.products.existing_images', [], 'id') }}</p>
                <div class="flex flex-wrap gap-3">
                    @foreach ($product->images as $image)
                        <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-xl object-cover">
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-charcoal/60">{{ __('farmer.products.replace_images_hint', [], 'id') }}</p>
            </div>
        @endif

        <div>
            <label for="photos" class="mb-2 block text-sm font-bold text-charcoal">{{ __('farmer.products.images', [], 'id') }}</label>
            <input id="photos" name="photos[]" type="file" accept=".jpg,.png,.webp" multiple @required(! $isEditing || $product->images->isEmpty()) class="block w-full text-sm text-charcoal file:mr-4 file:rounded-lg file:border-0 file:bg-cream file:px-4 file:py-2 file:font-semibold file:text-forest">
            <p class="mt-2 text-xs text-charcoal/60">{{ __('farmer.products.images_hint', [], 'id') }}</p>
            @error('photos') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
            @foreach ($errors->get('photos.*') as $messages)
                @foreach ($messages as $message) <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @endforeach
            @endforeach
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-forest/10 pt-5 sm:flex-row sm:justify-end">
            <button type="submit" name="action" value="draft" class="rounded-xl border border-forest/20 px-5 py-3 text-sm font-bold text-forest hover:bg-cream">{{ __('farmer.products.save_draft', [], 'id') }}</button>
            @if ($verified)
                <button type="submit" name="action" value="submit" class="rounded-xl bg-harvest px-5 py-3 text-sm font-extrabold text-charcoal hover:bg-amber-400">{{ __('farmer.products.submit', [], 'id') }}</button>
            @endif
        </div>
    </form>
</x-dashboard-layout>