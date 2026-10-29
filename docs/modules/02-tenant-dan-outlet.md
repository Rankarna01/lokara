# Modul 02: Tenant Core, Multi-Outlet & Manajemen Karyawan

Dokumen spesifikasi untuk **Modul Tenant Core, Multi-Outlet, dan Manajemen Karyawan** aplikasi POS Lokara. Modul ini merupakan fondasi operasional tenant di Back Office, mencakup pengaturan outlet/cabang, hak akses staf dengan PIN kasir, serta manajemen shift dan kas fisik.

---

## 1. Identifikasi Modul

| Item | Deskripsi |
|---|---|
| **Kode Modul** | `MOD-02-TENANT-CORE` |
| **Nama Modul** | Tenant Core, Multi-Outlet & Workforce Management |
| **Panel Filament** | `AppPanelProvider` (`{tenant}.lokara.id/app`) |
| **Akses Role** | `tenant_owner`, `tenant_manager` |
| **Prioritas Rilis** | 🟢 MVP |

---

## 2. Struktur Navigasi & Menu (Filament Tenant Panel)

```
├── 📊 Dashboard Tenant               (/app)
├── 🏢 Pengaturan Usaha
│   ├── Profil Bisnis & Toko          (/app/business-profile)
│   ├── Cabang / Outlet               (/app/outlets)
│   └── Perangkat & Printer           (/app/devices)
├── 👥 Pengguna & Karyawan
│   ├── Karyawan & Staf               (/app/users)
│   ├── Peran & Hak Akses (Shield)    (/app/roles)
│   └── Log Aktivitas Staf            (/app/activity-logs)
└── 💵 Shift & Kas Kasir
    ├── Sesi Shift Berjalan           (/app/shifts/active)
    ├── Riwayat Shift                 (/app/shifts)
    └── Arus Kas Masuk/Keluar (Petty) (/app/cash-movements)
```

---

## 3. Kebutuhan Fungsional & Spesifikasi Fitur

### 3.1 Dashboard Tenant
- **Statistik Penjualan Real-Time**:
  - Penjualan bersih hari ini, jumlah transaksi, total item terjual.
  - Nilai rata-rata transaksi per pelanggan (Average Ticket Size).
  - Margin kotor hari ini (Penjualan − HPP Snapshot).
  - Perbandingan kinerja vs hari kemarin (%) dan minggu lalu.
- **Filter Multi-Outlet**:
  - Dropdown pemilih outlet untuk memfilter seluruh widget metrik, stok, dan grafik.
- **Alert Operasional**:
  - Indikator peringatan stok menipis di bawah *reorder point*.
  - Notifikasi bahan/barang yang mendekati tanggal kedaluwarsa ($H-7$).
  - Grafik jam sibuk (Peak Hours Heatmap) untuk manajemen rotasi staf.

### 3.2 Manajemen Multi-Outlet
- **Data Outlet / Cabang**:
  - Nama outlet, alamat lengkap, koordinat GPS, telepon, dan jam operasional.
  - Konfigurasi Pajak (PPN/PB1 daerah misal 10% atau 11%) dan Service Charge per outlet.
  - Pembulatan total tagihan (misal pembulatan ke kelipatan 100 terdekat).
- **Pengaturan Struk Kasir per Outlet**:
  - Logo toko untuk struk cetak (58mm / 80mm).
  - Teks Header (Nama Usaha, Alamat, Nomor Meja / WiFi).
  - Teks Footer (Pesan terima kasih, akun Instagram, link survei kepuasan).
- **Pengaturan Default Gudang Outlet**:
  - Setiap outlet memiliki minimal 1 gudang default untuk pemotongan stok otomatis saat penjualan kasir.

### 3.3 Manajemen Pengguna, Role & Hak Akses (Filament Shield)
- **Role Bawaan (Default Seeded Roles)**:
  - `Owner`: Akses penuh tanpa batasan fitur dan finansial.
  - `Manager`: Akses operasional outlet, persetujuan transfer stok, void/refund kasir, dan laporan harian.
  - `Cashier`: Hanya akses terminal POS dan laporan shift sendiri.
  - `Warehouse Staff`: Akses modul bahan, gudang, mutasi, opname, dan penerimaan PO.
  - `Accountant`: Akses laporan keuangan, laba rugi, dan pencatatan biaya operasional.
