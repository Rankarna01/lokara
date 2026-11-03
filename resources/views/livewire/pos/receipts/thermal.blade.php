<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $order->order_number }}</title>
    <style>
        @page { size: {{ $paperWidth }} auto; margin: 2mm; }
        * { box-sizing: border-box; }
        body {
            width: {{ $paperWidth === '80mm' ? '72mm' : '48mm' }};
            margin: 0 auto;
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.5;
            color: #000;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; gap: 4px; margin: 2px 0; }
        .muted { font-size: 10px; }
        .logo { max-width: 40mm; max-height: 18mm; margin: 0 auto 6px; display: block; }
        .copy { padding: 10px 0 16px; page-break-after: always; }
        .copy:last-child { page-break-after: auto; }
    </style>
</head>
<body>
@php
    $showLogo = $settings['receipt_show_logo'] ?? true;
    $logoUrl = $settings['receipt_logo_url'] ?? null;
    $storeName = $settings['receipt_store_name'] ?? $tenantName;
    $tagline = $settings['receipt_tagline'] ?? '';
    $headerNotes = $settings['receipt_header_notes'] ?? '';
    $footerNotes = $settings['receipt_footer_notes'] ?? 'Terima Kasih Atas Kunjungan Anda!';
    $wifiInfo = $settings['receipt_wifi_info'] ?? '';
    $socialMedia = $settings['receipt_social_media'] ?? '';
    $showCustomer = $settings['receipt_show_customer'] ?? true;
    $showCashier = $settings['receipt_show_cashier'] ?? true;
    $showTable = $settings['receipt_show_table'] ?? true;
    $payment = $order->payments->first();
    $paymentMethod = $payment ? strtolower($payment->payment_method) : 'cash';
    $paymentLabels = ['qris' => 'QRIS', 'cash' => 'TUNAI', 'transfer' => 'TRANSFER'];
@endphp

@for ($copy = 1; $copy <= $copies; $copy++)
    <div class="copy">
        <div class="center">
            @if ($showLogo && $logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $storeName }}" class="logo" />
            @endif
            <div class="bold" style="font-size: 13px;">{{ $storeName }}</div>
            @if ($order->outlet)
                <div>{{ $order->outlet->name }}</div>
            @endif
            @if ($order->outlet?->address)
                <div class="muted">{{ $order->outlet->address }}</div>
            @endif
            @if ($tagline)
                <div class="muted">{{ $tagline }}</div>
            @endif
            @if ($headerNotes)
                <div class="muted">{{ $headerNotes }}</div>
            @endif
        </div>

        <div class="line"></div>

        <div class="row"><span>No. Order</span><span class="bold">{{ $order->order_number }}</span></div>
        <div class="row"><span>Tanggal</span><span>{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
        @if ($showCashier)
            <div class="row"><span>Kasir</span><span>{{ $order->user?->name ?? 'Kasir' }}</span></div>
        @endif
        @if ($showCustomer)
            <div class="row"><span>Pelanggan</span><span>{{ $order->customer?->name ?? 'Pelanggan Reguler' }}</span></div>
        @endif
        @if ($showTable && $order->table_number)
            <div class="row"><span>Meja</span><span>{{ $order->table_number }}</span></div>
        @endif

        <div class="line"></div>

        @foreach ($order->items as $item)
            <div>{{ $item->product_name }}@if ($item->variant_name) ({{ $item->variant_name }})@endif</div>
            <div class="row">
                <span>{{ (float) $item->qty == (int) $item->qty ? (int) $item->qty : $item->qty }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
        @endforeach

        <div class="line"></div>

        <div class="row"><span>Subtotal</span><span>{{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
        @if ($order->discount_amount > 0)
            <div class="row"><span>Diskon</span><span>-{{ number_format($order->discount_amount, 0, ',', '.') }}</span></div>
        @endif
        @if ($order->tax_amount > 0)
            <div class="row"><span>Pajak</span><span>{{ number_format($order->tax_amount, 0, ',', '.') }}</span></div>
        @endif
        @if ($order->service_amount > 0)
            <div class="row"><span>Service</span><span>{{ number_format($order->service_amount, 0, ',', '.') }}</span></div>
        @endif
        <div class="row bold" style="font-size: 13px;"><span>TOTAL</span><span>{{ number_format($order->total_amount, 0, ',', '.') }}</span></div>

        <div class="line"></div>

        <div class="row"><span>Metode Bayar</span><span>{{ $paymentLabels[$paymentMethod] ?? strtoupper($paymentMethod) }}</span></div>
        @if ($paymentMethod === 'cash' && $payment)
            <div class="row"><span>Uang Diterima</span><span>{{ number_format($payment->cash_tendered, 0, ',', '.') }}</span></div>
            <div class="row"><span>Kembalian</span><span>{{ number_format($payment->change_due, 0, ',', '.') }}</span></div>
        @endif

        <div class="line"></div>

        <div class="center">
            @if ($wifiInfo)
                <div class="muted">WiFi: {{ $wifiInfo }}</div>
            @endif
            @if ($socialMedia)
                <div class="muted">{{ $socialMedia }}</div>
            @endif
            <div style="margin-top: 4px;">{{ $footerNotes }}</div>
        </div>
    </div>
@endfor

<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
</body>
</html>
