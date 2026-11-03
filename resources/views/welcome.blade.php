@php
    $products = [
        ['name' => 'tomato', 'farmer' => 'farmer_1', 'location' => 'location_1', 'price' => 18000, 'preorder' => false, 'art' => 'tomato'],
        ['name' => 'spinach', 'farmer' => 'farmer_2', 'location' => 'location_2', 'price' => 9000, 'preorder' => true, 'art' => 'leaf'],
        ['name' => 'orange', 'farmer' => 'farmer_3', 'location' => 'location_3', 'price' => 22000, 'preorder' => false, 'art' => 'orange'],
        ['name' => 'rice', 'farmer' => 'farmer_4', 'location' => 'location_4', 'price' => 16000, 'preorder' => true, 'art' => 'rice'],
    ];

    $farmers = [
        ['name' => 'farmer_1', 'shop' => 'farmer_1_shop', 'location' => 'farmer_1_location', 'story' => 'farmer_1_story', 'initials' => 'SW', 'tone' => 0],
        ['name' => 'farmer_2', 'shop' => 'farmer_2_shop', 'location' => 'farmer_2_location', 'story' => 'farmer_2_story', 'initials' => 'DS', 'tone' => 1],
        ['name' => 'farmer_3', 'shop' => 'farmer_3_shop', 'location' => 'farmer_3_location', 'story' => 'farmer_3_story', 'initials' => 'RH', 'tone' => 2],
    ];
@endphp

<x-marketplace.layout>
    <x-marketplace.hero />
    <x-marketplace.promo-banners />
    <x-marketplace.categories />
    <x-marketplace.product-grid :products="$products" />
    <x-marketplace.farmer-grid :farmers="$farmers" />
</x-marketplace.layout>
