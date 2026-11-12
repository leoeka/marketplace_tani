<?php

return [
    'farmer_menu' => [
        ['label' => 'overview', 'route' => 'farmer.dashboard', 'icon' => '▦'],
        ['label' => 'my_products', 'route' => 'farmer.products.index', 'active' => 'farmer.products.*', 'icon' => '🥬'],
        ['label' => 'incoming_orders', 'route' => 'farmer.orders.index', 'active' => 'farmer.orders.*', 'icon' => '📦'],
        ['label' => 'shop_profile', 'route' => 'farmer.profile.edit', 'icon' => '🏡'],
    ],
    'admin_menu' => [
        ['label' => 'overview', 'route' => 'admin.dashboard', 'icon' => '▦'],
        ['label' => 'farmers', 'route' => 'admin.farmers.index', 'active' => 'admin.farmers.*', 'icon' => '🌾'],
        ['label' => 'products', 'route' => 'admin.products.index', 'active' => 'admin.products.*', 'icon' => '🥬'],
        ['label' => 'orders', 'route' => 'admin.orders.index', 'active' => 'admin.orders.*', 'icon' => '📦'],
        ['label' => 'rfqs', 'route' => 'admin.rfqs.index', 'active' => 'admin.rfqs.*', 'icon' => '✉️'],
        ['label' => 'certificates', 'route' => 'admin.certificates.index', 'active' => 'admin.certificates.*', 'icon' => '📄'],
        ['label' => 'shipping_services', 'route' => 'admin.shipping-services.index', 'active' => 'admin.shipping-services.*', 'icon' => '🚚'],
        ['label' => 'export_logistics', 'route' => 'admin.loading-ports.index', 'active' => ['admin.loading-ports.*', 'admin.freight-estimates.*'], 'icon' => '🌍'],
        ['label' => 'market_prices', 'route' => 'admin.market-prices.index', 'active' => 'admin.market-prices.*', 'icon' => '📈'],
        ['label' => 'articles', 'route' => 'admin.articles.index', 'active' => 'admin.articles.*', 'icon' => '📚'],
    ],
];