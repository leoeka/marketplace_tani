<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.shipping-services.create') }}" class="rounded-xl bg-harvest px-4 py-2.5 text-sm font-bold text-forest">{{ __('admin.shipping_services.add', [], 'id') }}</a>
    </div>

    @if ($shippingServices->isEmpty())
        <p class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('admin.shipping_services.empty', [], 'id') }}</p>
    @else
        <div class="overflow-x-auto rounded-2xl border border-forest/10 bg-white shadow-sm">
            <table class="w-full min-w-[42rem] text-left text-sm">
                <thead class="bg-cream text-xs uppercase tracking-wide text-charcoal/60"><tr>
                    <th class="px-5 py-4">{{ __('admin.shipping_services.name', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.shipping_services.type', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.shipping_services.is_active', [], 'id') }}</th>
                    <th class="px-5 py-4"><span class="sr-only">{{ __('admin.shipping_services.edit', [], 'id') }}</span></th>
                </tr></thead>
                <tbody class="divide-y divide-forest/10">
                    @foreach ($shippingServices as $shippingService)
                        <tr>
                            <td class="px-5 py-4 font-bold">{{ $shippingService->name }}</td>
                            <td class="px-5 py-4">{{ __('admin.shipping_services.'.$shippingService->type, [], 'id') }}</td>
                            <td class="px-5 py-4">{{ __('admin.shipping_services.'.($shippingService->is_active ? 'active' : 'inactive'), [], 'id') }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.shipping-services.edit', $shippingService) }}" class="font-bold text-forest underline">{{ __('admin.shipping_services.edit', [], 'id') }}</a>
                                    <form method="POST" action="{{ route('admin.shipping-services.destroy', $shippingService) }}" onsubmit="return confirm(@js(__('admin.shipping_services.confirm_delete', [], 'id')))" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold text-red-700 underline">{{ __('admin.shipping_services.delete', [], 'id') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $shippingServices->links() }}</div>
    @endif
</x-dashboard-layout>
