<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketPrice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MarketPriceController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $groups = MarketPrice::query()
            ->when($search !== '', fn ($query) => $query->where('commodity', 'like', '%'.$search.'%'))
            ->orderByDesc('recorded_on')
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn (MarketPrice $price): string => $price->commodity.'|'.$price->unit);

        $rows = $groups->map(function (Collection $history): array {
            $latest = $history->first();
            $previous = $history->get(1);

            return [
                'latest' => $latest,
                'previous' => $previous,
                'history' => $history->take(10),
                'change_percent' => $previous
                    ? round((($latest->price - $previous->price) / $previous->price) * 100, 2)
                    : null,
            ];
        })->values();

        return view('marketplace.market-prices.index', [
            'rows' => $rows,
            'search' => $search,
        ]);
    }
}
