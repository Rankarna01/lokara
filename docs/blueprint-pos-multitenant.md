# Blueprint Produk POS Multi-Tenant (SaaS)

> Aplikasi POS + Manajemen Stok + Perhitungan HPP/Modal Bahan Baku, dengan Super Admin untuk mengelola tenant, dibangun menggunakan **Laravel + Filament**.

---

## 1. Ringkasan Produk

| Item | Keterangan |
|---|---|
| Model bisnis | SaaS multi-tenant (langganan bulanan/tahunan) |
| Target pasar | F&B (kafe, resto), retail, laundry, salon, jasa, toko bahan/grosir |
| Pengguna utama | Super Admin (pemilik platform), Owner tenant, Manajer, Kasir, Staf Gudang |
| Fokus utama | Kasir cepat, stok akurat, **HPP per bahan & per produk**, laporan laba rugi |
| Platform | Web (responsif, PWA) — bisa dipakai di tablet, HP, dan PC kasir |

---

## 2. Teknologi (Tech Stack)

### 2.1 Inti

| Layer | Teknologi | Catatan |
|---|---|---|
| Framework | **Laravel 11/12** | PHP 8.3+ |
| Admin & Dashboard | **Filament 3** (atau 4 jika stabil) | Panel Super Admin & Panel Tenant dipisah |
| Interaktif UI | Livewire 3 + Alpine.js | Bawaan Filament |
| Layar Kasir (POS) | Livewire custom page + Tailwind CSS | Halaman khusus, bukan resource Filament biasa |
| Database | **MySQL 8 / MariaDB** atau PostgreSQL | PostgreSQL lebih kuat untuk laporan |
| Cache & Queue | **Redis** + Laravel Horizon | Untuk antrean laporan, email, struk |
| Multi-tenancy | **stancl/tenancy** *atau* **spatie/laravel-multitenancy** | Lihat bagian 3 |
| Auth & Role | Filament Shield (**bezhansalleh/filament-shield**) + spatie/laravel-permission | Role & permission per tenant |
| Penyimpanan file | Laravel Filesystem → S3 / MinIO / Cloudflare R2 | Foto produk, logo, invoice |

### 2.2 Package Pendukung

| Kebutuhan | Package / Layanan |
|---|---|
| Export Excel/PDF | `maatwebsite/excel`, `barryvdh/laravel-dompdf` atau `spatie/laravel-pdf` |
| Cetak struk thermal | `mike42/escpos-php`, atau RawBT / WebUSB / QZ Tray |
| Barcode / QR | `milon/barcode`, `endroid/qr-code` |
| Audit log | `spatie/laravel-activitylog` |
| Backup | `spatie/laravel-backup` |
| Media | `spatie/laravel-medialibrary` + plugin Filament |
| Pembayaran langganan | **Midtrans / Xendit / Duitku** (QRIS, VA, e-wallet) |
| Notifikasi WhatsApp | Fonnte / Wablas / WhatsApp Cloud API |
| Email | Mailgun / Resend / SMTP |
| Monitoring | Sentry, Laravel Telescope (dev), Laravel Pulse |
| Testing | Pest + Laravel Dusk |
| Offline mode (opsional, fase lanjut) | PWA + IndexedDB + sync queue |

### 2.3 Infrastruktur

- **Server**: VPS (Ubuntu 22/24) + Nginx + PHP-FPM, atau Laravel Forge / Ploi untuk mempermudah deploy.
- **CI/CD**: GitHub Actions → deploy otomatis.
- **Domain**: wildcard subdomain `*.namaproduk.com` untuk tiap tenant, plus dukungan custom domain.
- **SSL**: Let's Encrypt wildcard (via Cloudflare DNS).

---

## 3. Arsitektur Multi-Tenant

### 3.1 Pilihan Strategi

| Strategi | Kelebihan | Kekurangan | Rekomendasi |
|---|---|---|---|
| **Single DB, kolom `tenant_id`** | Sederhana, murah, mudah migrasi | Risiko kebocoran data jika lupa scope, performa turun saat data besar | ✅ Awal (MVP) sampai ratusan tenant |
| **Database per tenant** | Isolasi kuat, mudah backup/restore per tenant | Migrasi lebih berat, biaya naik | ✅ Jika target tenant menengah/enterprise |
| Schema per tenant (PostgreSQL) | Kompromi keduanya | Kompleks | Opsional |

> **Saran:** mulai dengan **single database + `tenant_id`** (global scope otomatis via trait `BelongsToTenant`), tapi tulis kode agar mudah dipindah ke database-per-tenant bila diperlukan. Pakai `stancl/tenancy` jika ingin opsi DB-per-tenant sejak awal.

