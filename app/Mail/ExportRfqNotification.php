<?php

namespace App\Mail;

use App\Models\Rfq;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExportRfqNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Rfq $rfq)
    {
    }

    public function envelope(): Envelope
    {
        $productName = $this->rfq->product_name ?: __('admin.rfqs.notification.general_inquiry', [], 'id');

        return new Envelope(
            subject: __('admin.rfqs.notification.subject', [
                'company' => $this->rfq->company,
                'product' => $productName,
            ], 'id'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.export-rfq-notification',
            with: [
                'rfq' => $this->rfq,
                'detailUrl' => route('admin.rfqs.show', $this->rfq),
            ],
        );
    }
}