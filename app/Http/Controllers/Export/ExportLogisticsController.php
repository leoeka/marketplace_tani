<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Http\Requests\Export\ExportLogisticsCalculatorRequest;
use App\Models\FreightEstimate;
use App\Models\LoadingPort;
use Illuminate\Contracts\View\View;

class ExportLogisticsController extends Controller
{
    public function index(ExportLogisticsCalculatorRequest $request): View
    {
        $filters = $request->validated();
        $mode = $filters['mode'] ?? 'sea';
        $destinationsByMode = [];

        foreach (['sea', 'air'] as $freightMode) {
            $destinationQuery = FreightEstimate::query()
                ->where('mode', $freightMode)
                ->where('is_active', true)
                ->select('destination')
                ->distinct();

            if ($freightMode === 'sea') {
                $destinationQuery->whereHas('loadingPort', fn ($query) => $query->where('is_active', true));
            }

            $destinationsByMode[$freightMode] = $destinationQuery->orderBy('destination')->pluck('destination');
        }

        $result = null;

        if ($request->hasCalculatorInput()) {
            $estimateQuery = FreightEstimate::query()
                ->where('mode', $filters['mode'])
                ->where('destination', $filters['destination'])
                ->where('is_active', true);

            if ($filters['mode'] === 'sea') {
                $estimateQuery->where('loading_port_id', $filters['loading_port_id']);
            }

            $estimate = $estimateQuery->orderBy('id')->first();

            if ($estimate) {
                $weightInKg = (float) $filters['weight'] * ($filters['unit'] === 'ton' ? 1000 : 1);
                $result = [
                    'estimate' => $estimate,
                    'duration' => $estimate->transit_days_min === $estimate->transit_days_max
                        ? __('export.logistics.result.days', ['days' => $estimate->transit_days_min], 'en')
                        : __('export.logistics.result.days_range', [
                            'minimum' => $estimate->transit_days_min,
                            'maximum' => $estimate->transit_days_max,
                        ], 'en'),
                    'cost' => $estimate->rate_usd_per_kg === null
                        ? null
                        : number_format($weightInKg * (float) $estimate->rate_usd_per_kg, 2, '.', ','),
                ];
            }
        }

        return view('export.logistics', [
            'mode' => $mode,
            'loadingPorts' => LoadingPort::query()->where('is_active', true)
                ->orderBy('sort_order')->orderBy('name')->get(),
            'destinationsByMode' => $destinationsByMode,
            'filters' => $filters,
            'result' => $result,
            'wasCalculated' => $request->hasCalculatorInput(),
        ]);
    }
}