<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    @verbatim
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Riwayat Transaksi</x:Name>
                    <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    @endverbatim
</head>
<body>
@php
    $headers = ['No. Order', 'Kasir', 'Outlet', 'Waktu & Tanggal', 'Pelanggan', 'Tipe Order', 'No. Meja', 'Item Pesanan', 'Metode Bayar', 'Diskon', 'Total Transaksi', 'Status'];
    $columnCount = count($headers);
    $statusLabels = ['completed' => 'Selesai (Lunas)', 'hold' => 'Hold', 'voided' => 'Void'];
    $paymentLabels = ['qris' => 'QRIS', 'cash' => 'TUNAI', 'transfer' => 'TRANSFER'];
@endphp
<table border="1">
    <tr>
        <td colspan="{{ $columnCount }}" style="font-size:14pt;font-weight:bold;">{{ $tenantName }} - Riwayat Transaksi</td>
    </tr>
    <tr>
        <td colspan="{{ $columnCount }}">Periode: {{ $periodLabel }} | Jumlah transaksi: {{ $transactions->count() }} | Diekspor: {{ $generatedAt }}</td>
    </tr>
    <tr>
        <td colspan="{{ $columnCount }}"></td>
    </tr>
    <tr>
        @foreach ($headers as $header)
            <th style="background-color:#F97316;color:#FFFFFF;font-weight:bold;text-align:center;">{{ $header }}</th>
        @endforeach
    </tr>
    @foreach ($transactions as $tx)
        @php
            $firstPayment = $tx->payments->first();
            $paymentMethod = $firstPayment ? strtolower($firstPayment->payment_method) : 'cash';
            $paymentLabel = $paymentLabels[$paymentMethod] ?? strtoupper($paymentMethod);
            $itemsSummary = $tx->items->map(fn ($item) => ((float) $item->qty == (int) $item->qty ? (int) $item->qty : $item->qty).'x '.$item->product_name)->implode(', ');
        @endphp
        <tr>
            <td>{{ $tx->order_number }}</td>
            <td>{{ $tx->user?->name ?? 'Kasir Lokara' }}</td>
            <td>{{ $tx->outlet?->name ?? 'Outlet Utama' }}</td>
            <td>{{ $tx->created_at->format('d M Y, H:i') }} WIB</td>
            <td>{{ $tx->customer?->name ?? 'Pelanggan Reguler' }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $tx->order_type)) }}</td>
            <td>{{ $tx->table_number ?? '-' }}</td>
            <td>{{ $itemsSummary }}</td>
            <td>{{ $paymentLabel }}</td>
            <td style="mso-number-format:'\#\,\#\#0';">{{ (float) $tx->discount_amount }}</td>
            <td style="mso-number-format:'\#\,\#\#0';">{{ (float) $tx->total_amount }}</td>
            <td>{{ $statusLabels[$tx->status] ?? ucfirst($tx->status) }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="{{ $columnCount - 2 }}" style="font-weight:bold;text-align:right;">Total</td>
        <td style="background-color:#FED7AA;font-weight:bold;mso-number-format:'\#\,\#\#0';">{{ $totalAmount }}</td>
        <td></td>
    </tr>
</table>
</body>
</html>
