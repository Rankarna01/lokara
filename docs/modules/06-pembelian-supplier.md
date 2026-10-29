# Modul 06: Pembelian (Procurement), Supplier & Hutang

Dokumen spesifikasi untuk **Modul Pembelian, Manajemen Supplier, dan Hutang Dagang** pada aplikasi POS Lokara. Modul ini mengelola pengadaan bahan baku dan barang dagangan, mengontrol termin pembayaran vendor, serta memperbarui harga pokok persediaan secara otomatis.

---

## 1. Identifikasi Modul

| Item | Deskripsi |
|---|---|
| **Kode Modul** | `MOD-06-PEMBELIAN-SUPPLIER` |
| **Nama Modul** | Procurement, Supplier Management & Accounts Payable |
| **Panel Filament** | `AppPanelProvider` (`{tenant}.lokara.id/app`) |
| **Akses Role** | `tenant_owner`, `tenant_manager`, `warehouse_staff` |
| **Prioritas Rilis** | 🟢 MVP |

---

## 2. Struktur Navigasi & Menu (Filament Tenant Panel)

```
├── 🛒 Pembelian & Supplier
│   ├── Purchase Order (PO)            (/app/purchase-orders)
│   ├── Penerimaan Barang (GRN)        (/app/goods-receipts)
│   ├── Retur Pembelian                (/app/purchase-returns)
│   ├── Data Supplier / Vendor         (/app/suppliers)
│   ├── Hutang Supplier & Jadwal Bayar (/app/supplier-payables)
│   └── Riwayat Harga Beli Bahan       (/app/purchase-price-trends)
```

---

## 3. Kebutuhan Fungsional & Spesifikasi Fitur

### 3.1 Master Data Supplier
- Informasi Vendor: Nama Perusahaan/Toko, Kontak PIC, Nomor WhatsApp/Telepon, Email, Alamat Lengkap.
- Syarat & Termin Pembayaran (*Payment Terms*):
  - `cash`: Tunai di tempat saat barang sampai.
  - `net_7`, `net_14`, `net_30`: Kredit tempo (7, 14, 30 hari).
  - `cod`: Cash on delivery.
- Katalog Bahan Pasokan: Hubungan antara supplier dengan bahan baku yang biasa dipasok beserta harga kontrak terakhir.

### 3.2 Siklus Alur Pengadaan (Procurement Workflow)
Sistem menerapkan kontrol pengadaan bertahap:
```
[Purchase Request] ──> [Purchase Order (PO)] ──> [Goods Receipt (GRN)] ──> [Faktur & Pembayaran]
(Usulan Cabang)        (Persetujuan Owner/Mgr)   (Pemeriksaan Fisik)       (Pelunasan Kas/Bank)
```

1. **Purchase Order (PO)**:
   - Pemilihan Supplier, Gudang Tujuan Penerimaan, Tanggal Ekspektasi Kedatangan.
   - Daftar item (Bahan Baku / Produk Jadi), kuantitas dalam *purchase unit*, harga satuan, diskon pembelian, dan pajak faktur.
   - Status PO: `draft`, `submitted`, `approved`, `partially_received`, `completed`, `cancelled`.
   - Cetak PDF surat pesanan resmi untuk dikirim via WhatsApp/Email ke supplier.
2. **Penerimaan Barang (*Goods Receipt Note / GRN*)**:
   - Pencatatan fisik barang masuk oleh staf gudang.
   - Pemeriksaan kondisi barang (kuantitas diterima vs kuantitas rusak/ditolak).
   - Input nomor batch/lot dan tanggal kedaluwarsa bahan berumur pendek.
   - GRN otomatis memicu:
     - Penambahan saldo stok di tabel `stocks` dan pembuatan kartu stok mutasi tipe `in`.
     - Update status PO menjadi `completed` atau `partially_received`.
     - Pembuatan tagihan hutang dagang (*Accounts Payable*).

### 3.3 Integrasi Kritis: Pembaruan Harga Beli & Pemicu HPP Otomatis
Ini adalah salah satu integrasi paling vital antara Modul 06 dan Modul 04:
1. Saat GRN disimpan dengan harga satuan yang berbeda dari harga sebelumnya:
   - Sistem memperbarui kolom `last_cost` pada master bahan `ingredients`.
   - Menghitung ulang `avg_cost` (Weighted Moving Average) berdasarkan jumlah stok yang ada ditambah kuantitas barang baru.
   - Memicu event `IngredientCostChangedEvent`.
2. Antrean latar belakang (Redis Worker) menangkap event tersebut dan:
   - Menghitung ulang seluruh resep yang mengandung bahan tersebut.
   - Memperbarui `cost_per_portion` resep menu.
   - Memperbarui rasio Food Cost % dan mengirimkan alert jika margin menu menjadi tipis.

