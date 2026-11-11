<x-export.layout :title="__('export.rfq.meta_title')">
    <section class="bg-[#f4f7f3]">
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16 lg:px-10">
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-forest/65">{{ __('export.rfq.eyebrow') }}</p>
            <h1 class="mt-3 max-w-3xl text-3xl font-extrabold leading-tight tracking-tight text-forest sm:text-4xl">{{ __('export.rfq.title') }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-charcoal/65">{{ __('export.rfq.description') }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-5 py-10 sm:px-8 sm:py-14">
        <form method="POST" action="{{ route('export.rfq.store') }}" class="overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
            @csrf
            <div class="border-b border-forest/10 px-6 py-6 sm:px-8">
                <h2 class="text-xl font-extrabold text-forest">{{ __('export.rfq.form_title') }}</h2>
                <p class="mt-2 text-sm text-charcoal/60">{{ __('export.rfq.required_note') }}</p>
            </div>

            @if ($errors->any())
                <div class="mx-6 mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 sm:mx-8" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-8 px-6 py-7 sm:px-8 sm:py-9">
                <section aria-labelledby="rfq-contact-heading">
                    <h3 id="rfq-contact-heading" class="text-sm font-extrabold uppercase tracking-wide text-forest">{{ __('export.rfq.contact_section') }}</h3>
                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="buyer_name" class="text-sm font-semibold text-charcoal">{{ __('export.rfq.full_name') }} <span aria-hidden="true">*</span></label>
                            <input id="buyer_name" name="buyer_name" type="text" value="{{ old('buyer_name') }}" maxlength="255" required autocomplete="name" class="mt-2 w-full rounded-lg border border-forest/20 px-4 py-3 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                            @error('buyer_name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="company" class="text-sm font-semibold text-charcoal">{{ __('export.rfq.company_name') }} <span aria-hidden="true">*</span></label>
                            <input id="company" name="company" type="text" value="{{ old('company') }}" maxlength="255" required autocomplete="organization" class="mt-2 w-full rounded-lg border border-forest/20 px-4 py-3 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                            @error('company')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="country" class="text-sm font-semibold text-charcoal">{{ __('export.rfq.country') }} <span aria-hidden="true">*</span></label>
                            <input id="country" name="country" type="text" value="{{ old('country') }}" maxlength="100" required autocomplete="country-name" class="mt-2 w-full rounded-lg border border-forest/20 px-4 py-3 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                            @error('country')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="text-sm font-semibold text-charcoal">{{ __('export.rfq.business_email') }} <span aria-hidden="true">*</span></label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="255" required autocomplete="email" class="mt-2 w-full rounded-lg border border-forest/20 px-4 py-3 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                            @error('email')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="phone" class="text-sm font-semibold text-charcoal">{{ __('export.rfq.phone') }}</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone') }}" maxlength="50" autocomplete="tel" class="mt-2 w-full rounded-lg border border-forest/20 px-4 py-3 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                            @error('phone')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section aria-labelledby="rfq-request-heading" class="border-t border-forest/10 pt-8">
                    <h3 id="rfq-request-heading" class="text-sm font-extrabold uppercase tracking-wide text-forest">{{ __('export.rfq.request_section') }}</h3>
                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="product_id" class="text-sm font-semibold text-charcoal">{{ __('export.rfq.product') }}</label>
                            <select id="product_id" name="product_id" class="mt-2 w-full rounded-lg border border-forest/20 bg-white px-4 py-3 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                                <option value="">{{ __('export.rfq.general_inquiry') }}</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected((string) old('product_id', $selectedProduct?->id) === (string) $product->id)>{{ $product->exportDetails->name_en }}</option>
                                @endforeach
                            </select>
                            @error('product_id')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="quantity" class="text-sm font-semibold text-charcoal">{{ __('export.rfq.quantity') }}</label>
                            <input id="quantity" name="quantity" type="number" min="1" step="1" value="{{ old('quantity') }}" class="mt-2 w-full rounded-lg border border-forest/20 px-4 py-3 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                            @error('quantity')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="quantity_unit" class="text-sm font-semibold text-charcoal">{{ __('export.rfq.quantity_unit') }}</label>
                            <select id="quantity_unit" name="quantity_unit" class="mt-2 w-full rounded-lg border border-forest/20 bg-white px-4 py-3 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                                <option value="">{{ __('export.rfq.choose_unit') }}</option>
                                <option value="kg" @selected(old('quantity_unit') === 'kg')>{{ __('export.units.kg') }}</option>
                                <option value="ton" @selected(old('quantity_unit') === 'ton')>{{ __('export.units.ton') }}</option>
                            </select>
                            @error('quantity_unit')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-3 sm:col-span-2">
                            <label class="flex items-start gap-3 text-sm leading-6 text-charcoal">
                                <input name="wants_sample" type="checkbox" value="1" @checked(old('wants_sample')) class="mt-1 rounded border-forest/30 text-forest focus:ring-forest">
                                <span>{{ __('export.rfq.wants_sample') }}</span>
                            </label>
                            <label class="flex items-start gap-3 text-sm leading-6 text-charcoal">
                                <input name="asks_moq" type="checkbox" value="1" @checked(old('asks_moq')) class="mt-1 rounded border-forest/30 text-forest focus:ring-forest">
                                <span>{{ __('export.rfq.asks_moq') }}</span>
                            </label>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="incoterms" class="text-sm font-semibold text-charcoal">{{ __('export.rfq.incoterms') }}</label>
                            <select id="incoterms" name="incoterms" class="mt-2 w-full rounded-lg border border-forest/20 bg-white px-4 py-3 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                                @foreach (['FOB', 'CIF', 'EXW', 'TBD'] as $term)
                                    <option value="{{ $term }}" @selected(old('incoterms', 'TBD') === $term)>{{ __('export.rfq.incoterms_options.'.$term) }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs leading-5 text-charcoal/55">{{ __('export.rfq.incoterms_help') }}</p>
                            @error('incoterms')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="message" class="text-sm font-semibold text-charcoal">{{ __('export.rfq.message') }}</label>
                            <textarea id="message" name="message" rows="5" maxlength="2000" class="mt-2 w-full rounded-lg border border-forest/20 px-4 py-3 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">{{ old('message') }}</textarea>
                            <p class="mt-2 text-xs text-charcoal/55">{{ __('export.rfq.message_help') }}</p>
                            @error('message')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <div aria-hidden="true" class="absolute left-[-10000px] top-auto h-px w-px overflow-hidden">
                    <label for="fax_number">{{ __('export.rfq.honeypot_label') }}</label>
                    <input id="fax_number" name="fax_number" type="text" tabindex="-1" autocomplete="off" maxlength="255">
                </div>

                <div class="border-t border-forest/10 pt-6">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-forest px-6 py-3.5 text-sm font-bold text-white transition hover:bg-forest/90 focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2 sm:w-auto">{{ __('export.rfq.submit') }}</button>
                </div>
            </div>
        </form>
    </section>
</x-export.layout>