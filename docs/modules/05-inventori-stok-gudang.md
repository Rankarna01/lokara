# Modul 05: Manajemen Multi-Gudang, Stok Ledger & Opname

Dokumen spesifikasi teknis untuk **Modul Manajemen Multi-Gudang, Kartu Stok (Stock Ledger), dan Stok Opname** pada platform POS Lokara. Modul ini menjamin integritas kuantitas dan nilai persediaan barang jadi maupun bahan baku secara *real-time*.

---

## 1. Identifikasi Modul

| Item | Deskripsi |
|---|---|
| **Kode Modul** | `MOD-05-INVENTORI-STOK` |
| **Nama Modul** | Multi-Warehouse Inventory, Stock Ledger & Physical Opname |
| **Panel Filament** | `AppPanelProvider` (`{tenant}.lokara.id/app`) |
| **Akses Role** | `tenant_owner`, `tenant_manager`, `warehouse_staff` |
| **Prioritas Rilis** | 🟢 MVP |

---

## 2. Struktur Navigasi & Menu (Filament Tenant Panel)

```
├── 🏬 Gudang & Persediaan
│   ├── Posisi & Saldo Stok            (/app/stocks)
│   ├── Kartu Stok / Mutasi (Ledger)   (/app/stock-movements)
│   ├── Lokasi Gudang & Dapur          (/app/warehouses)
│   ├── Transfer Antar Gudang/Cabang   (/app/stock-transfers)
│   ├── Stok Opname (Fisik)            (/app/stock-opnames)
│   ├── Penyesuaian & Waste Log        (/app/stock-adjustments)
│   └── Batch, Lot & Expired (FEFO)    (/app/stock-batches)
```

---

## 3. Kebutuhan Fungsional & Spesifikasi Fitur

### 3.1 Multi-Gudang & Lokasi Penyimpanan
Sistem mengelola relasi fisik antara cabang (*outlet*) dan titik penyimpanan persediaan:
- **Tipe Gudang**:
  - `central`: Gudang Pusat (tempat penerimaan barang dari supplier dalam partai besar).
  - `kitchen`: Dapur / Bar (tempat bahan baku siap diolah).
  - `display`: Rak pajang kasir (khusus barang jadi retail).
  - `storage`: Gudang cadangan outlet.
- Setiap outlet wajib memiliki 1 gudang utama (*default selling warehouse*) yang dituju oleh terminal POS saat pemotongan stok otomatis.

### 3.2 Dual Stock Tracking (Bahan Baku & Barang Jadi)
Tabel stok (`stocks`) melacak dua entitas secara bersamaan melalui hubungan polymorphic:
1. **Bahan Baku (`Ingredient`)**: Dicatat dalam *usage unit* (gram, ml, pcs) untuk auto-deduct resep.
2. **Barang Jadi / Retail (`ProductVariant` / `Product`)**: Dicatat dalam satuan jual (pcs, botol, kaleng).

### 3.3 Kartu Stok Lengkap (*Immutable Stock Ledger*)
Setiap penambahan atau pengurangan stok WAJIB membuat satu baris catatan riwayat (*ledger movement*), tidak boleh ada perubahan saldo tanpa jejak audit:
- **Tipe Pergerakan (`movement_type`)**:
  - `in`: Penerimaan pembelian supplier (*Goods Receipt*).
  - `sale`: Penjualan otomatis dari POS (*Auto-deduct*).
  - `transfer_in` / `transfer_out`: Pengiriman dan penerimaan mutasi antar gudang.
  - `opname`: Penyesuaian selisih hitung fisik.
  - `waste`: Bahan tumpah, basi, atau terbuang.
  - `sample`: Pemakaian untuk promosi / cicip / food testing.
  - `internal`: Pemakaian operasional (contoh: kopi untuk staf).

### 3.4 Auto-Deduct Saat Penjualan POS
Saat transaksi berhasil diselesaikan di layar kasir:
1. Sistem mengidentifikasi tipe produk:
   - Jika `item` (barang jadi): langsung memotong kuantitas stok produk bersangkutan di gudang outlet.
   - Jika `recipe` (menu berresep): sistem mengambil formula BOM produk (dan varian/modifier terkait), menghitung pemakaian bahan, dan memotong stok bahan baku (`ingredients`) di gudang dapur outlet.
2. Dilakukan dalam satu transaksi database (`DB::transaction`) dengan kunci pesimistik (`lockForUpdate`) untuk menghindari stok minus saat ramai antrean.

### 3.5 Transfer Antar Gudang / Outlet (Workflow 3 Tahap)
Perpindahan stok antar cabang membutuhkan mekanisme otorisasi yang ketat:
```
[Permintaan / Request] ──> [Pengiriman / Dispatch] ──> [Penerimaan / Receive]
(Stok Draft)               (Stok In-Transit)           (Stok Masuk Gudang Tujuan)
```
- Menghindari kehilangan barang selama perjalanan (catatan selisih barang rusak/hilang di jalan).

### 3.6 Stok Opname & Analisis Selisih
- **Prosedur Hitung Fisik**:
  - Mode *Blind Count*: Petugas gudang memasukkan jumlah fisik riil tanpa melihat jumlah saldo sistem agar hasil tidak dimanipulasi.
