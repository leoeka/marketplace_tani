<h1>{{ __('admin.rfqs.notification.title', [], 'id') }}</h1>
<dl>
    <dt>{{ __('admin.rfqs.notification.rfq_id', [], 'id') }}</dt><dd>{{ $rfq->id }}</dd>
    <dt>{{ __('admin.rfqs.notification.created_at', [], 'id') }}</dt><dd>{{ $rfq->created_at->format('d/m/Y H:i') }}</dd>
    <dt>{{ __('admin.rfqs.notification.buyer_name', [], 'id') }}</dt><dd>{{ $rfq->buyer_name }}</dd>
    <dt>{{ __('admin.rfqs.notification.company', [], 'id') }}</dt><dd>{{ $rfq->company }}</dd>
    <dt>{{ __('admin.rfqs.notification.country', [], 'id') }}</dt><dd>{{ $rfq->country }}</dd>
    <dt>{{ __('admin.rfqs.notification.email', [], 'id') }}</dt><dd>{{ $rfq->email }}</dd>
    <dt>{{ __('admin.rfqs.notification.phone', [], 'id') }}</dt><dd>{{ $rfq->phone ?: __('admin.rfqs.notification.not_provided', [], 'id') }}</dd>
    <dt>{{ __('admin.rfqs.notification.product', [], 'id') }}</dt><dd>{{ $rfq->product_name ?: __('admin.rfqs.general_inquiry', [], 'id') }}</dd>
    <dt>{{ __('admin.rfqs.notification.product_id', [], 'id') }}</dt><dd>{{ $rfq->product_id ?? __('admin.rfqs.notification.not_provided', [], 'id') }}</dd>
    <dt>{{ __('admin.rfqs.notification.quantity', [], 'id') }}</dt><dd>{{ $rfq->quantity !== null ? number_format($rfq->quantity, 0, ',', '.').' '.$rfq->quantity_unit : __('admin.rfqs.notification.not_provided', [], 'id') }}</dd>
    <dt>{{ __('admin.rfqs.notification.wants_sample', [], 'id') }}</dt><dd>{{ __('admin.rfqs.'.($rfq->wants_sample ? 'yes' : 'no'), [], 'id') }}</dd>
    <dt>{{ __('admin.rfqs.notification.asks_moq', [], 'id') }}</dt><dd>{{ __('admin.rfqs.'.($rfq->asks_moq ? 'yes' : 'no'), [], 'id') }}</dd>
    <dt>{{ __('admin.rfqs.notification.incoterms', [], 'id') }}</dt><dd>{{ $rfq->incoterms }}</dd>
    <dt>{{ __('admin.rfqs.notification.message', [], 'id') }}</dt><dd>{{ $rfq->message ?: __('admin.rfqs.notification.not_provided', [], 'id') }}</dd>
    <dt>{{ __('admin.rfqs.notification.status', [], 'id') }}</dt><dd>{{ __('admin.rfqs.'.$rfq->status, [], 'id') }}</dd>
    <dt>{{ __('admin.rfqs.notification.handled_at', [], 'id') }}</dt><dd>{{ $rfq->handled_at?->format('d/m/Y H:i') ?? __('admin.rfqs.notification.not_provided', [], 'id') }}</dd>
</dl>
<p><a href="{{ $detailUrl }}">{{ __('admin.rfqs.notification.open_detail', [], 'id') }}</a></p>