### 3.2 Struktur Panel Filament

```
┌─────────────────────────────────────────────────────────────┐
│  Landing Page (Public)      →  namaproduk.com               │
├─────────────────────────────────────────────────────────────┤
│  Panel Super Admin          →  admin.namaproduk.com         │
│  (Filament panel "admin")      Kelola semua tenant          │
├─────────────────────────────────────────────────────────────┤
│  Panel Tenant (Back Office) →  {tenant}.namaproduk.com/app  │
│  (Filament panel "app")        Produk, stok, laporan, dll   │
├─────────────────────────────────────────────────────────────┤
│  Layar Kasir (POS)          →  {tenant}.namaproduk.com/pos  │
│  (Livewire full-screen)        UI cepat seperti gambar      │
└─────────────────────────────────────────────────────────────┘
```

### 3.3 Hierarki Data

```
Tenant (Bisnis)
 └── Outlet / Cabang (banyak)
      ├── Gudang / Lokasi Stok
      ├── Perangkat (printer, terminal)
      ├── Karyawan & Shift
      └── Transaksi
```

---

## 4. Daftar Modul

### LEGENDA PRIORITAS
- 🟢 **MVP** — wajib ada di rilis pertama
- 🟡 **V2** — rilis berikutnya
- 🔵 **V3** — pengembangan lanjutan

---

## A. MODUL PLATFORM (SUPER ADMIN)

Panel khusus pemilik platform. Hanya bisa diakses user dengan role `super_admin`.

### A1. 🟢 Dashboard Super Admin
- Total tenant (aktif, trial, suspended, expired)
- MRR / ARR (pendapatan langganan)
- Tenant baru per bulan, churn rate
- Grafik transaksi seluruh platform (GMV)
- Tenant yang akan expired dalam 7 hari
- Status server / antrean / error terbaru

### A2. 🟢 Manajemen Tenant
- CRUD tenant (nama bisnis, jenis usaha, subdomain, kontak, alamat, NPWP)
- Status: `trial`, `active`, `suspended`, `expired`, `cancelled`
- **Aksi cepat**: suspend, aktifkan, perpanjang trial, reset data demo
- **Impersonate** (login sebagai owner tenant untuk membantu support)
- Batas penggunaan per tenant (jumlah outlet, user, produk, transaksi/bulan)
- Pengaturan domain: subdomain otomatis + custom domain
- Catatan internal (CRM ringan) per tenant
- Provisioning otomatis: saat tenant daftar → buat tenant, seed role, kategori default, template bisnis

### A3. 🟢 Manajemen Akun Tenant
- Daftar semua user lintas tenant (cari berdasarkan email/HP)
- Reset password, paksa logout semua sesi, nonaktifkan akun
- Verifikasi email / nomor HP
- Pantau login terakhir & perangkat
- Pemindahan kepemilikan (transfer owner)
- Log aktivitas akun (login gagal, perubahan role, dll)

### A4. 🟢 Paket & Langganan (Subscription)
- CRUD paket (Free/Trial, Basic, Pro, Business, Enterprise)
- Atur **fitur & limit per paket** (feature flags): jumlah outlet, user, produk, modul HPP, multi-gudang, API, dll
- Siklus tagihan: bulanan, tahunan, diskon tahunan
- Add-on (outlet tambahan, user tambahan, modul tambahan)
- Kupon & promo code
- Masa trial & grace period

### A5. 🟢 Billing & Invoice Platform
- Invoice otomatis per periode
- Integrasi payment gateway (QRIS, VA, e-wallet, kartu)
- Webhook pembayaran → aktivasi/perpanjangan otomatis
- Reminder jatuh tempo via email & WhatsApp
- Riwayat pembayaran, refund, kredit saldo
- Faktur pajak / bukti bayar (PDF)

### A6. 🟡 Template Jenis Usaha (Business Type Template)
Agar satu produk bisa dipakai banyak bidang usaha:
- Preset **F&B**: kategori menu, modifier, resep/BOM, meja, dapur
- Preset **Retail/Toko**: barcode, varian, satuan bertingkat, supplier
- Preset **Laundry/Jasa**: layanan per kg/per item, status pengerjaan
- Preset **Salon/Barbershop**: layanan, komisi terapis, booking
- Preset **Grosir/Distributor**: harga bertingkat, piutang, sales
- Super Admin bisa membuat/mengedit preset (kategori awal, satuan, pajak, label istilah)

### A7. 🟡 Pengaturan Global Platform
- Nama aplikasi, logo, warna (white-label)
- Konfigurasi payment gateway, email, WhatsApp
- Pajak default (PPN 11%), mata uang, zona waktu
- Syarat & Ketentuan, Kebijakan Privasi
- Mode maintenance per tenant / global
- Feature flag global

