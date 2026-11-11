<x-dashboard-layout :menu="$menu" :title="$title">
    @php($selectedProductIds = old('product_ids', $certificate->products->modelKeys()))
    @php($selectedProductIds = is_array($selectedProductIds) ? $selectedProductIds : [])
    <form method="POST" action="{{ $certificate->exists ? route('admin.certificates.update', $certificate) : route('admin.certificates.store') }}" enctype="multipart/form-data" class="max-w-3xl space-y-5 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($certificate->exists)
            @method('PUT')
        @endif
        <div>
            <label for="name" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('admin.certificates.name', [], 'id') }}</label>
            <input id="name" name="name" value="{{ old('name', $certificate->name) }}" required maxlength="255" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="file" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('admin.certificates.file', [], 'id') }}</label>
            @if ($certificate->file_path)
                <p class="mb-2 text-sm text-charcoal/65">{{ __('admin.certificates.current_file', [], 'id') }}: <a href="{{ asset('storage/'.$certificate->file_path) }}" target="_blank" rel="noopener" class="font-semibold text-forest underline">{{ __('admin.certificates.view_file', [], 'id') }}</a></p>
            @endif
            <input id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png" @required(! $certificate->file_path) class="w-full rounded-xl border border-forest/15 bg-white p-2 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-cream file:px-3 file:py-2 file:font-bold file:text-forest">
            <p class="mt-1 text-xs text-charcoal/55">{{ __('admin.certificates.file_help', [], 'id') }}</p>
            @error('file') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <fieldset>
            <legend class="mb-2 block text-sm font-bold text-charcoal">{{ __('admin.certificates.associated_products', [], 'id') }}</legend>
            <p class="mb-3 text-xs leading-5 text-charcoal/55">{{ __('admin.certificates.products_help', [], 'id') }}</p>
            @error('product_ids') <p class="mb-2 text-sm text-red-700">{{ $message }}</p> @enderror
            @error('product_ids.*') <p class="mb-2 text-sm text-red-700">{{ $message }}</p> @enderror
            <div class="grid max-h-64 gap-2 overflow-y-auto rounded-xl border border-forest/10 p-3 sm:grid-cols-2">
                @foreach ($products as $product)
                    <label class="flex items-start gap-2 rounded-lg px-2 py-2 text-sm text-charcoal hover:bg-cream">
                        <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" @checked(in_array($product->id, $selectedProductIds, false)) class="mt-0.5 rounded border-forest/25 text-forest focus:ring-forest">
                        <span>{{ $product->name }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white hover:bg-forest/90">{{ __('admin.certificates.save', [], 'id') }}</button>
            <a href="{{ route('admin.certificates.index') }}" class="rounded-xl border border-forest/20 px-5 py-3 text-sm font-bold text-forest">{{ __('admin.certificates.back', [], 'id') }}</a>
        </div>
    </form>
</x-dashboard-layout>