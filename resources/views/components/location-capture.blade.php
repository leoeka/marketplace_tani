@auth
    @if (auth()->user()->location_consent !== 'denied')
        <div x-data="locationCapture({{ auth()->user()->location_consent === 'granted' ? 'true' : 'false' }})" x-init="init()" x-show="ask" x-cloak
             class="fixed inset-x-4 bottom-4 z-50 mx-auto max-w-md rounded-2xl border border-black/10 bg-white p-5 shadow-xl">
            <p class="font-bold">{{ __('marketplace.location.title') }}</p>
            <p class="mt-1 text-sm text-gray-600">{{ __('marketplace.location.body') }}</p>
            <div class="mt-4 flex gap-2">
                <button type="button" @click="allow()" class="rounded-lg bg-green-800 px-4 py-2 text-sm font-bold text-white">{{ __('marketplace.location.allow') }}</button>
                <button type="button" @click="deny()" class="rounded-lg border px-4 py-2 text-sm font-bold">{{ __('marketplace.location.deny') }}</button>
            </div>
        </div>
        <script>
            function locationCapture(granted) {
                const csrf = document.querySelector('meta[name=csrf-token]')?.content;
                const post = (url, body) => fetch(url, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf},
                    body: JSON.stringify(body),
                });
                return {
                    ask: false,
                    init() {
                        if (!('geolocation' in navigator)) return;
                        granted ? this.send() : (this.ask = true);
                    },
                    send() {
                        navigator.geolocation.getCurrentPosition(
                            p => post('{{ route('location.store') }}', {
                                latitude: p.coords.latitude, longitude: p.coords.longitude, accuracy: p.coords.accuracy,
                            }),
                            () => {}, {maximumAge: 600000, timeout: 10000}
                        );
                    },
                    async allow() {
                        await post('{{ route('location.consent') }}', {choice: 'granted'});
                        this.ask = false;
                        this.send();
                    },
                    async deny() {
                        await post('{{ route('location.consent') }}', {choice: 'denied'});
                        this.ask = false;
                    },
                };
            }
        </script>
    @endif
@endauth