### A8. 🟡 Support & Komunikasi
- Pengumuman / banner broadcast ke semua tenant (update fitur, maintenance)
- Tiket bantuan dari tenant
- Template email & WhatsApp
- Changelog / release notes

### A9. 🟡 Monitoring & Audit
- Activity log seluruh platform
- Penggunaan resource per tenant (storage, jumlah transaksi, API call)
- Health check queue, scheduler, backup terakhir
- Export data tenant (kepatuhan / permintaan pelanggan)

### A10. 🔵 Super Admin Lanjutan
- Manajemen **reseller/agen** (komisi referral)
- Marketplace add-on / integrasi
- API key & webhook management lintas tenant
- Laporan keuangan platform (laba rugi SaaS)

---

## B. MODUL TENANT (BACK OFFICE)

Panel untuk owner/manajer tiap tenant.

### B1. 🟢 Dashboard Tenant
Seperti kartu ringkasan pada gambar:
- Penjualan hari ini, jumlah transaksi, jumlah pelanggan, rata-rata nilai transaksi
- Persentase perubahan vs kemarin / minggu lalu
- Grafik penjualan per jam / hari / bulan
- Produk terlaris, kategori terlaris
- **Stok menipis** & bahan hampir kedaluwarsa
- Laba kotor hari ini (berdasarkan HPP)
- Filter per outlet

### B2. 🟢 Manajemen Outlet / Cabang
- CRUD outlet (alamat, jam operasional, pajak, service charge)
- Pengaturan struk per outlet (header, footer, logo)
- Pengaturan printer & perangkat
- Hak akses user per outlet

### B3. 🟢 Manajemen Pengguna, Role & Izin
- CRUD user tenant (owner, manajer, kasir, gudang, akuntan)
- Role & permission granular (lihat harga modal, void, diskon, refund, dll)
- PIN kasir untuk login cepat
- Batasi user ke outlet tertentu
- Log aktivitas user

### B4. 🟢 Master Produk (Menu / Barang / Jasa)
- Tipe produk: **Barang jadi**, **Produk berresep (BOM)**, **Jasa**, **Paket/Bundle**
- Kategori & sub-kategori
- SKU, barcode, foto, deskripsi
- Varian (ukuran, warna) & **modifier/add-on** (extra shot, topping)
- Satuan jual (cup, pcs, kg) + konversi satuan
- Harga jual per outlet, harga bertingkat (grosir/member)
- Pajak & service charge per produk
- Aktif/nonaktif per outlet, jadwal ketersediaan
- Import/Export Excel massal

### B5. 🟢 Master Bahan Baku (Ingredient / Raw Material)
- Kode, nama, kategori bahan (protein, sayur, kemasan, dll)
- **Satuan beli** (karung, dus, liter) & **satuan pakai** (gram, ml, pcs) + faktor konversi
- Harga beli terakhir, harga rata-rata, supplier utama
- Stok minimum / titik pesan ulang (reorder point)
- Masa simpan / kedaluwarsa
- Flag: bahan utama, bahan pendukung, kemasan, bahan overhead
- Dipakai lintas jenis usaha (F&B, laundry: deterjen; salon: shampoo, dll)

### B6. 🟢 Resep / Bill of Materials (BOM) & HPP
**Modul inti diferensiasi produk Anda.**
- Resep per produk: daftar bahan + qty pakai + satuan
- Resep bertingkat (sub-resep / bahan setengah jadi, mis. *saus*, *adonan*)
- **Yield & waste/susut** (mis. daging menyusut 15%)
- Biaya tambahan per porsi: tenaga kerja, listrik/gas, kemasan, overhead
- **Perhitungan HPP otomatis** per porsi = Σ(qty × harga bahan) + biaya tambahan
- Pilihan metode harga bahan: **harga terakhir**, **rata-rata bergerak (weighted average)**, atau **FIFO**
- Simulasi: *"Jika harga bahan X naik 10%, HPP & margin jadi berapa?"*
- Margin & **food cost %** per produk, saran harga jual (target margin)
- Riwayat perubahan HPP (trend)
- Peringatan produk margin rendah / rugi

### B7. 🟢 Manajemen Stok & Gudang
- Multi-gudang / multi-lokasi (gudang pusat, dapur, outlet)
- Stok **bahan baku** dan **barang jadi** dalam satu sistem
- Kartu stok (stock ledger): setiap mutasi tercatat (masuk, keluar, transfer, koreksi)
- **Auto-deduct**: saat penjualan, stok bahan terpotong sesuai resep
- Transfer stok antar gudang/outlet (dengan approval)
- **Stok opname** (penghitungan fisik) + selisih & penyesuaian
- Penyesuaian: waste, rusak, expired, sampel, pemakaian internal
- Batch/lot & tanggal kedaluwarsa (FEFO)
- Notifikasi stok menipis & hampir kedaluwarsa
- Valuasi persediaan (nilai stok saat ini)

