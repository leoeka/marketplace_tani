@props(['id', 'name', 'autocomplete' => null, 'required' => false, 'containerClass' => ''])

<div class="relative {{ $containerClass }}" x-data="{ showPassword: false }">
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="password"
        x-bind:type="showPassword ? 'text' : 'password'"
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @required($required)
        {{ $attributes->merge(['class' => 'block w-full rounded-2xl border-forest/15 bg-cream/50 px-4 py-3 pr-12 text-sm text-charcoal shadow-sm placeholder:text-charcoal/35 focus:border-forest focus:ring-forest']) }}
    >
    <button
        type="button"
        x-on:click="showPassword = !showPassword"
        x-bind:aria-label="showPassword ? @js(__('auth-ui.hide_password', [], 'id')) : @js(__('auth-ui.show_password', [], 'id'))"
        x-bind:aria-pressed="showPassword"
        class="absolute inset-y-0 right-0 flex items-center px-4 text-charcoal/60 hover:text-forest focus:outline-none focus:ring-2 focus:ring-forest"
    >
        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.036 11.68a1.012 1.012 0 0 0 0 .639C3.423 16.49 7.36 19.5 12 19.5c1.16 0 2.27-.198 3.3-.56M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639a10.523 10.523 0 0 1-4.293 5.368M6.228 6.228 3 3m3.228 3.228 3.01 3.01m5.524 5.524 3.01 3.01M9.238 9.238a3 3 0 0 0 4.243 4.243" />
        </svg>
    </button>
</div>