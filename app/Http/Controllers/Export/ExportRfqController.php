<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Http\Requests\Export\StoreExportRfqRequest;
use App\Mail\ExportRfqNotification;
use App\Models\Product;
use App\Models\Rfq;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ExportRfqController extends Controller
{
    public function create(Request $request): View
    {
        $selectedProduct = null;
        $productSlug = $request->query('product');

        if (is_string($productSlug) && $productSlug !== '') {
            $selectedProduct = Product::exportVisible()
                ->with('exportDetails')
                ->where('slug', $productSlug)
                ->first();
        }

        return view('export.rfq.create', [
            'products' => Product::exportVisible()
                ->with('exportDetails')
                ->orderBy('id')
                ->get(),
            'selectedProduct' => $selectedProduct,
        ]);
    }

    public function store(StoreExportRfqRequest $request): RedirectResponse
    {
        if ($request->isHoneypotFilled()) {
            return redirect()->route('export.rfq.thanks');
        }

        $data = $request->validated();
        $product = isset($data['product_id'])
            ? Product::exportVisible()->with('exportDetails')->find($data['product_id'])
            : null;

        $rfq = Rfq::query()->create([
            'buyer_name' => $data['buyer_name'],
            'company' => $data['company'],
            'country' => $data['country'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'product_id' => $product?->id,
            'product_name' => $product?->exportDetails?->name_en,
            'quantity' => $data['quantity'] ?? null,
            'quantity_unit' => $data['quantity_unit'] ?? null,
            'wants_sample' => (bool) ($data['wants_sample'] ?? false),
            'asks_moq' => (bool) ($data['asks_moq'] ?? false),
            'incoterms' => $data['incoterms'],
            'message' => $data['message'] ?? null,
            'status' => 'new',
        ]);

        $notificationEmail = config('export.rfq_notification_email');

        if (is_string($notificationEmail) && trim($notificationEmail) !== '') {
            try {
                Mail::to($notificationEmail)->send(new ExportRfqNotification($rfq));
            } catch (Throwable $exception) {
                Log::error('RFQ admin notification could not be sent.', [
                    'rfq_id' => $rfq->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('export.rfq.thanks');
    }

    public function thanks(): View
    {
        return view('export.rfq.thanks');
    }
}