<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    @verbatim
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Laporan Laba Rugi</x:Name>
                    <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    @endverbatim
</head>
<body>
<table border="1">
    <!-- HEADER -->
    <tr>
        <td colspan="10" style="font-size:16pt;font-weight:bold;color:#EA580C;">{{ $tenantName }}</td>
    </tr>
    <tr>
        <td colspan="10" style="font-size:12pt;font-weight:bold;">Laporan Laba Rugi (P&amp;L) &amp; Analisis Finansial</td>
    </tr>
    <tr>
        <td colspan="10" style="color:#64748B;">Periode: {{ $dateFilterLabel }} | Waktu Ekspor: {{ $generatedAt }}</td>
    </tr>
    <tr><td colspan="10"></td></tr>

    <!-- RINGKASAN EKSEKUTIF -->
    <tr>
        <th colspan="10" style="background-color:#FED7AA;color:#9A3412;font-size:11pt;font-weight:bold;text-align:left;">RINGKASAN LABA RUGI EKSEKUTIF</th>
    </tr>
    <tr>
        <td colspan="4" style="font-weight:bold;">Total Omzet Penjualan Gabungan</td>
        <td colspan="6" style="font-weight:bold;mso-number-format:'\#\,\#\#0';">{{ (float) $combinedSales }}</td>
    </tr>
    <tr>
        <td colspan="4" style="padding-left:15px;color:#64748B;">- Penjualan Produk Master</td>
        <td colspan="6" style="mso-number-format:'\#\,\#\#0';">{{ (float) $productSummary['total_sales'] }}</td>
    </tr>
    <tr>
        <td colspan="4" style="padding-left:15px;color:#64748B;">- Penjualan Master Layanan / Jasa</td>
        <td colspan="6" style="mso-number-format:'\#\,\#\#0';">{{ (float) $serviceSummary['total_sales'] }}</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight:bold;color:#DC2626;">Total Biaya Langsung (HPP + Modal Bahan + Komisi)</td>
        <td colspan="6" style="font-weight:bold;color:#DC2626;mso-number-format:'\#\,\#\#0';">{{ (float) $combinedDirectCost }}</td>
    </tr>
    <tr>
        <td colspan="4" style="padding-left:15px;color:#64748B;">- Modal Pokok HPP Produk</td>
        <td colspan="6" style="mso-number-format:'\#\,\#\#0';">{{ (float) $productSummary['total_cogs'] }}</td>
    </tr>
    <tr>
        <td colspan="4" style="padding-left:15px;color:#64748B;">- Modal Bahan Baku Jasa Layanan</td>
        <td colspan="6" style="mso-number-format:'\#\,\#\#0';">{{ (float) $serviceSummary['total_material_cost'] }}</td>
    </tr>
    <tr>
        <td colspan="4" style="padding-left:15px;color:#64748B;">- Total Komisi Staf / Kapster</td>
        <td colspan="6" style="mso-number-format:'\#\,\#\#0';">{{ (float) $serviceSummary['total_commission'] }}</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight:bold;background-color:#F8FAFC;">Laba Kotor (Gross Profit)</td>
        <td colspan="6" style="font-weight:bold;background-color:#F8FAFC;mso-number-format:'\#\,\#\#0';">{{ (float) $combinedGrossProfit }} (Margin: {{ $combinedMargin }}%)</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight:bold;color:#D97706;">Beban Operasional Toko (OPEX)</td>
        <td colspan="6" style="font-weight:bold;color:#D97706;mso-number-format:'\#\,\#\#0';">{{ (float) $totalOpex }}</td>
    </tr>
    <tr>
        <td colspan="4" style="font-size:12pt;font-weight:bold;background-color:#D1FAE5;color:#065F46;">LABA BERSIH AKHIR (NET PROFIT)</td>
        <td colspan="6" style="font-size:12pt;font-weight:bold;background-color:#D1FAE5;color:#065F46;mso-number-format:'\#\,\#\#0';">{{ (float) $combinedNetProfit }}</td>
    </tr>
    <tr><td colspan="10"></td></tr>

    <!-- TABEL 1: RINCIAN PENJUALAN PRODUK MASTER -->
    <tr>
        <th colspan="10" style="background-color:#F97316;color:#FFFFFF;font-size:11pt;font-weight:bold;text-align:left;">1. RINCIAN LABA RUGI PRODUK MASTER (RETAIL &amp; DISPLAY)</th>
    </tr>
    <tr>
        <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:center;">No</th>
        <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;">Nama Produk</th>
        <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;">SKU</th>
        <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;">Kategori</th>
        <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:right;">Modal HPP/Unit</th>
        <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:right;">Harga Jual</th>
        <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:center;">Qty Terjual</th>
        <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:right;">Total Omzet</th>
        <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:right;">Total HPP</th>
        <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:right;">Laba Kotor</th>
    </tr>
    @forelse ($productRows as $idx => $p)
        <tr>
            <td style="text-align:center;">{{ $idx + 1 }}</td>
            <td>{{ $p['name'] }}</td>
            <td>{{ $p['sku'] }}</td>
            <td>{{ $p['category_name'] }}</td>
            <td style="mso-number-format:'\#\,\#\#0';">{{ (float) $p['unit_cost'] }}</td>
            <td style="mso-number-format:'\#\,\#\#0';">{{ (float) $p['unit_price'] }}</td>
            <td style="text-align:center;">{{ (float) $p['qty_sold'] }}</td>
            <td style="mso-number-format:'\#\,\#\#0';">{{ (float) $p['gross_sales'] }}</td>
            <td style="mso-number-format:'\#\,\#\#0';color:#DC2626;">{{ (float) $p['total_cost'] }}</td>
            <td style="mso-number-format:'\#\,\#\#0';font-weight:bold;color:#059669;">{{ (float) $p['gross_profit'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="10" style="text-align:center;color:#64748B;">Tidak ada data produk.</td>
        </tr>
    @endforelse
    <tr>
        <td colspan="6" style="font-weight:bold;text-align:right;background-color:#FFF7ED;">Subtotal Produk</td>
        <td style="font-weight:bold;text-align:center;background-color:#FFF7ED;">{{ (float) $productSummary['total_qty'] }}</td>
        <td style="font-weight:bold;text-align:right;background-color:#FFF7ED;mso-number-format:'\#\,\#\#0';">{{ (float) $productSummary['total_sales'] }}</td>
        <td style="font-weight:bold;text-align:right;background-color:#FFF7ED;mso-number-format:'\#\,\#\#0';">{{ (float) $productSummary['total_cogs'] }}</td>
        <td style="font-weight:bold;text-align:right;background-color:#FFF7ED;mso-number-format:'\#\,\#\#0';color:#059669;">{{ (float) $productSummary['total_profit'] }}</td>
    </tr>
    <tr><td colspan="10"></td></tr>

    <!-- TABEL 2: RINCIAN PENJUALAN MASTER LAYANAN -->
    @if ($isSalon || !empty($serviceRows))
        <tr>
            <th colspan="10" style="background-color:#F97316;color:#FFFFFF;font-size:11pt;font-weight:bold;text-align:left;">2. RINCIAN LABA RUGI MASTER LAYANAN JASA (TREATMENT &amp; STYLING)</th>
        </tr>
        <tr>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:center;">No</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;">Nama Layanan</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;">Kategori</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:center;">Durasi</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:right;">Tarif Jasa</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:center;">Sesi Selesai</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:right;">Total Omzet</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:right;">Modal Bahan</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:right;">Komisi Staf</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:right;">Laba Bersih</th>
        </tr>
        @forelse ($serviceRows as $idx => $s)
            <tr>
                <td style="text-align:center;">{{ $idx + 1 }}</td>
                <td>{{ $s['name'] }}</td>
                <td>{{ $s['category_name'] }}</td>
                <td style="text-align:center;">{{ $s['duration_minutes'] }} mnt</td>
                <td style="mso-number-format:'\#\,\#\#0';">{{ (float) $s['tarif'] }}</td>
                <td style="text-align:center;">{{ (float) $s['sessions_count'] }}</td>
                <td style="mso-number-format:'\#\,\#\#0';">{{ (float) $s['gross_sales'] }}</td>
                <td style="mso-number-format:'\#\,\#\#0';color:#DC2626;">{{ (float) $s['total_material_cost'] }}</td>
                <td style="mso-number-format:'\#\,\#\#0';color:#D97706;">{{ (float) $s['total_commission'] }}</td>
                <td style="mso-number-format:'\#\,\#\#0';font-weight:bold;color:#059669;">{{ (float) $s['net_profit'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" style="text-align:center;color:#64748B;">Tidak ada data layanan.</td>
            </tr>
        @endforelse
        <tr>
            <td colspan="5" style="font-weight:bold;text-align:right;background-color:#FFF7ED;">Subtotal Layanan</td>
            <td style="font-weight:bold;text-align:center;background-color:#FFF7ED;">{{ (float) $serviceSummary['total_sessions'] }}</td>
            <td style="font-weight:bold;text-align:right;background-color:#FFF7ED;mso-number-format:'\#\,\#\#0';">{{ (float) $serviceSummary['total_sales'] }}</td>
            <td style="font-weight:bold;text-align:right;background-color:#FFF7ED;mso-number-format:'\#\,\#\#0';">{{ (float) $serviceSummary['total_material_cost'] }}</td>
            <td style="font-weight:bold;text-align:right;background-color:#FFF7ED;mso-number-format:'\#\,\#\#0';">{{ (float) $serviceSummary['total_commission'] }}</td>
            <td style="font-weight:bold;text-align:right;background-color:#FFF7ED;mso-number-format:'\#\,\#\#0';color:#059669;">{{ (float) $serviceSummary['total_profit'] }}</td>
        </tr>
        <tr><td colspan="10"></td></tr>
    @endif

    <!-- TABEL 3: BEBAN OPERASIONAL (OPEX) -->
    @if ($expenseRows->isNotEmpty())
        <tr>
            <th colspan="10" style="background-color:#F97316;color:#FFFFFF;font-size:11pt;font-weight:bold;text-align:left;">3. RINCIAN BEBAN OPERASIONAL TOKO (OPEX)</th>
        </tr>
        <tr>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:center;">No</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;">Tanggal</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;">Kategori Beban</th>
            <th colspan="2" style="background-color:#FED7AA;color:#9A3412;font-weight:bold;">Judul / Keperluan</th>
            <th colspan="2" style="background-color:#FED7AA;color:#9A3412;font-weight:bold;">Metode Bayar</th>
            <th colspan="2" style="background-color:#FED7AA;color:#9A3412;font-weight:bold;">Catatan</th>
            <th style="background-color:#FED7AA;color:#9A3412;font-weight:bold;text-align:right;">Jumlah Beban</th>
        </tr>
        @foreach ($expenseRows as $idx => $exp)
            <tr>
                <td style="text-align:center;">{{ $idx + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y') }}</td>
                <td>{{ $exp->category }}</td>
                <td colspan="2">{{ $exp->title }}</td>
                <td colspan="2">{{ $exp->payment_method }}</td>
                <td colspan="2">{{ $exp->notes ?: '-' }}</td>
                <td style="mso-number-format:'\#\,\#\#0';color:#DC2626;font-weight:bold;">{{ (float) $exp->amount }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="9" style="font-weight:bold;text-align:right;background-color:#FFF7ED;">Total Beban Operasional (OPEX)</td>
            <td style="font-weight:bold;text-align:right;background-color:#FFF7ED;mso-number-format:'\#\,\#\#0';color:#DC2626;">{{ (float) $totalOpex }}</td>
        </tr>
    @endif
</table>
</body>
</html>