### 3.4 Retur Pembelian (*Purchase Return*)
- Pencatatan pengembalian bahan/barang rusak kepada supplier.
- Pilihan kompensasi retur:
  - Pemotongan nominal hutang faktur (*Debit Note*).
  - Pengembalian dana tunai (*Cash Refund*).
  - Penggantian barang baru (*Replacement*).

### 3.5 Manajemen Hutang Supplier & Pembayaran
- Pelacakan saldo hutang berjalan per supplier.
- Dasbor pengingat jatuh tempo pembayaran (*Due Date Tracker*): $H-3$, $H-1$, dan *Overdue*.
- Pencatatan pembayaran cicilan atau pelunasan hutang:
  - Sumber dana: Kas Kecil, Kas Toko, atau Rekening Bank.
  - Cetak bukti pengeluaran kas / voucher pembayaran.

### 3.6 Rekomendasi Pesan Ulang Cerdas (*Smart Reorder Suggestion*)
- Sistem menganalisis:
  - Saldo stok saat ini.
  - Batas stok minimum (*Reorder Point*).
  - Rata-rata pemakaian bahan harian selama 14 hari terakhir.
- Menghasilkan daftar rekomendasi kuantitas belanja yang dapat langsung di-generate menjadi draft Purchase Order (PO) hanya dengan 1 klik.

---

## 4. Struktur Database & Skema Tabel

```sql
CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    address TEXT NULL,
    payment_terms VARCHAR(50) DEFAULT 'cash', -- 'cash', 'net_7', 'net_14', 'net_30', 'cod'
    bank_name VARCHAR(100) NULL,
    bank_account_number VARCHAR(100) NULL,
    bank_account_holder VARCHAR(150) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE purchase_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    po_number VARCHAR(50) NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    order_date DATE NOT NULL,
    expected_delivery_date DATE NULL,
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    status ENUM('draft', 'submitted', 'approved', 'partially_received', 'completed', 'cancelled') DEFAULT 'draft',
    notes TEXT NULL,
    approved_by BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
);

CREATE TABLE purchase_order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_order_id BIGINT UNSIGNED NOT NULL,
    item_type VARCHAR(100) NOT NULL,              -- 'App\Models\Ingredient' atau 'App\Models\Product'
    item_id BIGINT UNSIGNED NOT NULL,
    qty_ordered DECIMAL(14,4) NOT NULL,
    qty_received DECIMAL(14,4) NOT NULL DEFAULT 0.0000,
    unit_id BIGINT UNSIGNED NOT NULL,
    unit_price DECIMAL(12,4) NOT NULL,
    subtotal DECIMAL(14,2) NOT NULL,
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(id)
);

CREATE TABLE goods_receipts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    receipt_number VARCHAR(50) NOT NULL,
    purchase_order_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    received_date DATE NOT NULL,
    invoice_number VARCHAR(100) NULL,             -- nomor nota/faktur fisik supplier
    total_received_cost DECIMAL(14,2) NOT NULL,
    notes TEXT NULL,
    received_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
);

CREATE TABLE supplier_payables (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    purchase_order_id BIGINT UNSIGNED NOT NULL,
    invoice_number VARCHAR(100) NULL,
    total_amount DECIMAL(14,2) NOT NULL,
    paid_amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    remaining_amount DECIMAL(14,2) NOT NULL,
    due_date DATE NOT NULL,
    status ENUM('unpaid', 'partially_paid', 'paid', 'overdue') DEFAULT 'unpaid',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id)
);

CREATE TABLE supplier_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    payable_id BIGINT UNSIGNED NOT NULL,
    payment_number VARCHAR(50) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,          -- 'cash', 'bank_transfer'
    amount DECIMAL(14,2) NOT NULL,
    payment_date DATE NOT NULL,
    reference_number VARCHAR(100) NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (payable_id) REFERENCES supplier_payables(id) ON DELETE CASCADE
);
```

---

## 5. Implementasi Teknis & Service Classes

- `App\Actions\Procurement\ReceiveGoodsReceiptAction`:
  Memvalidasi item fisik, menambah stok gudang via `RecordStockMovementAction`, memperbarui saldo PO, dan menerbitkan tagihan hutang supplier.
- `App\Actions\Procurement\RecalculateMovingAverageCostAction`:
  Menghitung harga rata-rata baru bahan baku dan memperbarui field `avg_cost` serta menerbitkan event update HPP.
- `App\Services\Procurement\ReorderRecommendationService`:
  Algoritma penentuan safety stock dan rekomendasi kuantitas restock bahan otomatis.