### B8. 🟢 Pembelian & Supplier
- Master supplier (kontak, termin pembayaran, bahan yang dipasok)
- **Purchase Request → Purchase Order → Penerimaan Barang (GRN)**
- Retur pembelian
- Update harga beli otomatis → memicu **hitung ulang HPP**
- Hutang supplier & jadwal pembayaran
- Riwayat harga beli per bahan per supplier
- Rekomendasi pesan ulang berdasarkan stok minimum & pemakaian

### B9. 🟢 Penjualan & Transaksi
- Daftar semua transaksi (filter outlet, kasir, tanggal, metode bayar)
- Detail transaksi + cetak ulang struk
- Void / refund / retur (dengan otorisasi & alasan)
- Order online / pesanan masuk (fase lanjut)
- Penjualan tertunda (open bill / hold)

### B10. 🟢 Pelanggan & Loyalti
- Master pelanggan (nama, HP, email, tanggal lahir)
- Riwayat pembelian & total belanja
- Grup pelanggan (reguler, member, VIP) → harga/diskon khusus
- 🟡 Poin loyalti, voucher, kartu member
- 🟡 Piutang pelanggan (untuk grosir/tempo)
- 🟡 Kampanye WhatsApp/email (promo, ulang tahun)

### B11. 🟢 Diskon, Promo & Pajak
- Diskon per item, per transaksi, persentase/nominal
- Promo: beli X gratis Y, happy hour, bundling, kupon
- Jadwal & syarat promo (jam, hari, outlet, minimum belanja)
- Pajak (PPN/PB1) & service charge, termasuk/tidak termasuk harga
- Pembulatan

### B12. 🟢 Shift & Kas (Cash Management)
- Buka shift (modal awal kas) & tutup shift
- Kas masuk/keluar selama shift
- Rekonsiliasi: kas seharusnya vs kas aktual → selisih
- Laporan shift per kasir

### B13. 🟢 Keuangan & Biaya Operasional
- Pencatatan biaya operasional (sewa, gaji, listrik, marketing)
- Kategori biaya, biaya berulang (recurring)
- Pembayaran hutang supplier
- **Laba Rugi sederhana**: Penjualan − HPP − Biaya = Laba bersih
- Arus kas harian/bulanan
- 🟡 **Perhitungan Modal Usaha / Break Even Point (BEP)**
  - Modal awal (investasi peralatan, renovasi, stok awal)
  - Biaya tetap vs variabel
  - Estimasi balik modal (ROI/payback period)
- 🟡 Ekspor ke format akuntansi / integrasi Jurnal.id, Accurate, Zahir

### B14. 🟢 Laporan
| Kelompok | Laporan |
|---|---|
| Penjualan | Harian, per produk, per kategori, per kasir, per outlet, per jam sibuk, per metode bayar |
| Stok | Kartu stok, posisi stok, stok menipis, nilai persediaan, mutasi, selisih opname, kedaluwarsa |
| **HPP & Margin** | HPP per produk, food cost %, margin per produk, perbandingan harga jual vs HPP, trend harga bahan |
| **Pemakaian Bahan** | Pemakaian teoritis (berdasarkan resep) vs aktual (opname) → deteksi kebocoran/waste |
| Pembelian | Per supplier, per bahan, hutang, kenaikan harga |
| Keuangan | Laba rugi, arus kas, biaya operasional |
| Pelanggan | Pelanggan teratas, frekuensi kunjungan |
| Pajak | Rekap PPN/PB1 |
| Audit | Void, refund, diskon, aktivitas user |
- Semua laporan: filter, **ekspor PDF/Excel**, jadwal kirim otomatis via email

### B15. 🟡 Pengaturan Tenant
- Profil bisnis, logo, NPWP
- Template struk & invoice
- Metode pembayaran (tunai, QRIS, kartu, transfer, e-wallet)
- Pengaturan satuan, pajak, pembulatan
- Integrasi (printer, payment, WhatsApp, marketplace)
- Pengaturan metode HPP (terakhir / rata-rata / FIFO)
- Backup & ekspor data
- Langganan & tagihan tenant (lihat paket, upgrade, riwayat invoice)

