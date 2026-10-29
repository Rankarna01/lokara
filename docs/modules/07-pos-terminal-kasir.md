# Modul 07: POS Terminal Kasir (Layar Penjualan & Pembayaran)

Dokumen spesifikasi untuk **Modul Layar Kasir / POS Terminal (Point of Sale)** pada platform Lokara. Modul ini adalah antarmuka garda terdepan (*front-facing interface*) yang digunakan kasir setiap hari untuk melayani transaksi secara cepat, responsif, dan akurat.

---

## 1. Identifikasi Modul

| Item | Deskripsi |
|---|---|
| **Kode Modul** | `MOD-07-POS-TERMINAL` |
| **Nama Modul** | Cashier POS Terminal, Sales Screen & Checkout |
| **Teknologi UI** | Custom Full-Screen Livewire 3 + Alpine.js + Tailwind CSS |
| **Route Path** | `https://{tenant}.lokara.id/pos` |
| **Akses Role** | `cashier`, `tenant_manager`, `tenant_owner` |
| **Prioritas Rilis** | 🟢 MVP (Kritis) |

---

## 2. Desain Antarmuka & Layout POS

Layar kasir menggunakan layout layar penuh (*full-screen SPA*) yang dioptimalkan untuk tablet dan monitor touchscreen (aspek rasio 16:9 atau 4:3), terbagi menjadi dua panel utama:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  [🏠 Backoffice]  [Shift: Siang - Rian]  [Outlet: Cabang Tebet]   [🟢 Online] [🔒 Lock]│
├───────────────────────────────────┬────────────────────────────────────────────────────┤
│  PANEL KIRI: KERANJANG PESANAN    │  PANEL KANAN: KATALOG & PENCARIAN                  │
│                                   │                                                    │
│  [Dine-In ▾] [Meja: 04 ▾] [Cust: +│  [ 🔍 Cari nama menu / scan barcode...           ] │
│  ──────────────────────────────── │  [Semua] [☕ Kopi] [🍔 Makanan] [🥤 Non-Kopi] [🍰] │
│  Item Pesanan:                    │  ────────────────────────────────────────────────  │
│  • 2x Caramel Macchiato  Rp 64.000│  ┌───────────┐ ┌───────────┐ ┌───────────┐        │
│    - Large (+Rp 6.000)            │  │ [Foto]    │ │ [Foto]    │ │ [Foto]    │        │
│    - Oat Milk (+Rp 7.000)         │  │ Espresso  │ │ Latte     │ │ Croissant │        │
│    - Less Sugar 50%               │  │ Rp 18.000 │ │ Rp 28.000 │ │ Rp 22.000 │        │
│    [✏️ Catatan: extra panas]       │  └───────────┘ └───────────┘ └───────────┘        │
│  • 1x Beef Burger        Rp 45.000│  ┌───────────┐ ┌───────────┐ ┌───────────┐        │
│                                   │  │ [Foto]    │ │ [Foto]    │ │ [Foto]    │        │
│  ──────────────────────────────── │  │ Aren Latte│ │ Lemon Tea │ │ Red Velvet│        │
│  Subtotal:             Rp 109.000 │  │ Rp 24.000 │ │ Rp 18.000 │ │ Rp 32.000 │        │
│  PB1 Pajak (10%):       Rp 10.900 │  └───────────┘ └───────────┘ └───────────┘        │
│  Diskon Promo:        - Rp 10.000 │                                                    │
│  TOTAL AKHIR:          Rp 109.900 │                                                    │
│  ──────────────────────────────── │                                                    │
│  [🗑️ Batal] [⏸️ Simpan] [💳 BAYAR] │  [◀ Halaman 1 dari 4 ▶]                            │
└───────────────────────────────────┴────────────────────────────────────────────────────┘
```

---

## 3. Kebutuhan Fungsional & Spesifikasi Fitur

### 3.1 Navigasi Katalog & Pencarian Kilat
- **Grid Produk Visual**:
  - Kartu produk dilengkapi thumbnail foto beresolusi optimal, nama produk, dan harga jual cabang.
  - Indikator visual badge jika stok produk/bahan habis (*Sold Out*) sehingga tombol tidak dapat diklik.
- **Filter Kategori Dinamis**:
  - Tombol tab horizontal dengan icon kategori untuk beralih kelompok produk dalam 1 kali klik.
- **Pencarian Cepat & Barcode Scanning**:
  - Kolom pencarian dengan input listener instan (debounce 250ms).
  - Integrasi hardware *USB / Bluetooth Barcode Scanner*: scanner memicu event `Enter` otomatis yang langsung memasukkan item ke keranjang belanja tanpa perlu memfokuskan kursor.

### 3.2 Manajemen Keranjang Belanja (*Cart Management*)
- **Penyesuaian Kuantitas Cepat**:
  - Tombol `+` dan `−` pada setiap baris item, atau klik langsung nominal angka untuk mengetik kuantitas besar.
  - Geser (*swipe*) atau tombol ikon sampah untuk menghapus item dari keranjang.
- **Catatan Khusus per Item (*Item Notes*)**:
  - Tombol *"Add Note"* pada item untuk instruksi barista/dapur (misal: *"Kurang manis"*, *"Tanpa daun bawang"*, *"Es dipisah"*).
  - Catatan ini otomatis tercetak pada struk dapur (*Kitchen Checker*).
- **Modal Pemilih Varian & Modifier**:
  - Jika produk yang diklik memiliki varian (misal: *Ukuran*) atau grup modifier (misal: *Topping / Pilihan Susu*), sistem memunculkan modal dialog elegan:
    - Pilihan varian radio button (*Single choice*).
    - Pilihan modifier checkbox (*Multiple choice* dengan validasi minimum/maksimum seleksi).
    - Tombol *"Tambahkan ke Keranjang"* yang memperbarui total harga varian + modifier.

### 3.3 Penanganan Meja & Jenis Pesanan
- **Tipe Pesanan (*Order Type*)**:
  - `dine_in`: Makan di tempat. Memunculkan pilihan nomor meja atau denah lantai.
  - `take_away`: Bawa pulang.
  - `delivery`: Pesanan pesan antar online (Grab/Gojek/Kurir toko).
- **Hold & Recall Order (Simpan Pesanan Tertunda / Open Bill)**:
  - Kasir dapat menahan transaksi berjalan jika pelanggan ingin menambah menu nanti atau antrean terhambat.
  - Daftar transaksi tertunda dapat dibuka kembali (*Recall*) dari daftar *Open Orders*.

### 3.4 Engine Pembayaran Multifungsi (*Checkout & Payment*)
Modal pembayaran dirancang untuk kecepatan transaksi di bawah 10 detik:

1. **Pembayaran Tunai (*Cash*)**:
   - Kolom input nominal uang tunai yang diterima.
   - Tombol nominal cepat (*Quick Cash Buttons*): **Uang Pas**, **Rp 20.000**, **Rp 50.000**, **Rp 100.000**, **Rp 200.000**.
   - Kalkulasi nilai uang kembalian (*Change Due*) otomatis dengan teks berukuran besar agar kasir tidak salah memberikan kembalian.
2. **QRIS Dinamis (*Dynamic QRIS*)**:
   - Sistem melakukan request ke API Payment Gateway (Midtrans/Xendit/Duitku) untuk menerbitkan string QRIS unik per transaksi.
   - Layar menampilkan QR Code. Kasir dapat membalikkan layar tablet atau menampilkannya di *Customer Facing Display*.
   - WebSocket listener otomatis mendeteksi ketika pembayaran sudah berstatus `settled` dan melanjutkan ke layar sukses.
3. **Kartu Debit / Kredit & E-Wallet**:
   - Input nomor referensi transaksi / Approval Code dari mesin EDC bank fisik.
4. **Split Payment (Pembayaran Terpisah)**:
   - Pelanggan dapat membayar sebagian tunai (misal Rp 50.000) dan sisanya menggunakan QRIS atau Kartu Debit.

### 3.5 Layar Konfirmasi Berhasil (*Payment Successful Screen*)
- Tampilan konfirmasi grafis centang hijau dengan detail:
  - Nomor struk transaksi (misal: `#INV-20261029-0042`).
  - Total nominal pembayaran & nominal uang kembalian.
- Tombol Aksi Utama:
  - **Cetak Struk Thermal (Print Receipt)**.
  - **Kirim Struk WhatsApp**: Form nomor HP pelanggan untuk mengirimkan bukti pembayaran berformat PDF/teks langsung ke WhatsApp pelanggan via API.
  - **Transaksi Baru (New Sale)**: Mengosongkan keranjang dan kembali ke katalog.

### 3.6 Integrasi Cetak Struk Thermal (ESC/POS)
Sistem mendukung 3 mode komunikasi printer thermal 58mm / 80mm:
- **Network / LAN / WiFi Printer**: Mengirim byte raw ESC/POS via soket TCP/IP ke IP address printer di jaringan lokal dapur/kasir (menggunakan `mike42/escpos-php`).
- **USB / Bluetooth Printer**: Integrasi WebUSB API langsung dari browser Google Chrome atau via aplikasi perantara Android (RawBT Print Service).
- **Buzzer Laci Uang (*Cash Drawer Kick*)**: Perintah byte ESC/POS `\x1B\x70\x00\x19\xFA` dikirimkan secara otomatis saat pembayaran tunai selesai untuk membuka laci kasir fisik.

### 3.7 Keamanan & Otorisasi Manajer (PIN Authorization)
Tindakan kasir yang berpotensi menimbulkan kerugian atau manipulasi wajib meminta popup otorisasi PIN 6-digit Manajer/Owner:
- **Void Struk**: Pembatalan pesanan yang sudah tercetak atau tersimpan.
- **Diskon Manual di Luar Batas**: Memberikan diskon bebas di atas 10%.
- **Buka Laci Tanpa Transaksi (*No-Sale Open Drawer*)**.
- **Refund Dana**: Pengembalian uang pelanggan karena komplain.

---

## 4. Struktur Database & Model Transaksi POS

```sql
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    outlet_id BIGINT UNSIGNED NOT NULL,
    shift_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,             -- kasir pembuat
    customer_id BIGINT UNSIGNED NULL,
    order_number VARCHAR(50) NOT NULL,
    order_type ENUM('dine_in', 'take_away', 'delivery') DEFAULT 'dine_in',
    table_number VARCHAR(30) NULL,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    service_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    rounding_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_cost_snapshot DECIMAL(12,2) NOT NULL DEFAULT 0.00, -- Total HPP pesanan ini
    status ENUM('pending', 'completed', 'voided', 'refunded') DEFAULT 'pending',
    notes TEXT NULL,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY order_number_unique (tenant_id, order_number),
    INDEX order_outlet_date_idx (tenant_id, outlet_id, created_at)
);

CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED NULL,
    qty DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    unit_price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    cost_snapshot DECIMAL(12,4) NOT NULL DEFAULT 0.0000, -- HPP riil per porsi saat transaksi
    notes VARCHAR(255) NULL,                             -- catatan barista/koki
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE RESTRICT
);

CREATE TABLE order_item_modifiers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_item_id BIGINT UNSIGNED NOT NULL,
    modifier_id BIGINT UNSIGNED NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    cost_snapshot DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE,
    FOREIGN KEY (modifier_id) REFERENCES modifiers(id) ON DELETE RESTRICT
);

CREATE TABLE order_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    payment_method VARCHAR(50) NOT NULL,          -- 'cash', 'qris', 'debit', 'credit', 'transfer'
    amount DECIMAL(12,2) NOT NULL,
    cash_tendered DECIMAL(12,2) NULL,             -- uang yang diserahkan pelanggan
    change_due DECIMAL(12,2) NULL,                -- uang kembalian
    reference_number VARCHAR(100) NULL,
    gateway_response JSON NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
```

---

## 5. Implementasi Teknis Livewire & Services

### 5.1 Komponen Livewire Utama
- `App\Livewire\Pos\Terminal`:
  Komponen utama pengontrol state katalog produk, tab kategori, keranjang belanja (*cart array*), dan modal kalkulator pembayaran.
- `App\Livewire\Pos\Modals\VariantModifierModal`:
  Komponen modal popup untuk pemilihan varian dan modifier grup produk.
- `App\Livewire\Pos\Modals\PinAuthModal`:
  Komponen otorisasi PIN supervisor/manajer untuk aksi berisiko.

### 5.2 Actions & Services
- `App\Actions\Orders\CreatePosOrderAction`:
  Menjalankan transaksi database atomik: simpan order, simpan snapshot HPP, kurangi persediaan via `DeductStockFromRecipeAction`, dan catat pembayaran.
- `App\Services\Printing\ThermalReceiptPrinter`:
  Menyusun struktur layout struk thermal (header, item, catatan mod, pajak, total, footer) dan menembakkannya langsung ke port TCP printer thermal kasir dan dapur.

