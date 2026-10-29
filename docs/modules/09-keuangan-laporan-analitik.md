# Modul 09: Keuangan Operasional, Laba Rugi & Laporan Analitik

Dokumen spesifikasi untuk **Modul Keuangan Operasional, Biaya Pengeluaran (OPEX), Perhitungan Modal & BEP, serta Pusat Laporan Analitik** pada platform POS Lokara. Modul ini menyajikan visibilitas finansial menyeluruh bagi pemilik bisnis untuk memantau profitabilitas riil usaha mereka.

---

## 1. Identifikasi Modul

| Item | Deskripsi |
|---|---|
| **Kode Modul** | `MOD-09-KEUANGAN-LAPORAN` |
| **Nama Modul** | Operational Finance, P&L, BEP Analysis & Business Intelligence |
| **Panel Filament** | `AppPanelProvider` (`{tenant}.lokara.id/app`) |
| **Akses Role** | `tenant_owner`, `accountant`, `tenant_manager` (akses sebagian) |
| **Prioritas Rilis** | 🟢 MVP (Biaya, Laba Rugi Sederhana, Laporan Inti) / 🟡 V2 (BEP, Ekspor Akuntansi) |

---

## 2. Struktur Navigasi & Menu (Filament Tenant Panel)

```
├── 💵 Keuangan & Kas
│   ├── Biaya Operasional (Expenses)   (/app/expenses)
│   ├── Kategori Biaya Pengeluaran     (/app/expense-categories)
│   ├── Arus Kas Toko (Cash Flow)      (/app/cash-flow)
│   └── Modal Usaha & Analisis BEP     (/app/capital-investments)
└── 📈 Pusat Laporan & Analitik
    ├── Laporan Penjualan              (/app/reports/sales)
    ├── Laporan Laba Rugi (P&L)        (/app/reports/profit-loss)
    ├── Laporan HPP & Food Cost %      (/app/reports/cogs-margin)
    ├── Laporan Bahan: Teori vs Aktual (/app/reports/ingredient-variance)
    ├── Laporan Mutasi & Valuasi Stok  (/app/reports/inventory-valuation)
    ├── Laporan Rekap Pajak (PB1/PPN)  (/app/reports/taxes)
    └── Laporan Audit Kasir (Void/Disc)(/app/reports/cashier-audits)
```

---

## 3. Kebutuhan Fungsional & Spesifikasi Fitur

### 3.1 Pencatatan Beban Operasional (*Operational Expenses / OPEX*)
- Pencatatan seluruh arus keluar non-bahan baku:
  - Gaji dan upah karyawan.
  - Sewa tempat/ruko, iuran kebersihan & keamanan.
  - Tagihan utilitas: Listrik PLN, Air PDAM, Gas LPG, Internet WiFi.
  - Biaya pemasaran dan promosi (iklan medsos, cetak banner).
  - Pembelian perlengkapan habis pakai (sabun cuci piring, kantong plastik, serbet).
- **Beban Berulang (*Recurring Expenses*)**:
  - Konfigurasi otomatis biaya yang dibayarkan rutin setiap bulan/minggu (misal biaya internet setiap tanggal 5).
- Lampiran Bukti Transaksi: Foto struk / kuitansi fisik via upload media.

### 3.2 Laporan Laba Rugi (*Profit and Loss Statement*)
Sistem mengagregasi data dari seluruh modul secara otomatis:
$$\text{Pendapatan Penjualan Bersih} = \text{Kotor} - \text{Diskon} - \text{Refund}$$
$$\text{Laba Kotor (Gross Profit)} = \text{Penjualan Bersih} - \text{Total HPP Snapshot (COGS)}$$
$$\text{Total Biaya Operasional} = \sum (\text{Beban Pengeluaran OPEX})$$
$$\text{Laba Bersih Operasional (Net Profit)} = \text{Laba Kotor} - \text{Total Biaya Operasional}$$
- Dapat difilter harian, mingguan, bulanan, tahunan, atau rentang tanggal custom per cabang.

### 3.3 Analisis Modal Usaha & Break Even Point (BEP)
Fitur kalkulasi strategis bagi owner untuk memantau balik modal investasi awal:
- **Pencatatan Belanja Modal (*Capital Expenditure / CAPEX*)**:
  - Investasi Awal: Mesin espresso, grinder, kulkas chiller, renovasi interior toko, deposit sewa gedung, perizinan usaha.
  - Masa penyusutan aset (*Depreciation Schedule* per bulan).
- **Kalkulasi Titik Impas (BEP)**:
  - Klasifikasi Biaya Tetap (*Fixed Costs*: Gaji pokok, sewa) vs Biaya Variabel (*Variable Costs*: Bahan baku, kemasan per porsi).
  - Menghitung target minimal porsi menu yang harus terjual per hari/bulan agar bisnis tidak mengalami kerugian:
    $$\text{BEP (Unit Porsi)} = \frac{\text{Total Biaya Tetap Bulanan}}{\text{Harga Jual Rata-Rata} - \text{HPP Variabel per Porsi}}$$
  - Estimasi Payback Period (Waktu Balik Modal dalam bulan).

