# Modul 08: Transaksi Penjualan, Pelanggan (CRM) & Promosi

Dokumen spesifikasi untuk **Modul Transaksi Penjualan Back Office, Manajemen Pelanggan (CRM), Poin Loyalti, dan Engine Promosi** pada platform POS Lokara. Modul ini berfokus pada pengawasan riwayat transaksi, retensi pelanggan, dan otomasi pemasaran.

---

## 1. Identifikasi Modul

| Item | Deskripsi |
|---|---|
| **Kode Modul** | `MOD-08-PENJUALAN-CRM-PROMOSI` |
| **Nama Modul** | Sales History, CRM, Loyalty Program & Promotion Engine |
| **Panel Filament** | `AppPanelProvider` (`{tenant}.lokara.id/app`) |
| **Akses Role** | `tenant_owner`, `tenant_manager`, `cashier` (read-only terbatas) |
| **Prioritas Rilis** | 🟢 MVP (Penjualan, Pelanggan Dasar, Promo) / 🟡 V2 (Poin Loyalti, Piutang) |

---

## 2. Struktur Navigasi & Menu (Filament Tenant Panel)

```
├── 💰 Penjualan & Order
│   ├── Riwayat Seluruh Transaksi      (/app/orders)
│   ├── Void & Pembatalan Transaksi    (/app/orders/voided)
│   └── Retur & Pengembalian Dana      (/app/orders/refunds)
├── 👥 Pelanggan & CRM
│   ├── Data Pelanggan                 (/app/customers)
│   ├── Grup Pelanggan (Member Tier)   (/app/customer-groups)
│   ├── Program Poin & Hadiah Loyalti  (/app/loyalty-points)
│   └── Piutang Pelanggan (Tempo)      (/app/customer-receivables)
└── 🏷️ Promosi & Diskon
    ├── Diskon & Voucher Promo         (/app/promotions)
    └── Paket Bundling & Happy Hour    (/app/bundles)
```

---

## 3. Kebutuhan Fungsional & Spesifikasi Fitur

### 3.1 Manajemen Riwayat Transaksi (Back Office)
- **Tabel Transaksi Terpadu**:
  - Filter komprehensif: Rentang tanggal/jam, outlet cabang, kasir yang bertugas, metode pembayaran (Tunai, QRIS, Kartu), dan status transaksi (`completed`, `voided`, `refunded`).
  - Pencarian cepat berdasarkan nomor struk atau nomor telepon pelanggan.
- **Detail Lengkap Transaksi**:
  - Daftar item yang dibeli, pilihan varian dan modifier, catatan barista/dapur.
  - Snapshot HPP pada saat transaksi untuk audit margin kotor per transaksi.
  - Bukti metode pembayaran dan riwayat pencetakan struk.
  - Aksi cetak ulang struk (*Reprint Receipt*) atau kirim ulang ke WhatsApp pelanggan.

### 3.2 Otorisasi Void & Pengembalian Dana (Refund / Return)
- **Alur Void Transaksi**:
  - Pembatalan transaksi hanya dapat dilakukan dengan otorisasi manajer/owner.
  - Kasir wajib memilih alasan void (misal: *Salah input menu*, *Pelanggan membatalkan*, *Pesanan tidak selesai*).
  - Sistem otomatis:
    - Mengembalikan saldo kas fisik di shift kasir jika dibayar tunai.
    - Mengembalikan stok bahan baku/barang jadi ke gudang outlet via kartu stok tipe `in` (koreksi void).
    - Membatalkan catatan omzet dan HPP pada laporan laba rugi.
- **Alur Refund Parsial**:
  - Pengembalian dana untuk item tertentu yang dikeluhkan pelanggan, dengan opsi apakah item tersebut dikembalikan ke stok fisik atau dibuang (*waste*).

### 3.3 Manajemen Data Pelanggan (CRM)
- Profil Pelanggan: Nama, Nomor WhatsApp/Telepon (sebagai ID unik), Alamat Email, Tanggal Lahir (untuk promo ulang tahun), Alamat Pengiriman.
- **Customer Lifetime Analytics**:
  - Total transaksi seumur hidup (*Total Spend*).
  - Frekuensi kunjungan dan rata-rata belanja per kunjungan.
  - Tanggal kunjungan terakhir (*Recency, Frequency, Monetary - RFM analysis*).
  - Produk favorit yang paling sering dipesan.

### 3.4 Grup Pelanggan & Program Poin Loyalti
- **Tingkatan Member (Member Tiers)**:
  - Klasifikasi: *Reguler*, *Silver*, *Gold*, *VIP*.
  - Diskon otomatis: Setiap kali kasir memilih pelanggan tier *Gold*, sistem otomatis memberikan diskon khusus 10%.