- **Otorisasi Outlet Granular**:
  - User dapat dibatasi hanya dapat mengakses outlet tertentu (`outlet_user` mapping).
- **Keamanan PIN Kasir (6 Digit PIN)**:
  - Setiap user memiliki PIN 6-digit terenkripsi untuk:
    - *Quick Login / Switch Cashier* pada layar sentuh POS tanpa logout browser.
    - Otorisasi manajer saat kasir melakukan tindakan berisiko (Void struk, Diskon manual besar, Buka laci tanpa transaksi).

### 3.4 Manajemen Shift & Kas (Cash Drawer Management)
- **Buka Shift Kasir (Shift Opening)**:
  - Kasir memilih outlet dan register kasir.
  - Input modal kas awal (*Starting Floating Cash*).
  - Sistem mencatat waktu mulai dan ID kasir.
- **Kas Masuk & Keluar Selama Shift (Petty Cash)**:
  - Kas Keluar: Pengeluaran kecil tak terduga (beli es batu darurat, bayar galon air, parkir).
  - Kas Masuk: Tambahan modal tengah hari dari owner.
  - Wajib input nominal, kategori, dan keterangan alasan.
- **Tutup Shift (Shift Closing & Reconciliation)**:
  - Hitung fisik uang tunai di laci kasir (*Blind Drop / Counted Cash*).
  - Sistem menampilkan kalkulasi otomatis:
    $$\text{Kas Diharapkan} = \text{Kas Awal} + \text{Penjualan Tunai} + \text{Kas Masuk} - \text{Kas Keluar}$$
  - Kalkulasi selisih kas fisik:
    $$\text{Selisih} = \text{Kas Aktual} - \text{Kas Diharapkan}$$
  - Cetak laporan ringkasan shift (*Shift Report*) ke printer struk thermal sebelum kasir pulang.

---

## 4. Struktur Database & Skema Tabel

```sql
CREATE TABLE outlets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NULL,
    address TEXT NULL,
    tax_rate DECIMAL(5,2) DEFAULT 0.00,       -- misal 11.00%
    service_rate DECIMAL(5,2) DEFAULT 0.00,   -- misal 5.00%
    rounding_rule VARCHAR(30) DEFAULT 'none',  -- 'floor_100', 'ceil_100', 'nearest_100'
    receipt_settings JSON NULL,               -- header, footer, show_logo, show_tax
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    UNIQUE KEY outlet_tenant_slug_unique (tenant_id, slug)
);

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NULL,
    pin_hash VARCHAR(255) NULL,               -- hashed 6-digit pin
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    UNIQUE KEY user_tenant_email_unique (tenant_id, email)
);

CREATE TABLE outlet_user (
    outlet_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (outlet_id, user_id),
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE shifts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    outlet_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    opened_at TIMESTAMP NOT NULL,
    closed_at TIMESTAMP NULL,
    opening_cash DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    expected_cash DECIMAL(12,2) NULL,
    actual_cash DECIMAL(12,2) NULL,
    difference DECIMAL(12,2) NULL,
    notes TEXT NULL,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE cash_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    shift_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('in', 'out') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    reason VARCHAR(255) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## 5. Implementasi Teknis & Service Classes

### 5.1 Filament Resources
- `OutletResource`: Manajemen data cabang, pengaturan struk dengan Live Preview layout thermal, dan konfigurasi pajak.
- `UserResource`: Manajemen data staf, penugasan role Shield, penugasan cabang yang diizinkan, dan set PIN kasir.
- `ShiftResource`: Riwayat shift terdahulu, filter kasir, perbandingan selisih kas fisik, dan reprint struk tutup kasir.

### 5.2 Action & Service Classes
- `App\Actions\Tenant\OpenShiftAction`: Memeriksa apakah user masih memiliki sesi shift aktif di outlet tersebut. Jika belum, buat record `shifts` baru berstatus `open`.
- `App\Actions\Tenant\CloseShiftAction`: Melakukan rekapitulasi total pembayaran tunai dari tabel `order_payments`, menambah mutasi kas masuk/keluar, membandingkan dengan `actual_cash` inputan kasir, dan menandai shift sebagai `closed`.
- `App\Services\Auth\PinVerificationService`: Layanan verifikasi PIN cepat untuk otorisasi aksi supervisor di POS.