- **Kalkulasi Selisih Otomatis**:
  $$\text{Selisih Qty} = \text{Qty Fisik} - \text{Qty Sistem}$$
  $$\text{Nilai Kerugian Selisih} = \text{Selisih Qty} \times \text{Avg Cost}$$
- **Persetujuan Manajer (*Approval*)**:
  - Selisih yang disetujui otomatis membuat transaksi penyesuaian stok (*Adjustment Ledger*) untuk menyeimbangkan angka saldo sistem dengan fisik.

### 3.7 Manajemen Batch / Lot & FEFO (*First Expired, First Out*)
- Khusus tenant F&B dengan bahan baku berumur pendek (susu, daging, telur):
  - Penerimaan barang mencatat nomor batch dan tanggal kedaluwarsa (*expired date*).
  - Sistem menginstruksikan staf untuk mengeluarkan batch yang paling dekat dengan tanggal kedaluwarsa.
  - Peringatan dini di dashboard jika ada stok yang akan kedaluwarsa dalam 3–7 hari.

### 3.8 Laporan Valuasi Persediaan
- Menghitung nilai total aset kekayaan persediaan tenant saat ini:
  $$\text{Total Nilai Persediaan} = \sum (\text{Qty Saldo Stok} \times \text{Harga Beli Rata-Rata / Avg Cost})$$
- Dapat difilter per gudang dan per kategori bahan/barang.

---

## 4. Struktur Database & Skema Tabel

```sql
CREATE TABLE warehouses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    outlet_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NOT NULL,
    type ENUM('central', 'kitchen', 'display', 'storage') DEFAULT 'storage',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    UNIQUE KEY warehouse_code_unique (tenant_id, code)
);

CREATE TABLE stocks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    stockable_type VARCHAR(100) NOT NULL,        -- 'App\Models\Ingredient' atau 'App\Models\Product'
    stockable_id BIGINT UNSIGNED NOT NULL,
    qty DECIMAL(14,4) NOT NULL DEFAULT 0.0000,
    avg_cost DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    UNIQUE KEY stock_item_unique (warehouse_id, stockable_type, stockable_id)
);

CREATE TABLE stock_batches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    stock_id BIGINT UNSIGNED NOT NULL,
    batch_no VARCHAR(100) NOT NULL,
    qty DECIMAL(14,4) NOT NULL DEFAULT 0.0000,
    cost DECIMAL(12,4) NOT NULL,
    expired_at DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (stock_id) REFERENCES stocks(id) ON DELETE CASCADE
);

CREATE TABLE stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    stockable_type VARCHAR(100) NOT NULL,
    stockable_id BIGINT UNSIGNED NOT NULL,
    type ENUM('in', 'sale', 'transfer_in', 'transfer_out', 'opname', 'waste', 'sample', 'internal') NOT NULL,
    qty DECIMAL(14,4) NOT NULL,                   -- nilai positif untuk masuk, negatif untuk keluar
    unit_cost DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
    total_cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    balance_after DECIMAL(14,4) NOT NULL,         -- saldo stok setelah mutasi
    reference_type VARCHAR(100) NULL,             -- 'App\Models\Order', 'App\Models\StockTransfer'
    reference_id BIGINT UNSIGNED NULL,
    notes VARCHAR(255) NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    INDEX movement_stockable_idx (tenant_id, stockable_type, stockable_id),
    INDEX movement_created_idx (tenant_id, created_at)
);

CREATE TABLE stock_transfers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    transfer_number VARCHAR(50) NOT NULL,
    from_warehouse_id BIGINT UNSIGNED NOT NULL,
    to_warehouse_id BIGINT UNSIGNED NOT NULL,
    status ENUM('draft', 'dispatched', 'received', 'cancelled') DEFAULT 'draft',
    dispatched_at TIMESTAMP NULL,
    received_at TIMESTAMP NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (from_warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (to_warehouse_id) REFERENCES warehouses(id)
);

CREATE TABLE stock_opnames (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    opname_number VARCHAR(50) NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    status ENUM('in_progress', 'completed', 'cancelled') DEFAULT 'in_progress',
    total_system_cost DECIMAL(14,2) DEFAULT 0.00,
    total_physical_cost DECIMAL(14,2) DEFAULT 0.00,
    total_diff_cost DECIMAL(14,2) DEFAULT 0.00,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
);
```

---

## 5. Implementasi Teknis & Action Classes

- `App\Actions\Stock\DeductStockFromRecipeAction`:
  Mengeksekusi pemotongan stok bahan dari pesanan kasir secara atomik.
- `App\Actions\Stock\RecordStockMovementAction`:
  Menyimpan kartu stok mutasi, menghitung saldo akhir, serta memperbarui tabel ringkasan `stocks`.
- `App\Actions\Stock\ProcessStockOpnameApprovalAction`:
  Menghitung selisih hasil fisik, membuat penyesuaian kartu stok mutasi tipe `opname`, dan merefleksikan kerugian/keuntungan selisih ke buku besar.

