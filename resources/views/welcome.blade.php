<x-marketplace.layout>
    <x-marketplace.hero />
    <x-marketplace.promo-banners />
    <x-marketplace.categories />
    <x-marketplace.product-grid :products="$products" />
    <x-marketplace.farmer-grid :farmers="$farmers" />
</x-marketplace.layout>
