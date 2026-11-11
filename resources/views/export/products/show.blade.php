<x-export.layout :title="__('export.product_detail.meta_title', ['name' => $product->exportDetails->name_en])">
    <article class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14 lg:px-10">
        <a href="{{ route('export.products.index') }}" class="inline-flex text-sm font-bold text-forest underline decoration-forest/30 underline-offset-4 hover:decoration-forest">{{ __('export.product_detail.back_to_products') }}</a>

        <div class="mt-7 grid gap-10 lg:grid-cols-[minmax(0,1.05fr)_minmax(20rem,0.95fr)] lg:gap-14">
            <div class="grid content-start gap-3 sm:grid-cols-2">
                @forelse ($product->images as $image)
                    <img src="{{ asset('storage/'.$image->path) }}" alt="{{ __('export.product_detail.image_alt', ['name' => $product->exportDetails->name_en]) }}" class="aspect-[4/3] w-full rounded-xl bg-[#edf2ed] object-cover {{ $loop->first ? 'sm:col-span-2' : '' }}">
                @empty
                    <div class="flex aspect-[4/3] items-center justify-center rounded-xl bg-[#edf2ed] px-6 text-center text-sm text-charcoal/55 sm:col-span-2">{{ __('export.product_detail.no_image') }}</div>
                @endforelse
            </div>

            <div class="space-y-7">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-forest/65"><x-export.category-label :category="$product->category" /></p>
                    <h1 class="mt-3 text-3xl font-extrabold leading-tight tracking-tight text-forest sm:text-4xl">{{ $product->exportDetails->name_en }}</h1>
                    <p class="mt-5 whitespace-pre-line text-base leading-8 text-charcoal/70">{{ $product->exportDetails->description_en }}</p>
                </div>

                @if ($product->certificates->isNotEmpty())
                    <section aria-labelledby="export-certifications" class="rounded-xl border border-forest/10 bg-[#f8faf8] p-5">
                        <h2 id="export-certifications" class="text-lg font-extrabold text-forest">{{ __('export.product_detail.certifications') }}</h2>
                        <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                            @foreach ($product->certificates as $certificate)
                                <li class="flex items-center justify-between gap-3 rounded-lg bg-white px-3 py-2 text-sm">
                                    <span>
                                        <span class="block font-semibold text-charcoal">{{ $certificate->name }}</span>
                                        <span class="mt-0.5 block text-xs text-charcoal/55">{{ __('export.certifications.types.'.$certificate->type) }}</span>
                                    </span>
                                    <a href="{{ asset('storage/'.$certificate->file_path) }}" target="_blank" rel="noopener" class="shrink-0 font-bold text-forest underline">{{ __('export.product_detail.view_certificate') }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <section aria-labelledby="export-specifications" class="overflow-hidden rounded-xl border border-forest/10">
                    <h2 id="export-specifications" class="bg-[#f4f7f3] px-5 py-4 text-lg font-extrabold text-forest">{{ __('export.product_detail.specifications') }}</h2>
                    <dl class="divide-y divide-forest/10 px-5">
                        @if (filled($product->exportDetails->hs_code))
                            <div class="grid gap-1 py-4 sm:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] sm:gap-5">
                                <dt class="text-sm text-charcoal/55">{{ __('export.product_detail.hs_code') }}</dt>
                                <dd class="text-sm font-semibold text-charcoal">{{ $product->exportDetails->hs_code }}</dd>
                            </div>
                        @endif
                        @if ($product->exportDetails->moisture_percent !== null)
                            <div class="grid gap-1 py-4 sm:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] sm:gap-5">
                                <dt class="text-sm text-charcoal/55">{{ __('export.product_detail.moisture_content') }}</dt>
                                <dd class="text-sm font-semibold text-charcoal">{{ $product->exportDetails->moisture_percent }}%</dd>
                            </div>
                        @endif
                        @if (filled($product->exportDetails->size_spec))
                            <div class="grid gap-1 py-4 sm:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] sm:gap-5">
                                <dt class="text-sm text-charcoal/55">{{ __('export.product_detail.size_specification') }}</dt>
                                <dd class="text-sm font-semibold text-charcoal">{{ $product->exportDetails->size_spec }}</dd>
                            </div>
                        @endif
                        @if ($product->exportDetails->monthly_capacity !== null && $product->exportDetails->monthly_capacity_unit)
                            <div class="grid gap-1 py-4 sm:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] sm:gap-5">
                                <dt class="text-sm text-charcoal/55">{{ __('export.product_detail.monthly_supply_capacity') }}</dt>
                                <dd class="text-sm font-semibold text-charcoal">{{ number_format($product->exportDetails->monthly_capacity) }} {{ __('export.units.'.$product->exportDetails->monthly_capacity_unit) }} / {{ __('export.units.month') }}</dd>
                            </div>
                        @endif
                        <div class="grid gap-1 py-4 sm:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] sm:gap-5">
                            <dt class="text-sm text-charcoal/55">{{ __('export.product_detail.category') }}</dt>
                            <dd class="text-sm font-semibold text-charcoal"><x-export.category-label :category="$product->category" /></dd>
                        </div>
                    </dl>
                </section>
            </div>
        </div>
    </article>
</x-export.layout>