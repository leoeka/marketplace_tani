<section id="home" class="relative isolate overflow-hidden bg-forest text-white">
    <div class="absolute -left-28 -top-32 h-80 w-80 rounded-full bg-white/5 blur-3xl" aria-hidden="true"></div>
    <div class="absolute right-0 top-12 h-72 w-72 rounded-full bg-harvest/10 blur-3xl" aria-hidden="true"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-8 px-5 pb-28 pt-12 sm:px-8 sm:pb-32 sm:pt-16 lg:grid-cols-[0.95fr_1.05fr] lg:gap-10 lg:px-10 lg:pb-36 lg:pt-20">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-harvest sm:text-sm">
                <span class="h-2 w-2 rounded-full bg-harvest"></span>
                {{ __('marketplace.hero.eyebrow', [], 'id') }}
            </span>
            <h1 class="mt-6 max-w-xl text-4xl font-extrabold leading-[1.08] tracking-tight sm:text-5xl lg:text-6xl xl:text-7xl">
                {{ __('marketplace.hero.title', [], 'id') }}
            </h1>
            <p class="mt-5 max-w-lg text-base leading-7 text-white/75 sm:text-lg sm:leading-8">
                {{ __('marketplace.hero.description', [], 'id') }}
            </p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                <a href="#products" class="inline-flex min-h-12 items-center justify-center rounded-full bg-harvest px-6 py-3 text-sm font-extrabold text-forest shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:bg-yellow-400 sm:text-base">
                    {{ __('marketplace.hero.shop', [], 'id') }}
                    <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.5 10a.75.75 0 0 1 .75-.75h9.94L10.72 5.78a.75.75 0 1 1 1.06-1.06l4.75 4.75a.75.75 0 0 1 0 1.06l-4.75 4.75a.75.75 0 1 1-1.06-1.06l3.47-3.47H4.25A.75.75 0 0 1 3.5 10Z" clip-rule="evenodd" /></svg>
                </a>
                <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center rounded-full border border-white/30 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10 sm:text-base">
                    {{ __('marketplace.hero.sell', [], 'id') }}
                </a>
            </div>
            <div class="mt-8 flex items-center gap-3 text-sm text-white/70">
                <span class="flex -space-x-2" aria-hidden="true">
                    <span class="h-8 w-8 rounded-full border-2 border-forest bg-harvest"></span>
                    <span class="h-8 w-8 rounded-full border-2 border-forest bg-[#d28e4b]"></span>
                    <span class="h-8 w-8 rounded-full border-2 border-forest bg-[#b5c77a]"></span>
                </span>
                <span>{{ __('marketplace.hero.direct_from_farmer', [], 'id') }}</span>
            </div>
        </div>

        <div class="relative mx-auto w-full max-w-[34rem] lg:ml-auto" role="img" aria-label="{{ __('marketplace.hero.decorative_produce', [], 'id') }}">
            <div class="absolute inset-x-7 bottom-4 top-8 rounded-[3rem] bg-[#547c4e] shadow-[0_35px_80px_-40px_rgba(0,0,0,0.65)] sm:inset-x-10 sm:top-5"></div>
            <div class="absolute left-1/2 top-1/2 h-[78%] w-[86%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-harvest/10 blur-2xl"></div>
            <svg viewBox="0 0 560 430" class="relative w-full drop-shadow-[0_25px_22px_rgba(0,0,0,0.25)]" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <ellipse cx="284" cy="365" rx="208" ry="28" fill="#1F422C" fill-opacity=".55" />
                <path d="M107 247c32-70 92-111 172-111 77 0 136 42 173 111" stroke="#A8C96D" stroke-width="13" stroke-linecap="round" />
                <path d="M111 239c-16-47-10-89 16-125 43 23 62 57 56 103M181 143c-6-44 10-79 47-105 32 35 40 73 23 114M251 132c8-49 35-81 83-95 21 45 14 85-20 120M327 145c29-36 66-48 111-36 4 47-16 80-59 99M378 183c40-22 79-19 116 10-13 43-43 65-88 65" fill="#8FBF46" stroke="#B6D66E" stroke-width="5" stroke-linejoin="round" />
                <path d="M121 241c8-46 35-75 81-86 18 42 11 78-22 108M194 218c3-50 26-84 70-102 25 39 24 77-4 115M278 209c16-47 47-74 95-79 14 45 0 81-42 109M356 227c34-36 73-47 117-32-3 46-27 76-73 89" fill="#5F9D3E" stroke="#A9CC67" stroke-width="5" stroke-linejoin="round" />
                <path d="M125 242h346l-33 114c-5 18-21 30-40 30H199c-19 0-35-12-40-30l-34-114Z" fill="#C88A4B" stroke="#E3B66A" stroke-width="9" stroke-linejoin="round" />
                <path d="M141 278h313M151 316h292M171 353h249" stroke="#9B643A" stroke-width="10" stroke-linecap="round" />
                <path d="M178 240c-6-41 10-68 47-81 32 31 39 61 19 91" fill="#F08A32" stroke="#FFC36C" stroke-width="5" />
                <path d="M248 240c-7-51 12-82 57-95 34 39 37 74 9 105" fill="#D9463F" stroke="#F78666" stroke-width="5" />
                <path d="M332 241c-4-48 18-78 66-90 30 43 28 78-7 106" fill="#F6B942" stroke="#F8D072" stroke-width="5" />
                <path d="M218 163c9-16 21-24 37-24M286 142c11-15 24-20 40-17M365 151c12-12 25-15 40-10" stroke="#35663A" stroke-width="7" stroke-linecap="round" />
                <circle cx="405" cy="197" r="18" fill="#4C315C" /><circle cx="433" cy="211" r="17" fill="#5D3B6C" /><circle cx="405" cy="229" r="17" fill="#654374" /><circle cx="456" cy="235" r="17" fill="#4C315C" /><circle cx="432" cy="248" r="16" fill="#5D3B6C" />
                <path d="M410 175c8-18 20-26 37-25" stroke="#8FBF46" stroke-width="7" stroke-linecap="round" />
                <circle cx="153" cy="365" r="26" fill="#E84C42" /><path d="M150 340c3-12 10-17 20-18-1 11-7 18-20 20" fill="#5F9D3E" />
                <circle cx="478" cy="365" r="27" fill="#EFAE36" /><path d="M475 338c3-13 11-18 21-18-1 12-8 19-21 21" fill="#4E8D3E" />
            </svg>
            <div class="absolute bottom-0 left-0 flex items-center gap-3 rounded-2xl border border-white/70 bg-white px-4 py-3 text-forest shadow-xl sm:bottom-4 sm:left-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-cream text-xl" aria-hidden="true">✦</span>
                <span class="text-xs font-bold leading-5 sm:text-sm">{{ __('marketplace.hero.fresh_today', [], 'id') }}<br><span class="font-medium text-charcoal/60">{{ __('marketplace.hero.direct_from_farmer', [], 'id') }}</span></span>
            </div>
        </div>
    </div>
    <svg class="absolute bottom-0 left-0 h-12 w-full sm:h-16" viewBox="0 0 1440 100" preserveAspectRatio="none" aria-hidden="true"><path d="M0 39C128 79 174 72 281 40c115-35 161-2 264 25 104 28 152 6 256-26 105-33 167-27 262 7 110 40 215 36 377-5v59H0V39Z" fill="#FAF7F0" /></svg>
</section>