### 3.4 Laporan Pemakaian Bahan: Teoritis vs Aktual (*Variance Analysis*)
Ini adalah instrumen pengawasan paling ampuh untuk mendeteksi kebocoran dan pencurian di dapur/bar:
1. **Pemakaian Teoritis (Berdasarkan Resep)**:
   $$\text{Pemakaian Teoritis} = \sum (\text{Jumlah Menu Terjual} \times \text{Gram/ml Resep Standar})$$
2. **Pemakaian Aktual (Berdasarkan Fisik Gudang)**:
   $$\text{Pemakaian Aktual} = \text{Stok Awal} + \text{Pembelian Masuk} - \text{Stok Akhir Opname}$$
3. **Selisih / Varians (*Discrepancy*)**:
   $$\text{Selisih Pemakaian} = \text{Pemakaian Aktual} - \text{Pemakaian Teoritis}$$
   - Jika nilai selisih positif tinggi: Indikasi porsi barista tidak sesuai standar (*over-portioning*), bahan basi/tumpah tidak dicatat (*unrecorded waste*), atau terjadi kebocoran bahan baku.

### 3.5 Matriks Laporan Lengkap

| Kategori Laporan | Deskripsi & Informasi yang Ditampilkan | Format Ekspor |
|---|---|---|
| **Penjualan Multi-Dimensi** | Rekap omzet per cabang, per kategori menu, per kasir, jam sibuk (*Peak Hours*), perbandingan tunai vs QRIS vs kartu. | PDF, Excel |
| **HPP & Gross Margin** | Analisis margin per menu, food cost %, mendeteksi produk paling menguntungkan (*Star Products*) dan produk beban (*Dog Products*). | PDF, Excel |
| **Valuasi Persediaan** | Nilai uang yang terikat pada persediaan bahan baku dan barang jadi di seluruh gudang. | PDF, Excel |
| **Rekapitulasi Pajak** | Rekap dasar pengenaan pajak (DPP) dan total setoran pajak daerah (PB1 Restoran) atau PPN untuk pelaporan SPT. | PDF, Excel |
| **Audit Jejak Kasir** | Daftar seluruh pembatalan transaksi (*void*), pengembalian dana (*refund*), dan diskon manual beserta alasan dan supervisor yang menyetujui. | PDF, Excel |

### 3.6 Ekspor Format Akuntansi
- Penyediaan format ekspor data jurnal umum transaksi (Akun Kas, Penjualan, HPP, Persediaan, Biaya) yang siap diimpor (*ready to import*) ke software akuntansi populer Indonesia: **Jurnal.id**, **Accurate Online**, dan **Zahir Accounting**.

---

## 4. Struktur Database & Skema Tabel

```sql
CREATE TABLE expense_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    outlet_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    expense_number VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    amount DECIMAL(14,2) NOT NULL,
    expense_date DATE NOT NULL,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'cash', -- 'cash', 'bank_transfer'
    receipt_attachment_url VARCHAR(255) NULL,
    is_recurring BOOLEAN DEFAULT FALSE,
    recurring_period ENUM('weekly', 'monthly', 'yearly') NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id)
);

CREATE TABLE capital_investments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    outlet_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,                   -- misal: 'Mesin Kopi La Marzocco 2 Group'
    amount DECIMAL(14,2) NOT NULL,
    investment_date DATE NOT NULL,
    depreciation_months INT DEFAULT 36,           -- masa manfaat penyusutan dalam bulan
    residual_value DECIMAL(14,2) DEFAULT 0.00,
    monthly_depreciation DECIMAL(14,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE
);

CREATE TABLE daily_sales_summaries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    outlet_id BIGINT UNSIGNED NOT NULL,
    summary_date DATE NOT NULL,
    gross_sales DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    discount_total DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    net_sales DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    total_cogs DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    gross_profit DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    total_tax DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    total_service DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    total_expenses DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    net_profit DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    order_count INT NOT NULL DEFAULT 0,
    customer_count INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    UNIQUE KEY daily_summary_unique (tenant_id, outlet_id, summary_date)
);
```

---

## 5. Implementasi Teknis & Kinerja Reporting

### 5.1 Agregasi Latar Belakang (Scheduled Job)
Untuk menjaga performa query tetap instan saat tenant memiliki ratusan ribu transaksi:
- Perintah terjadwal harian (`php artisan lokara:aggregate-daily-summaries`) dijalankan pada pukul 01:00 dini hari via Laravel Scheduler untuk mengisi tabel `daily_sales_summaries`.
- Dasbor analitik membaca data agregat yang sudah matang dari tabel ringkasan ini, sehingga rendering grafik statistik berlangsung dalam hitungan milidetik.

### 5.2 Action & Report Services
- `App\Services\Reports\ProfitLossReportService`: Menghitung kalkulasi P&L dinamis berdasarkan filter rentang tanggal.
- `App\Services\Reports\IngredientVarianceReportService`: Menjalankan komparasi formula resep vs mutasi fisik gudang untuk menghasilkan persentase selisih kebocoran bahan.
- `App\Actions\Export\GenerateAccountingExportAction`: Menghasilkan berkas CSV/XLSX dengan mapping chart of accounts (COA) untuk Jurnal/Accurate.

