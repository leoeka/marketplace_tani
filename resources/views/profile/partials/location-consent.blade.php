@php
    $consent = auth()->user()->location_consent;
@endphp
<section class="mt-8 rounded-2xl border border-forest/10 bg-white p-6 shadow-sm">
    <h2 class="text-lg font-extrabold text-charcoal">Izin lokasi</h2>
    <p class="mt-2 text-sm leading-6 text-charcoal/65">
        Status:
        @if ($consent === 'granted')
            <span class="font-bold text-green-800">Anda mengizinkan berbagi lokasi.</span>
        @elseif ($consent === 'denied')
            <span class="font-bold text-red-700">Anda menolak berbagi lokasi.</span>
        @else
            <span class="font-bold text-amber-800">Anda belum memilih.</span>
        @endif
        Lokasi hanya dilihat oleh admin dan dipakai untuk mengetahui wilayah akses TaniRaya.
    </p>

    @if ($consent === 'granted')
        <form method="POST" action="{{ route('location.destroy') }}" class="mt-4"
              onsubmit="return confirm('Hapus semua data lokasi Anda dan tarik izin?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-50">
                Tarik izin dan hapus data lokasi saya
            </button>
        </form>
    @endif
</section>