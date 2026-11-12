<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - {{ $tenantName }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; }
        .header { border-bottom: 2px solid #F97316; padding-bottom: 8px; margin-bottom: 12px; }
        h1 { font-size: 18px; margin: 0 0 4px; color: #0f172a; }
        .meta { color: #64748b; font-size: 11px; }
        
        /* Executive Summary Grid */
        .summary-grid { display: table; width: 100%; margin-bottom: 15px; border-spacing: 6px; }
        .summary-card { display: table-cell; width: 25%; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 8px 10px; vertical-align: top; }
        .summary-card.highlight { background: #ECFDF5; border-color: #A7F3D0; }
        .summary-title { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 3px; }
        .summary-value { font-size: 14px; font-weight: bold; color: #0f172a; }
        .summary-card.highlight .summary-value { color: #047857; }
        .summary-sub { font-size: 8.5px; color: #64748b; margin-top: 3px; }

        /* Section Titles */
        .section-title { font-size: 12px; font-weight: bold; color: #0f172a; margin: 14px 0 6px; padding-left: 6px; border-left: 3px solid #F97316; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9.5px; }
        th { background: #F97316; color: #FFFFFF; font-weight: 600; text-align: left; padding: 5px 6px; font-size: 9px; text-transform: uppercase; }
        td { border-bottom: 1px solid #E2E8F0; padding: 4.5px 6px; vertical-align: middle; }
        tr:nth-child(even) td { background: #FFF7ED; }
        .right { text-align: right; white-space: nowrap; }
        .center { text-align: center; }
        .total-row td { font-weight: bold; border-top: 2px solid #F97316; background: #FED7AA; color: #9A3412; }
        .empty { text-align: center; padding: 15px; color: #94a3b8; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

<div class="header">
    <table style="width:100%; border:none; margin:0;">
        <tr>
            <td style="border:none; padding:0; vertical-align:middle;">
                <h1>{{ $tenantName }}</h1>
                <div class="meta"><strong>Laporan Laba Rugi (P&L) &amp; Analisis Profitabilitas</strong></div>
            </td>
            <td style="border:none; padding:0; text-align:right; vertical-align:middle;">
                <div class="meta">Periode: <strong>{{ $dateFilterLabel }}</strong></div>
                <div class="meta">Waktu Cetak: {{ $generatedAt }}</div>
            </td>
        </tr>
    </table>
</div>

<!-- Ringkasan Eksekutif -->
<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-title">Total Omzet Penjualan</div>
        <div class="summary-value">Rp {{ number_format($combinedSales, 0, ',', '.') }}</div>
        <div class="summary-sub">Produk: Rp {{ number_format($productSummary['total_sales'], 0, ',', '.') }} | Jasa: Rp {{ number_format($serviceSummary['total_sales'], 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">HPP, Bahan &amp; Komisi</div>
        <div class="summary-value" style="color:#E11D48;">- Rp {{ number_format($combinedDirectCost, 0, ',', '.') }}</div>
        <div class="summary-sub">HPP: {{ number_format($productSummary['total_cogs'], 0, ',', '.') }} | Bahan: {{ number_format($serviceSummary['total_material_cost'], 0, ',', '.') }} | Komisi: {{ number_format($serviceSummary['total_commission'], 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">Beban OPEX Toko</div>
        <div class="summary-value" style="color:#D97706;">- Rp {{ number_format($totalOpex, 0, ',', '.') }}</div>
        <div class="summary-sub">Sewa, Gaji, Listrik, Air &amp; Operasional</div>
    </div>
    <div class="summary-card highlight">
        <div class="summary-title">Laba Bersih Akhir (Net Profit)</div>
        <div class="summary-value">Rp {{ number_format($combinedNetProfit, 0, ',', '.') }}</div>
        <div class="summary-sub">Margin Kotor: {{ $combinedMargin }}%</div>
    </div>
</div>

<!-- TABEL 1: PENJUALAN PRODUK MASTER -->
<div class="section-title">1. Rincian Laba Rugi Penjualan Produk Master (Retail &amp; Display)</div>
<table>
    <thead>
        <tr>
            <th style="width:25px;" class="center">No</th>
            <th>Nama Produk &amp; SKU</th>
            <th>Kategori</th>
            <th class="right">Modal HPP</th>
            <th class="right">Harga Jual</th>
            <th class="center">Qty</th>
            <th class="right">Total Omzet</th>
            <th class="right">Total HPP</th>
            <th class="right">Laba Kotor</th>
            <th class="center">Margin</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($productRows as $idx => $p)
            <tr>
                <td class="center">{{ $idx + 1 }}</td>
                <td>
                    <strong>{{ $p['name'] }}</strong><br>
                    <span style="color:#64748b; font-size:8.5px;">{{ $p['sku'] }}</span>
                </td>
                <td>{{ $p['category_name'] }}</td>
                <td class="right">Rp {{ number_format($p['unit_cost'], 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($p['unit_price'], 0, ',', '.') }}</td>
                <td class="center"><strong>{{ number_format($p['qty_sold'], 0, ',', '.') }}</strong></td>
                <td class="right">Rp {{ number_format($p['gross_sales'], 0, ',', '.') }}</td>
                <td class="right" style="color:#e11d48;">Rp {{ number_format($p['total_cost'], 0, ',', '.') }}</td>
                <td class="right" style="color:#059669;"><strong>Rp {{ number_format($p['gross_profit'], 0, ',', '.') }}</strong></td>
                <td class="center"><strong>{{ $p['margin_pct'] }}%</strong></td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="empty">Tidak ada data penjualan produk master pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
    @if (!empty($productRows))
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="right">Total Produk</td>
                <td class="center">{{ number_format($productSummary['total_qty'], 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($productSummary['total_sales'], 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($productSummary['total_cogs'], 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($productSummary['total_profit'], 0, ',', '.') }}</td>
                <td class="center">{{ $productSummary['avg_margin'] }}%</td>
            </tr>
        </tfoot>
    @endif
</table>

<!-- TABEL 2: PENJUALAN MASTER LAYANAN (KHUSUS SALON / LAYANAN) -->
@if ($isSalon || !empty($serviceRows))
    <div class="section-title">2. Rincian Laba Rugi Master Layanan Jasa (Treatment &amp; Styling)</div>
    <table>
        <thead>
            <tr>
                <th style="width:25px;" class="center">No</th>
                <th>Nama Layanan</th>
                <th>Tag Kategori</th>
                <th class="center">Durasi</th>
                <th class="right">Tarif Jasa</th>
                <th class="right">Modal Bahan</th>
                <th class="center">Sesi</th>
                <th class="right">Total Omzet</th>
                <th class="right">Total Bahan</th>
                <th class="right">Komisi Staf</th>
                <th class="right">Laba Bersih</th>
                <th class="center">Margin</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($serviceRows as $idx => $s)
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $s['name'] }}</strong><br>
                        <span style="color:#64748b; font-size:8.5px;">{{ $s['category_name'] }}</span>
                    </td>
                    <td>{{ $s['category_tag'] }}</td>
                    <td class="center">{{ $s['duration_minutes'] }} mnt</td>
                    <td class="right">Rp {{ number_format($s['tarif'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($s['unit_material_cost'], 0, ',', '.') }}</td>
                    <td class="center"><strong>{{ number_format($s['sessions_count'], 0, ',', '.') }}</strong></td>
                    <td class="right">Rp {{ number_format($s['gross_sales'], 0, ',', '.') }}</td>
                    <td class="right" style="color:#e11d48;">Rp {{ number_format($s['total_material_cost'], 0, ',', '.') }}</td>
                    <td class="right" style="color:#d97706;">Rp {{ number_format($s['total_commission'], 0, ',', '.') }}</td>
                    <td class="right" style="color:#059669;"><strong>Rp {{ number_format($s['net_profit'], 0, ',', '.') }}</strong></td>
                    <td class="center"><strong>{{ $s['margin_pct'] }}%</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="empty">Tidak ada data penjualan master layanan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if (!empty($serviceRows))
            <tfoot>
                <tr class="total-row">
                    <td colspan="6" class="right">Total Layanan</td>
                    <td class="center">{{ number_format($serviceSummary['total_sessions'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($serviceSummary['total_sales'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($serviceSummary['total_material_cost'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($serviceSummary['total_commission'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($serviceSummary['total_profit'], 0, ',', '.') }}</td>
                    <td class="center">{{ $serviceSummary['avg_margin'] }}%</td>
                </tr>
            </tfoot>
        @endif
    </table>
@endif

<!-- TABEL 3: BEBAN OPERASIONAL (OPEX) -->
@if ($expenseRows->isNotEmpty())
    <div class="section-title">3. Daftar Pengeluaran &amp; Beban Operasional (OPEX)</div>
    <table>
        <thead>
            <tr>
                <th style="width:25px;" class="center">No</th>
                <th>Tanggal</th>
                <th>Kategori Beban</th>
                <th>Keterangan / Keperluan</th>
                <th>Metode Bayar</th>
                <th>Catatan</th>
                <th class="right">Jumlah Beban</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenseRows as $idx => $exp)
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}</td>
                    <td><strong>{{ $exp->category }}</strong></td>
                    <td>{{ $exp->title }}</td>
                    <td>{{ $exp->payment_method }}</td>
                    <td><span style="color:#64748b;">{{ $exp->notes ?: '-' }}</span></td>
                    <td class="right" style="color:#e11d48;"><strong>Rp {{ number_format($exp->amount, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="right">Total Beban Operasional (OPEX)</td>
                <td class="right">Rp {{ number_format($totalOpex, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
@endif

<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
</body>
</html>