### B16. 🔵 Modul Lanjutan Tenant
- **Meja & Reservasi** (F&B): denah meja, split bill, gabung meja
- **Kitchen Display System (KDS)**: pesanan masuk ke layar dapur
- **Self-order QR**: pelanggan pesan lewat HP
- **Online ordering** & integrasi GoFood/GrabFood/ShopeeFood
- **Booking & Jadwal** (salon/jasa), komisi terapis
- **Manajemen Karyawan**: absensi, gaji, komisi
- **Produksi** (central kitchen): order produksi, hasil, biaya produksi
- **Multi-currency**
- **API publik & webhook**
- **Aplikasi mobile** (Flutter) / mode offline penuh

---

## C. MODUL KASIR (POS TERMINAL)

Halaman full-screen khusus (mengikuti desain referensi): **kiri = keranjang, kanan = katalog produk.**

### C1. 🟢 Layar Penjualan
- Katalog produk berbentuk grid + foto, filter kategori (Semua, Kopi, Makanan, Minuman, Dessert)
- Pencarian cepat + **scan barcode**
- Keranjang: ubah qty (+/−), hapus item, **catatan per item** ("Add Note")
- Pilih varian & modifier saat tambah item
- Subtotal, pajak, service charge, diskon, **total**
- Tombol **Clear Cart** & **Pay**
- Hold / simpan pesanan, lanjutkan pesanan tertunda
- Pilih pelanggan, tipe order (dine-in / take away / delivery)
- Pilih meja (opsional)

### C2. 🟢 Pembayaran
- Tunai (hitung kembalian + tombol nominal cepat)
- QRIS dinamis, kartu debit/kredit, transfer, e-wallet
- **Split payment** (bayar sebagian tunai, sebagian QRIS)
- Layar **"Payment Successful"** (seperti perangkat di gambar)
- Cetak struk / kirim struk via WhatsApp / email

### C3. 🟢 Operasional Kasir
- Buka/tutup shift
- Riwayat transaksi shift berjalan, cetak ulang struk
- Void/refund (dengan PIN manajer)
- Kas masuk/keluar
- Indikator status koneksi & printer

### C4. 🟡 Kemampuan Tambahan
- Layar pelanggan kedua (customer display)
- **Mode offline** (transaksi tetap tersimpan lalu sinkron saat online)
- Shortcut keyboard untuk kasir cepat
- Mode gelap / terang

---

## D. LANDING PAGE (PUBLIK)

Bisa dibangun dengan **Blade + Tailwind** (satu codebase Laravel), atau Filament-driven CMS ringan agar konten bisa diubah dari Super Admin.

### D1. 🟢 Halaman Utama
| Section | Isi |
|---|---|
| Hero | Headline, sub-headline, CTA "Coba Gratis 14 Hari", mockup dashboard & POS |
| Logo/Trust | Jumlah bisnis pengguna, rating, logo klien |
| Fitur Unggulan | Kasir cepat, stok real-time, **HPP otomatis**, multi-outlet, laporan |
| Solusi per Industri | Kafe & Resto, Retail, Laundry, Salon, Grosir |
| Cara Kerja | 3 langkah: Daftar → Atur produk & resep → Mulai jualan |
| Demo / Screenshot | Galeri tampilan aplikasi / video |
| Harga | Tabel paket (bulanan/tahunan toggle) diambil dari data paket Super Admin |
| Testimoni | Kutipan pengguna |
| FAQ | Pertanyaan umum |
| CTA Akhir | Daftar sekarang / hubungi sales |
| Footer | Link, kontak, sosial media, legal |

### D2. 🟢 Halaman Pendukung
- Fitur (detail), Harga, Tentang Kami, Kontak
- Syarat & Ketentuan, Kebijakan Privasi
- Halaman per industri (SEO): `/pos-untuk-kafe`, `/pos-untuk-laundry`, dst.

### D3. 🟢 Alur Pendaftaran Tenant (Self-Onboarding)
1. Isi form: nama bisnis, jenis usaha, nama, email, HP, password
2. Pilih subdomain (cek ketersediaan real-time)
3. Verifikasi email / OTP WhatsApp
4. Sistem otomatis membuat tenant + data awal sesuai **template jenis usaha**
5. Wizard onboarding: tambah outlet pertama → produk pertama → bahan & resep → siap jualan
6. Mulai masa trial

### D4. 🟡 Tambahan
- Blog / artikel (SEO)
- Form request demo / kontak sales
- Halaman changelog & status sistem
- Multi-bahasa (ID / EN)
- Live chat / tombol WhatsApp
- Tracking konversi (GA4, Meta Pixel)

---

## 5. Rancangan Database (Tabel Utama)

