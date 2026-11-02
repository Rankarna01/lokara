<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - {{ $tenantName }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #1e293b; margin: 0; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .meta { color: #64748b; font-size: 11px; margin-bottom: 10px; }
        .summary { display: inline-block; background: #FFF7ED; border: 1px solid #FDBA74; border-radius: 6px; padding: 6px 10px; margin-bottom: 10px; font-weight: bold; color: #9A3412; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #F97316; color: #FFFFFF; font-weight: bold; text-align: left; padding: 6px 5px; font-size: 10px; }
        td { border-bottom: 1px solid #E2E8F0; padding: 5px; vertical-align: top; }
        tr:nth-child(even) td { background: #FFF7ED; }
        .right { text-align: right; white-space: nowrap; }
        .total-row td { font-weight: bold; border-top: 2px solid #F97316; background: #FED7AA; }
        .empty { text-align: center; padding: 20px; color: #94a3b8; }
    </style>
</head>
<body>
@php
    $statusLabels = ['completed' => 'Selesai (Lunas)', 'hold' => 'Hold', 'voided' => 'Void'];
    $paymentLabels = ['qris' => 'QRIS', 'cash' => 'TUNAI', 'transfer' => 'TRANSFER'];
@endphp

<h1>{{ $tenantName }} - Riwayat Transaksi</h1>
<div class="meta">Periode: {{ $periodLabel }} &nbsp;|&nbsp; Diekspor: {{ $generatedAt }}</div>
<div class="summary">{{ $transactions->count() }} transaksi &nbsp;•&nbsp; Total Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>

<table>
    <thead>
        <tr>
            <th>No. Order &amp; Kasir</th>
            <th>Waktu &amp; Tanggal</th>
            <th>Pelanggan &amp; Meja</th>
            <th>Item Pesanan</th>
            <th>Metode Bayar</th>
            <th class="right">Diskon</th>
            <th class="right">Total Transaksi</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transactions as $tx)
            @php
                $firstPayment = $tx->payments->first();
                $paymentMethod = $firstPayment ? strtolower($firstPayment->payment_method) : 'cash';
                $paymentLabel = $paymentLabels[$paymentMethod] ?? strtoupper($paymentMethod);
                $itemsSummary = $tx->items->map(fn ($item) => ((float) $item->qty == (int) $item->qty ? (int) $item->qty : $item->qty).'x '.$item->product_name)->implode(', ');
            @endphp
            <tr>
                <td>
                    <strong>{{ $tx->order_number }}</strong><br>
                    <span style="color:#64748b;">Kasir: {{ $tx->user?->name ?? 'Kasir Lokara' }} &bull; {{ $tx->outlet?->name ?? 'Outlet Utama' }}</span>
                </td>
                <td>{{ $tx->created_at->format('d M Y, H:i') }} WIB</td>
                <td>
                    {{ $tx->customer?->name ?? 'Pelanggan Reguler' }}<br>
                    <span style="color:#64748b;">{{ ucwords(str_replace('_', ' ', $tx->order_type)) }}@if ($tx->table_number) &bull; Meja {{ $tx->table_number }}@endif</span>
                </td>
                <td>{{ $itemsSummary }}</td>
                <td>{{ $paymentLabel }}</td>
                <td class="right">Rp {{ number_format($tx->discount_amount, 0, ',', '.') }}</td>
                <td class="right"><strong>Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</strong></td>
                <td>{{ $statusLabels[$tx->status] ?? ucfirst($tx->status) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="empty">Tidak ada transaksi pada filter ini.</td>
            </tr>
        @endforelse
    </tbody>
    @if ($transactions->isNotEmpty())
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="right">Total</td>
                <td class="right">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    @endif
</table>

<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
</body>
</html>