- **Sistem Akumulasi Poin**:
  - Aturan perolehan: Misal setiap belanja Rp 10.000 mendapatkan 1 poin.
  - Penukaran poin (*Point Redemption*): Poin dapat dipotongkan langsung sebagai pengurang nominal total belanja kasir (1 poin = Rp 100) atau ditukar voucher menu gratis.

### 3.5 Piutang Pelanggan (*Accounts Receivable* / Penjualan Tempo)
- Khusus untuk tenant grosir, laundry korporat, atau katering langganan:
  - Pelanggan dapat menyelesaikan transaksi kasir dengan metode pembayaran *Kredit / Tempo*.
  - Menetapkan batas limit piutang (*Credit Limit*) dan tanggal jatuh tempo per pelanggan.
  - Pencatatan pembayaran cicilan piutang dan penerbitan nota tanda terima pelunasan.

### 3.6 Engine Promosi & Diskon Fleksibel
Sistem menyediakan konfigurasi promosi berbasis aturan (*Rules Engine*):
1. **Tipe Diskon**:
   - Persentase (misal: Diskon 20%) atau Nominal Tetap (misal: Potongan Rp 15.000).
   - Diterapkan pada level seluruh transaksi atau level produk tertentu.
2. **Promo Beli X Gratis Y (*Buy 1 Get 1 Free*)**:
   - Contoh: Beli 1 Kopi Aren Large gratis 1 Donat Cokelat.
3. **Happy Hour & Syarat Terjadwal**:
   - Promo hanya aktif pada jam tertentu (misal: Senin - Kamis pukul 14:00 - 17:00).
   - Syarat minimum nominal belanja (misal: Minimal belanja Rp 100.000).
   - Batas kuota penggunaan (misal: Hanya untuk 50 pelanggan pertama per hari).
4. **Kupon Voucher Kode**:
   - Pelanggan/kasir memasukkan kode kupon unik (misal: `GAJIANHEMAT`) saat checkout.

---

## 4. Struktur Database & Skema Tabel

```sql
CREATE TABLE customer_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0.00,
    min_spend DECIMAL(12,2) DEFAULT 0.00,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    customer_group_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(150) NULL,
    birth_date DATE NULL,
    address TEXT NULL,
    loyalty_points INT DEFAULT 0,
    total_spent DECIMAL(14,2) DEFAULT 0.00,
    total_visits INT DEFAULT 0,
    last_visit_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_group_id) REFERENCES customer_groups(id) ON DELETE SET NULL,
    UNIQUE KEY customer_phone_unique (tenant_id, phone)
);

CREATE TABLE customer_receivables (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    paid_amount DECIMAL(12,2) DEFAULT 0.00,
    remaining_amount DECIMAL(12,2) NOT NULL,
    due_date DATE NOT NULL,
    status ENUM('unpaid', 'partially_paid', 'paid', 'overdue') DEFAULT 'unpaid',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

CREATE TABLE promotions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NULL,                        -- kode kupon opsional
    type ENUM('percentage', 'fixed_amount', 'bogo', 'bundle') NOT NULL,
    value DECIMAL(12,2) NOT NULL,                 -- % diskon atau nominal potongan
    min_purchase DECIMAL(12,2) DEFAULT 0.00,
    max_discount DECIMAL(12,2) NULL,              -- batas maksimal diskon jika %
    applicable_to ENUM('entire_order', 'specific_products', 'specific_categories') DEFAULT 'entire_order',
    rules JSON NULL,                              -- jadwal hari/jam, kuota, list product_id
    valid_from TIMESTAMP NOT NULL,
    valid_to TIMESTAMP NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    usage_count INT DEFAULT 0,
    max_usage INT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE void_audits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,             -- kasir yang mengajukan
    approved_by BIGINT UNSIGNED NOT NULL,         -- manajer yang memberi PIN
    reason VARCHAR(255) NOT NULL,
    restock_items BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
```

---

## 5. Implementasi Teknis & Service Classes

- `App\Actions\Orders\VoidOrderAction`:
  Memvalidasi PIN manajer, membatalkan order, membuat log audit `void_audits`, mengembalikan uang ke kasir shift, dan mengembalikan kuantitas stok bahan/barang.
- `App\Services\Promotion\PromotionEvaluator`:
  Service evaluator yang memeriksa keranjang belanja POS terhadap seluruh daftar promo aktif (apakah memenuhi jam tayang, minimum belanja, dan kuota) lalu menghitung potongan harga maksimal.
- `App\Actions\Crm\AccrueLoyaltyPointsAction`:
  Menghitung akumulasi poin reward dari nilai belanja bersih transaksi dan memperbarui status grup pelanggan secara berkala.