### 5.1 Platform (Super Admin)
```
tenants               id, name, slug, business_type_id, status, trial_ends_at, owner_id, settings(json)
domains               id, tenant_id, domain, is_primary
plans                 id, name, price_monthly, price_yearly, limits(json), features(json)
subscriptions         id, tenant_id, plan_id, status, starts_at, ends_at, cancelled_at
invoices              id, tenant_id, subscription_id, number, amount, status, due_at, paid_at
payments              id, invoice_id, gateway, reference, amount, status, payload(json)
coupons               id, code, type, value, max_uses, expires_at
business_types        id, name, preset(json)
platform_users        id, name, email, password, role   -- super admin
```

### 5.2 Tenant (semua tabel di bawah memiliki `tenant_id`)
```
outlets               id, name, address, tax_rate, service_charge, receipt_settings(json)
users                 id, name, email, pin, is_active
outlet_user           outlet_id, user_id

categories            id, parent_id, name
products               id, category_id, type(item|recipe|service|bundle), sku, barcode, name, price, tax_id, is_active
product_variants      id, product_id, name, sku, price
modifier_groups       id, name, min, max
modifiers             id, group_id, name, price, ingredient_id?, qty?
product_outlet        product_id, outlet_id, price, is_available

units                 id, name, symbol
unit_conversions      id, from_unit_id, to_unit_id, factor
ingredients           id, category_id, name, purchase_unit_id, usage_unit_id, conversion_factor,
                      last_cost, avg_cost, min_stock, shelf_life_days
recipes               id, product_id, yield_qty, yield_unit_id, extra_cost(json), total_cost, cost_per_portion
recipe_items          id, recipe_id, ingredient_id|sub_recipe_id, qty, unit_id, waste_percent

warehouses            id, outlet_id, name, type
stocks                id, warehouse_id, ingredient_id|product_id, qty, avg_cost
stock_batches         id, stock_id, batch_no, qty, cost, expired_at
stock_movements       id, warehouse_id, item_type, item_id, type(in|out|transfer|adjust|sale|waste|opname),
                      qty, cost, reference_type, reference_id, created_by, created_at   -- ledger
stock_transfers       id, from_wh_id, to_wh_id, status, approved_by
stock_opnames         id, warehouse_id, status, counted_at
stock_opname_items    id, opname_id, item_id, system_qty, counted_qty, diff, note

suppliers             id, name, phone, payment_term_days
purchase_orders       id, supplier_id, warehouse_id, status, total, expected_at
purchase_order_items  id, po_id, ingredient_id, qty, unit_id, price
goods_receipts        id, po_id, received_at
supplier_payments     id, supplier_id, amount, paid_at

customers             id, name, phone, email, group_id, points
customer_groups      id, name, discount_percent
discounts / promos    id, name, type, value, rules(json), valid_from, valid_to

shifts                id, outlet_id, user_id, opened_at, closed_at, opening_cash, expected_cash, actual_cash
cash_movements        id, shift_id, type(in|out), amount, note
orders                id, outlet_id, shift_id, customer_id, order_type, table_id, subtotal, discount, tax,
                      service, total, status, paid_at, number
order_items           id, order_id, product_id, variant_id, qty, price, cost_snapshot, note
order_item_modifiers  id, order_item_id, modifier_id, price
order_payments        id, order_id, method, amount, reference
refunds               id, order_id, reason, amount, approved_by

expense_categories    id, name
expenses              id, outlet_id, category_id, amount, date, note, is_recurring
capital_investments   id, name, amount, date, depreciation_months    -- modal usaha
activity_logs         (spatie/activitylog)
```

> **Catatan penting:** simpan **`cost_snapshot`** di `order_items` agar laporan laba historis tetap akurat walaupun harga bahan berubah di kemudian hari.

---

## 6. Logika Bisnis Kritis

### 6.1 Perhitungan HPP (Resep)
```
Biaya bahan per item = qty × harga_bahan_per_satuan_pakai × (1 + waste%)
HPP resep            = Σ biaya bahan semua item
HPP per porsi        = (HPP resep + biaya tenaga kerja + overhead + kemasan) ÷ yield
Food cost %          = HPP per porsi ÷ harga jual × 100
Margin               = harga jual − HPP per porsi
Harga saran          = HPP per porsi ÷ (1 − target_margin%)
```

### 6.2 Harga Bahan (Costing Method)
| Metode | Cara kerja | Cocok untuk |
|---|---|---|
| Harga terakhir | Pakai harga pembelian terbaru | Sederhana, UMKM |
| **Weighted Average** | `(stok_lama × avg_lama + qty_baru × harga_baru) ÷ total_qty` | ✅ Default yang disarankan |
| FIFO | Biaya mengikuti batch tertua | Bahan dengan expired / harga fluktuatif |

