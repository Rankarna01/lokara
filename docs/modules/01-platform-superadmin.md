# Modul 01: Platform & SaaS Super Admin

Dokumen spesifikasi untuk **Modul Super Admin Platform** aplikasi POS Lokara. Modul ini dikhususkan bagi pemilik platform SaaS untuk mengelola tenant, paket langganan, billing, preset jenis usaha, serta memonitor stabilitas sistem.

---

## 1. Identifikasi Modul

| Item | Deskripsi |
|---|---|
| **Kode Modul** | `MOD-01-SUPERADMIN` |
| **Nama Modul** | Platform & SaaS Management |
| **Panel Filament** | `AdminPanelProvider` (`admin.lokara.id`) |
| **Akses Role** | `super_admin` (Auth guard: `platform`) |
| **Prioritas Rilis** | 🟢 MVP (Tenant, Plan, Billing) / 🟡 V2 (Business Template, Support) |

---

## 2. Struktur Navigasi & Menu (Filament Admin Panel)

Berikut adalah struktur menu sidebar pada Panel Super Admin:

```
├── 📊 Dashboard Platform            (/)
├── 🏢 Manajemen Bisnis
│   ├── Daftar Tenant                (/tenants)
│   ├── Pengguna Lintas Tenant       (/tenant-users)
│   └── Template Jenis Usaha         (/business-types)
├── 💳 Langganan & Billing
│   ├── Paket Langganan              (/plans)
│   ├── Langganan Aktif              (/subscriptions)
│   ├── Tagihan & Invoice            (/invoices)
│   ├── Riwayat Transaksi Payment    (/payments)
│   └── Kupon Diskon                 (/coupons)
├── 📢 Komunikasi & Support
│   ├── Broadcast Pengumuman         (/announcements)
│   └── Tiket Bantuan                (/support-tickets)
└── ⚙️ Pengaturan & Sistem
    ├── Konfigurasi Platform         (/settings/general)
    ├── Gateway & Integrasi          (/settings/integrations)
    ├── Log Aktivitas Platform       (/activity-logs)
    └── Server & Queue Health        (/system/health)
```

---

## 3. Kebutuhan Fungsional & Spesifikasi Fitur

### 3.1 Dashboard Super Admin
- **Metrik Utama (Stats Overview Widget)**:
  - Total Tenant (Aktif, Trial, Menunggu Pembayaran, Terisolir/Suspended, Expired).
  - Monthly Recurring Revenue (MRR) dan Annual Recurring Revenue (ARR).
  - Gross Merchandise Value (GMV) gabungan seluruh transaksi POS platform.
  - Jumlah tenant baru bulan ini vs churn rate.
  - Tenant yang masa aktifnya habis dalam 7 hari ke depan.
- **Visualisasi Grafik**:
  - Tren pertumbuhan tenant (Area chart mingguan/bulanan).
  - Grafik distribusi paket langganan (Donut chart).
  - Queue status, latency jobs, dan error rate (via integrasi Laravel Pulse / Horizon).

### 3.2 Manajemen Tenant & Provisioning
- **Pendaftaran Tenant Manual & Otomatis**:
  - Form CRUD Tenant: Nama Bisnis, Jenis Usaha (F&B, Retail, Laundry, Barbershop, Grosir), Subdomain slug (`{subdomain}.lokara.id`), kontak PIC, nomor WhatsApp, email, dan NPWP.
  - Custom domain provisioning (CNAME record validation).
- **Kontrol Status Tenant**:
  - `trial`, `active`, `suspended`, `grace_period`, `expired`, `cancelled`.
  - Tombol Aksi Cepat: **Suspend** (kunci akses POS & Backoffice), **Aktifkan**, **Perpanjang Trial**, **Reset Demo Data**.
- **Fitur Impersonasi (Login As Tenant Owner)**:
  - Super admin dapat masuk ke panel tenant bersangkutan tanpa mengetahui password owner untuk investigasi komplain atau bantuan teknis (menggunakan `filament-impersonate` / custom token).
- **Limit Penggunaan (Feature Flags & Limits)**:
  - Override batas maksimal outlet, kasir, produk, dan transaksi per bulan khusus tenant tertentu.

### 3.3 Paket Langganan & Add-on (Subscription Engine)
- **Master Plan**:
  - Nama paket: Free/Trial, Basic, Pro, Business, Enterprise.
  - Siklus penagihan: Bulanan dan Tahunan (dengan diskon tahunan misal diskon 2 bulan).
  - Matriks Fitur (JSON feature flags):
    - `max_outlets` (integer)
    - `max_users` (integer)
    - `max_products` (integer)
    - `allow_recipe_bom` (boolean)
    - `allow_multi_warehouse` (boolean)
    - `allow_accounting_export` (boolean)
    - `allow_custom_domain` (boolean)
    - `allow_open_api` (boolean)
