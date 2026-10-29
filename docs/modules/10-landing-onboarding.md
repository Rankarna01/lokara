# Modul 10: Landing Page Publik & Self-Onboarding Tenant

Dokumen spesifikasi untuk **Modul Landing Page Publik, Katalog Harga Dinamis, dan Wizard Pendaftaran Mandiri (Self-Onboarding Tenant)** pada platform POS Lokara. Modul ini menjadi pintu masuk utama akuisisi calon pelanggan SaaS.

---

## 1. Identifikasi Modul

| Item | Deskripsi |
|---|---|
| **Kode Modul** | `MOD-10-LANDING-ONBOARDING` |
| **Nama Modul** | Public Landing Page, Pricing Showcase & Self-Service Onboarding |
| **Teknologi UI** | Laravel Blade + Tailwind CSS + Alpine.js (Public Domain: `lokara.id`) |
| **Akses Role** | Publik / Tamu (Guest) & Pengguna Baru |
| **Prioritas Rilis** | 🟢 MVP |

---

## 2. Struktur Navigasi & Arsitektur Halaman Publik

```
├── 🌐 Website Publik (lokara.id)
│   ├── Halaman Beranda (Landing Page)         (/)
│   ├── Solusi Industri:
│   │   ├── POS untuk Kafe & Restoran (F&B)    (/solusi/kafe-restoran)
│   │   ├── POS untuk Retail & Minimarket      (/solusi/retail-toko)
│   │   ├── POS untuk Laundry & Jasa           (/solusi/laundry-jasa)
│   │   └── POS untuk Barbershop & Salon       (/solusi/barbershop-salon)
│   ├── Daftar Harga & Paket Langganan         (/harga)
│   ├── Tentang Kami & Kontak Sales            (/kontak)
│   └── Legal (Syarat Ketentuan & Kebijakan)   (/legal/terms, /legal/privacy)
└── 🚀 Alur Pendaftaran (Onboarding)
    ├── Formulir Pendaftaran Mandiri           (/register)
    ├── Cek Ketersediaan Subdomain             (/api/check-subdomain)
    ├── Verifikasi WhatsApp OTP                (/verify-otp)
    └── Wizard Onboarding Pertama              ({subdomain}.lokara.id/app/onboarding)
```

---

## 3. Kebutuhan Fungsional & Spesifikasi Fitur

### 3.1 Halaman Beranda (*Main Landing Page*)
- **Hero Section**:
  - Headline memikat: *"POS Modern dengan Kalkulasi HPP & Stok Bahan Otomatis Pertama di Indonesia"*.
  - Sub-headline penjelasan fokus bisnis F&B dan retail.
  - Call to Action (CTA) Utama: Tombol *"Coba Gratis 14 Hari"* (tanpa perlu kartu kredit) dan *"Lihat Demo Kasir"*.
  - Mockup visual interaktif antarmuka Layar Kasir tablet dan Dasbor Laba Rugi Back Office.
- **Social Proof & Bukti Kredibilitas**:
  - Statistik jumlah cabang dan transaksi yang diproses.
  - Logo klien dan testimoni pemilik resto/kafe.
- **Fitur Unggulan Berfokus Nilai Tambah (*Value Propositions*)**:
  - *Kasir Sentuh Super Cepat*: Navigasi menu intuitif, pencarian kilat, split payment.
  - *Engine HPP & Resep Akurat*: Hitung modal per cup hingga ke satuan gram/ml.
  - *Stok Auto-Deduct & Deteksi Kebocoran*: Bahan otomatis terpotong saat pesanan dibayar.
  - *Laporan Laba Rugi Real-Time*: Pantau keuntungan kotor dan bersih harian dari HP.
- **Showcase Solusi per Industri**:
  - Tab interaktif yang menampilkan bagaimana POS Lokara menyesuaikan kebutuhan kafe (resep/meja), toko kelontong (barcode/grosir), laundry (kiloan/status pengerjaan), dan salon (komisi staf).

### 3.2 Tabel Harga Dinamis (*Dynamic Pricing Showcase*)
- Menampilkan data paket yang diambil langsung dari database Super Admin (`plans` table):
  - Toggle pilihan siklus penagihan: **Bayar Bulanan** vs **Bayar Tahunan (Hemat 20%)**.
  - Kolom perbandingan paket (Free/Trial, Basic, Pro, Business).
  - Matriks fitur: Jumlah outlet, jumlah user, batas produk, modul resep HPP, multi-gudang, prioritas support.
  - Tombol CTA *"Pilih Paket"* yang langsung mengarahkan ke form pendaftaran dengan pre-selected plan.