### 6.3 Alur Saat Transaksi
```
1. Kasir klik "Pay" → validasi stok (opsional blokir jika habis)
2. Simpan order + order_items (dengan cost_snapshot)
3. Job/Event: potong stok bahan sesuai resep (termasuk modifier)
4. Catat stock_movements (type = sale)
5. Catat order_payments & update kas shift
6. Cetak struk / kirim WhatsApp
7. Cek stok minimum → trigger notifikasi
```
Gunakan **DB transaction** + **pessimistic lock** pada baris stok untuk mencegah stok minus saat transaksi bersamaan.

### 6.4 Konversi Satuan
Beli: 1 karung gula = 50 kg → Pakai: gram
`harga per gram = harga karung ÷ (50 × 1000)`

### 6.5 Pemakaian Teoritis vs Aktual
```
Pemakaian teoritis = Σ(qty terjual × qty resep)
Pemakaian aktual   = stok awal + pembelian − stok akhir (hasil opname)
Selisih            = aktual − teoritis   → indikasi waste / pencurian / porsi tidak standar
```

---

## 7. Implementasi di Filament

### 7.1 Dua Panel Terpisah
```php
// app/Providers/Filament/AdminPanelProvider.php  → Super Admin
->id('admin')->domain('admin.namaproduk.com')->path('')
->authGuard('platform')

// app/Providers/Filament/AppPanelProvider.php    → Tenant
->id('app')->path('app')
->tenant(Tenant::class)            // Filament multi-tenancy bawaan
->tenantMiddleware([InitializeTenancyByDomain::class], isPersistent: true)
```

### 7.2 Pemetaan Modul → Filament Resource

| Modul | Filament Resource / Page |
|---|---|
| Tenant | `TenantResource` (admin) + action *Impersonate*, *Suspend* |
| Paket | `PlanResource`, `SubscriptionResource`, `InvoiceResource` |
| Produk | `ProductResource` + RelationManager (Variants, Modifiers) |
| Bahan | `IngredientResource` |
| Resep | `RecipeResource` + Repeater untuk bahan, **kalkulasi HPP live** |
| Stok | `StockResource`, `StockMovementResource`, `StockOpnameResource`, Page *Transfer* |
| Pembelian | `PurchaseOrderResource`, `SupplierResource` |
| Laporan | Custom Page + Widget (Chart, Stats) + export |
| Dashboard | Filament Widgets (StatsOverview, Chart, Table) |
| **Kasir (POS)** | **Livewire Page custom** (bukan Resource) memakai layout full-screen |

### 7.3 Tips Teknis
- Gunakan trait `BelongsToTenant` + **global scope** di semua model tenant.
- **Policy & Shield** untuk membatasi akses per role dan per outlet.
- Gunakan **Filament Actions** untuk void, approve transfer, dll, lengkap dengan konfirmasi.
- Hitung laporan berat via **queue** lalu simpan hasil ke tabel ringkasan (`daily_sales_summaries`).
- Gunakan **feature gating**: middleware / `canAccess()` yang membaca limit paket tenant.
- Gunakan **Laravel Pennant** untuk feature flag.
- Terapkan **Enum** PHP untuk status, tipe, dan metode pembayaran.
- Pisahkan logika ke **Service/Action class** (`CreateOrderAction`, `DeductStockAction`, `CalculateRecipeCostAction`).

---

## 8. Struktur Folder yang Disarankan

```
app/
├── Actions/
│   ├── Orders/CreateOrderAction.php
│   ├── Stock/DeductStockAction.php
│   └── Recipes/CalculateRecipeCostAction.php
├── Enums/
├── Filament/
│   ├── Admin/          ← Panel Super Admin
│   │   ├── Resources/
│   │   └── Widgets/
│   └── App/            ← Panel Tenant
│       ├── Resources/
│       ├── Pages/
│       └── Widgets/
├── Livewire/Pos/       ← Layar Kasir
├── Models/
│   ├── Concerns/BelongsToTenant.php
│   └── ...
├── Policies/
├── Services/
│   ├── Billing/
│   ├── Costing/
│   ├── Printing/
│   └── Tenancy/
├── Jobs/
└── Observers/
resources/views/
├── landing/            ← Landing page (Blade)
└── pos/
routes/
├── web.php             ← Landing & onboarding
└── tenant.php
```

---

## 9. Paket Langganan (Contoh)