- **Add-on Manajemen**:
  - Pembelian tambahan kuota outlet atau kuota user tanpa harus naik paket.

### 3.4 Billing & Integrasi Payment Gateway Platform
- **Automated Invoicing**:
  - Invoice dibuat otomatis $H-7$ sebelum langganan jatuh tempo.
  - Invoice status: `unpaid`, `paid`, `overdue`, `cancelled`.
- **Payment Gateway Platform**:
  - Integrasi Midtrans / Xendit / Duitku (QRIS, BCA VA, Mandiri VA, BRI VA, GoPay, OVO).
  - Webhook listener otomatis memperpanjang tanggal `ends_at` dan mengaktifkan tenant saat pembayaran lunas.
- **Notifikasi Otomatis**:
  - Reminder tagihan via WhatsApp (Wablas/Fonnte) dan Email otomatis pada $H-7$, $H-3$, $H-0$, dan saat overdues.

### 3.5 Template Jenis Usaha (Business Type Presets)
Fitur unggulan agar platform Lokara modular untuk beragam industri:
- Preset **F&B (Kafe/Resto)**: Membawa default satuan (gram, ml, porsi), kategori (Kopi, Makanan, Minuman), flag BOM resep aktif, opsi meja makan.
- Preset **Retail**: Barcode scanner aktif, multi-satuan (pcs, pack, karton), supplier tracking, BOM nonaktif.
- Preset **Laundry**: Layanan per kg / per keping, status pengerjaan (Cuci, Kering, Setrika, Selesai), bahan operasional (deterjen, pewangi).
- Preset **Salon / Barbershop**: Layanan potong/treatment, komisi kapster, produk retail shampoo.

---

## 4. Struktur Database & Model

```sql
CREATE TABLE platform_users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'super_admin',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE business_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL,
    default_settings JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE tenants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    business_type_id BIGINT UNSIGNED NOT NULL,
    owner_id BIGINT UNSIGNED NULL,
    status ENUM('trial', 'active', 'suspended', 'expired', 'cancelled') DEFAULT 'trial',
    trial_ends_at TIMESTAMP NULL,
    custom_domain VARCHAR(255) NULL,
    settings JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (business_type_id) REFERENCES business_types(id)
);

CREATE TABLE plans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    price_monthly DECIMAL(12,2) NOT NULL DEFAULT 0,
    price_yearly DECIMAL(12,2) NOT NULL DEFAULT 0,
    limits JSON NOT NULL,
    features JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    plan_id BIGINT UNSIGNED NOT NULL,
    billing_cycle ENUM('monthly', 'yearly') DEFAULT 'monthly',
    status ENUM('active', 'past_due', 'cancelled', 'expired') DEFAULT 'active',
    starts_at TIMESTAMP NOT NULL,
    ends_at TIMESTAMP NOT NULL,
    cancelled_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id)
);

CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    subscription_id BIGINT UNSIGNED NULL,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    discount_amount DECIMAL(12,2) DEFAULT 0,
    tax_amount DECIMAL(12,2) DEFAULT 0,
    total_amount DECIMAL(12,2) NOT NULL,
    status ENUM('unpaid', 'paid', 'overdue', 'cancelled') DEFAULT 'unpaid',
    due_date DATE NOT NULL,
    paid_at TIMESTAMP NULL,
    pdf_path VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    payment_gateway VARCHAR(50) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(255) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'settlement', 'expire', 'deny', 'refund') NOT NULL,
    payload JSON NULL,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);
```

---

## 5. Implementasi Teknis Laravel & Filament

### 5.1 Filament Resources
- `TenantResource`: Form pendaftaran tenant, pengelolaan status, kartu batasan penggunaan, relasi doman, serta Action Impersonasi.
- `PlanResource`: Konfigurasi paket langganan dan fitur JSON builder.
- `InvoiceResource`: Detail tagihan, tombol kirim ulang pengingat WhatsApp, preview bukti bayar.
- `BusinessTypeResource`: Konfigurasi preset template jenis usaha.

### 5.2 Action Classes Utama
- `App\Actions\Platform\ProvisionNewTenantAction`: Membuat tenant, seeding role awal (Owner, Manager, Cashier), menginjeksi template data awal berdasarkan jenis usaha terpilih, dan membuat langganan trial.
- `App\Actions\Platform\HandlePaymentWebhookAction`: Memvalidasi signature payment gateway (Midtrans/Xendit), memperbarui status invoice ke `paid`, dan memperpanjang masa berlaku langganan tenant.
- `App\Actions\Platform\ImpersonateTenantOwnerAction`: Mengautentikasi super admin ke dalam guard tenant untuk sesi support.