### 3.3 Formulir Pendaftaran Mandiri (*Self-Registration Flow*)
Proses pendaftaran dirancang agar selesai dalam waktu **kurang dari 2 menit**:
1. **Langkah 1: Identitas Bisnis & Akun**:
   - Nama Bisnis / Merk (misal: *Kopi Senja Bahagia*).
   - Kategori Jenis Usaha: Dropdown (F&B / Kafe, Retail / Toko, Laundry, Salon / Jasa, Grosir).
   - Nama Lengkap Pemilik (Owner).
   - Alamat Email Aktif.
   - Nomor WhatsApp Aktif (untuk verifikasi OTP).
   - Password Akun.
2. **Langkah 2: Pemilihan Subdomain Unik**:
   - Input slug subdomain: `[ kopisenja ] .lokara.id`.
   - Pemeriksaan real-time via AJAX/Livewire ke database: apakah subdomain masih tersedia atau sudah digunakan.
   - Pengecekan kata terlarang (*reserved keywords*: `admin`, `api`, `app`, `billing`, `root`, `pos`, `mail`, dll).
3. **Langkah 3: Verifikasi Keamanan OTP**:
   - Pengiriman 6-digit kode OTP ke nomor WhatsApp pemilik via WhatsApp Gateway (Fonnte/Wablas).
   - Input validasi OTP untuk mencegah pendaftaran bot / spam.

### 3.4 Mesin Otomasi Provisioning (*Automated Provisioning Pipeline*)
Segera setelah nomor OTP terverifikasi:
1. Menjalankan `ProvisionNewTenantAction`:
   - Membuat record baru di tabel `tenants` dengan status `trial` (masa aktif 14 hari).
   - Membuat domain mapping subdomain `{slug}.lokara.id`.
   - Membuat akun owner pada tabel `users` dengan hak akses role `Owner` (Filament Shield).
   - Menghubungkan paket langganan `Trial Plan` pada tabel `subscriptions`.
2. **Penyuntikan Template Data Awal (*Industry Starter Seeders*)**:
   - Mengambil preset dari `business_types`:
     - *Jika F&B*: Otomatis membuat satuan standar (Gram, Mililiter, Pcs, Porsi), kategori awal (Kopi, Makanan, Minuman), 3 contoh produk berresep lengkap dengan bahan baku awal, serta gudang utama "Dapur & Bar".
     - *Jika Retail*: Otomatis membuat satuan (Pcs, Box, Lusin), kategori retail, dan gudang utama "Toko".
3. Redirect instan ke URL backoffice tenant: `https://{subdomain}.lokara.id/app`.

### 3.5 Wizard Panduan Onboarding (*5-Minute Setup Wizard*)
Saat owner pertama kali login ke back office, sistem menyajikan modal interaktif 4 langkah:
- **Langkah 1**: Konfirmasi profil toko & unggah logo outlet.
- **Langkah 2**: Tambah atau sesuaikan produk & harga jual pertamamu.
- **Langkah 3**: Panduan input resep bahan baku & HPP (disertai video tutorial 60 detik).
- **Langkah 4**: Buka Layar Kasir (`/pos`) dan coba transaksi pertama!

---

## 4. Struktur Database & Model Pendukung

```sql
CREATE TABLE otp_verifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(50) NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    action VARCHAR(50) NOT NULL DEFAULT 'tenant_registration',
    payload JSON NULL,                            -- data registrasi sebelum di-commit
    expires_at TIMESTAMP NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX otp_phone_action_idx (phone, action)
);

CREATE TABLE landing_leads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(150) NULL,
    business_type VARCHAR(100) NULL,
    outlet_count INT DEFAULT 1,
    notes TEXT NULL,
    status ENUM('new', 'contacted', 'demo_scheduled', 'converted', 'closed') DEFAULT 'new',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## 5. Implementasi Teknis & Alur Kode

### 5.1 Route Definition (`routes/web.php`)
```php
Route::domain(config('app.domain', 'lokara.id'))->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('landing.home');
    Route::get('/harga', [LandingController::class, 'pricing'])->name('landing.pricing');
    Route::get('/solusi/{slug}', [LandingController::class, 'solution'])->name('landing.solution');
    
    // Onboarding Livewire Component
    Route::get('/register', \App\Livewire\Onboarding\TenantRegistrationWizard::class)->name('register');
});
```

### 5.2 Action & Service Classes
- `App\Actions\Auth\SendWhatsAppOtpAction`: Mengirimkan pesan verifikasi 6 digit ke nomor pendaftar.
- `App\Actions\Platform\ProvisionNewTenantAction`: Orkestrasi pembuatan database tenant, data seeder bisnis, dan trial subscription.
- `App\Http\Middleware\EnsureTenantSubscriptionActive`: Middleware di level panel tenant yang memeriksa status masa trial atau kelunasan invoice. Jika expired, redirect ke halaman penagihan (*Billing Lock Screen*).