| Fitur | Free/Trial | Basic | Pro | Business |
|---|:--:|:--:|:--:|:--:|
| Outlet | 1 | 1 | 3 | Tak terbatas |
| User | 2 | 5 | 15 | Tak terbatas |
| Produk | 50 | 500 | 5.000 | Tak terbatas |
| Kasir & Laporan dasar | ✅ | ✅ | ✅ | ✅ |
| Stok bahan baku | ❌ | ✅ | ✅ | ✅ |
| Resep & **HPP otomatis** | ❌ | ❌ | ✅ | ✅ |
| Multi-gudang & transfer | ❌ | ❌ | ✅ | ✅ |
| Pembelian & hutang | ❌ | ❌ | ✅ | ✅ |
| Loyalti & promo lanjutan | ❌ | ❌ | ✅ | ✅ |
| API & integrasi | ❌ | ❌ | ❌ | ✅ |
| Custom domain & white-label | ❌ | ❌ | ❌ | ✅ |
| Support | Email | Email | WhatsApp | Prioritas |

---

## 10. Keamanan & Kepatuhan

- Isolasi data tenant (global scope + pengujian otomatis kebocoran lintas tenant)
- Hash password (bcrypt/argon), **2FA** untuk owner & super admin
- Rate limit login & API, proteksi CSRF/XSS bawaan Laravel
- Audit log untuk aksi sensitif (void, ubah harga, hapus, opname)
- Backup harian otomatis + uji restore berkala (`spatie/laravel-backup`)
- Enkripsi data sensitif, HTTPS wajib
- Kepatuhan UU **PDP** (Perlindungan Data Pribadi) — kebijakan privasi & hak hapus data
- Soft delete pada data transaksi (jangan hapus permanen)

---

## 11. Roadmap Pengembangan

| Fase | Durasi (estimasi) | Cakupan |
|---|---|---|
| **Fase 0 – Fondasi** | 2–3 minggu | Setup Laravel+Filament, multi-tenancy, auth, role, panel admin & tenant, CI/CD |
| **Fase 1 – MVP Kasir** | 4–6 minggu | Produk, kategori, POS, pembayaran tunai/QRIS, struk, shift, laporan penjualan dasar |
| **Fase 2 – Stok & HPP** | 4–6 minggu | Bahan baku, resep, auto-deduct, stok, opname, pembelian, supplier, laporan HPP |
| **Fase 3 – SaaS** | 3–4 minggu | Paket, subscription, billing, payment gateway, onboarding, **landing page** |
| **Fase 4 – Penyempurnaan** | 3–4 minggu | Promo, loyalti, multi-outlet, transfer stok, keuangan, laporan lanjutan |
| **Fase 5 – Skala** | Berkelanjutan | KDS, online order, mode offline, mobile app, integrasi akuntansi, reseller |

> Total MVP yang bisa dijual (Fase 0–3): sekitar **3–4 bulan** untuk tim kecil (1–2 developer).

---

## 12. Rekomendasi Strategi

1. **Mulai dari satu vertikal dulu** (mis. F&B/kafe), lalu lebarkan lewat *Business Type Template* — jangan langsung melayani semua bidang sekaligus.
2. **HPP + stok bahan baku adalah nilai jual utama** — buat sangat mudah: import bahan dari Excel, wizard pembuatan resep.
3. Buat **onboarding < 10 menit** sampai transaksi pertama.
4. **Desain kasir untuk kecepatan** — minimal klik, besar tombol, jalan baik di tablet.
5. Siapkan **mode offline** sejak desain data (UUID, sync queue), walau dirilis belakangan.
6. Tulis **test otomatis** untuk isolasi tenant, perhitungan HPP, dan pemotongan stok — ini bagian paling berisiko.

---

## 13. Checklist Cepat Modul

**Super Admin**
- [ ] Dashboard platform
- [ ] Manajemen tenant
- [ ] Manajemen akun tenant
- [ ] Paket & langganan
- [ ] Billing & invoice
- [ ] Template jenis usaha
- [ ] Pengaturan global
- [ ] Support & pengumuman
- [ ] Monitoring & audit

**Tenant (Back Office)**
- [ ] Dashboard
- [ ] Outlet
- [ ] User, role & izin
- [ ] Produk
- [ ] Bahan baku
- [ ] Resep & HPP
- [ ] Stok & gudang
- [ ] Pembelian & supplier
- [ ] Penjualan
- [ ] Pelanggan & loyalti
- [ ] Diskon, promo, pajak
- [ ] Shift & kas
- [ ] Keuangan & biaya
- [ ] Laporan
- [ ] Pengaturan

**Kasir (POS)**
- [ ] Layar penjualan
- [ ] Pembayaran
- [ ] Operasional kasir

**Landing Page**
- [ ] Halaman utama
- [ ] Harga & fitur
- [ ] Pendaftaran & onboarding
- [ ] Legal & SEO

---

*Dokumen ini adalah blueprint awal; detail dapat disesuaikan setelah validasi dengan calon pengguna.*
