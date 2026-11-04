<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.farmers.index') }}" class="text-sm font-bold text-forest underline underline-offset-4">← {{ __('admin.farmers.back', [], 'id') }}</a>
        @if ($farmerProfile->verified_at)
            <form method="POST" action="{{ route('admin.farmers.revoke', $farmerProfile) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl border border-red-200 px-4 py-2.5 text-sm font-bold text-red-700 hover:bg-red-50">{{ __('admin.farmers.revoke', [], 'id') }}</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.farmers.verify', $farmerProfile) }}">
                @csrf
                <button type="submit" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-bold text-white hover:bg-forest/90">{{ __('admin.farmers.verify', [], 'id') }}</button>
            </form>
        @endif
    </div>

    <article class="grid gap-6 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:grid-cols-[12rem_1fr] sm:p-8">
        @if ($farmerProfile->photo)
            <img src="{{ asset('storage/'.$farmerProfile->photo) }}" alt="{{ __('admin.farmers.photo', [], 'id') }}" class="aspect-square w-full rounded-2xl object-cover">
        @else
            <div class="flex aspect-square items-center justify-center rounded-2xl bg-cream text-5xl" role="img" aria-label="{{ __('marketplace.farmers.no_photo', [], 'id') }}">🌱</div>
        @endif
        <div class="space-y-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-forest/60">{{ $farmerProfile->shop_name }}</p>
                <h2 class="mt-1 text-2xl font-extrabold text-charcoal">{{ $farmerProfile->user->name }}</h2>
                <p class="mt-2 text-sm text-charcoal/65">{{ __('admin.farmers.email', [], 'id') }}: {{ $farmerProfile->user->email }}</p>
            </div>
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-xs font-bold uppercase text-charcoal/55">{{ __('admin.farmers.region', [], 'id') }}</dt><dd class="mt-1 text-sm font-semibold text-charcoal">{{ $farmerProfile->region }}</dd></div>
                <div><dt class="text-xs font-bold uppercase text-charcoal/55">{{ __('admin.farmers.group_name', [], 'id') }}</dt><dd class="mt-1 text-sm font-semibold text-charcoal">{{ $farmerProfile->group_name ?: '—' }}</dd></div>
                <div><dt class="text-xs font-bold uppercase text-charcoal/55">{{ __('admin.farmers.status', [], 'id') }}</dt><dd class="mt-1 text-sm font-semibold text-charcoal">{{ $farmerProfile->verified_at ? __('admin.farmers.verified', [], 'id') : __('admin.farmers.pending', [], 'id') }}</dd></div>
            </dl>
            <div>
                <h3 class="text-sm font-extrabold text-charcoal">{{ __('admin.farmers.story', [], 'id') }}</h3>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-charcoal/70">{{ $farmerProfile->story ?: '—' }}</p>
            </div>
        </div>
    </article>
</x-dashboard-layout>