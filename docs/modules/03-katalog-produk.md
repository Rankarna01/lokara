# Modul 03: Master Produk, Kategori, Varian & Modifier

Dokumen spesifikasi teknis untuk **Modul Katalog Produk, Kategori, Varian, dan Modifier** pada aplikasi POS Lokara. Modul ini bertanggung jawab atas fleksibilitas pengelolaan menu dan barang dagangan lintas industri (F&B, Retail, Laundry, Jasa, Grosir).

---

## 1. Identifikasi Modul

| Item | Deskripsi |
|---|---|
| **Kode Modul** | `MOD-03-KATALOG-PRODUK` |
| **Nama Modul** | Product Catalog, Categories, Variants & Modifiers |
| **Panel Filament** | `AppPanelProvider` (`{tenant}.lokara.id/app`) |
| **Akses Role** | `tenant_owner`, `tenant_manager` |
| **Prioritas Rilis** | 🟢 MVP |

---

## 2. Struktur Navigasi & Menu (Filament Tenant Panel)

```
├── 📦 Katalog & Produk
│   ├── Semua Produk                (/app/products)
│   ├── Kategori Produk             (/app/categories)
│   ├── Grup Modifier & Topping     (/app/modifier-groups)
│   ├── Pengaturan Satuan Jual      (/app/units)
│   ├── Harga Cabang (Multi-Price)  (/app/product-prices)
│   └── Import & Export Massal      (/app/products/import-export)
```

---

## 3. Kebutuhan Fungsional & Spesifikasi Fitur

### 3.1 Klasifikasi Tipe Produk
Lokara mendukung 4 tipe produk inti sesuai model bisnis tenant:
1. **Barang Jadi (`item`)**: Produk retail yang dibeli langsung dalam bentuk jadi dan dijual kembali tanpa modifikasi resep (contoh: minuman kaleng, rokok, snack, keripik).
2. **Produk Berresep (`recipe`)**: Produk F&B yang membutuhkan proses peracikan bahan baku saat dipesan (contoh: Kopi Latte, Nasi Goreng Spesial, Roti Bakar). Tipe ini terhubung langsung ke **Modul 04 (Resep & HPP)**.
3. **Jasa / Layanan (`service`)**: Layanan non-fisik yang tidak mengurangi stok saat dijual, tetapi dapat mencatat durasi atau staf pelaksana (contoh: Potong Rambut, Cuci Setrika Kiloan, Jasa Servis).
4. **Paket / Bundling (`bundle`)**: Gabungan beberapa produk yang dijual bersama dengan harga khusus (contoh: Paket Hemat Kopi + Croissant).

### 3.2 Kategori & Hierarki Produk
- Pengelompokan berjenjang (*Parent - Child category*) untuk memudahkan penemuan produk di POS.
- Icon kategori dan warna badge untuk tampilan visual tab filter pada layar kasir.
- Pengaturan urutan urut (*Display Order*) agar kategori terlaris muncul di tab paling depan.

### 3.3 SKU, Barcode & Media Produk
- **Generator SKU Otomatis**: Format yang dapat disesuaikan (misal: `KAT-00123`).
- **Barcode & QR Code Reader/Generator**:
  - Dukungan format barcode standar retail (EAN-13, UPC, Code-128).
  - Cetak label barcode langsung ke printer barcode thermal via template PDF.
- **Manajemen Media**:
  - Upload foto produk resolusi tinggi dengan kompresi WebP otomatis untuk mempercepat load di terminal POS.

### 3.4 Varian & Atribut Produk
- Varian digunakan untuk variasi produk yang mengubah harga dasar atau SKU:
  - Contoh ukuran F&B: *Regular (Rp 18.000)*, *Large (Rp 24.000)*.
  - Contoh Retail: *Ukuran Baju (S, M, L, XL)* atau *Warna (Hitam, Putih)*.
- Setiap varian memiliki: SKU spesifik, barcode spesifik, selisih harga dasar, dan keterkaitan resep/stok terpisah.

### 3.5 Grup Modifier & Add-On (Khusus F&B)
Modifier adalah opsi kustomisasi tambahan saat pelanggan memesan:
- **Modifier Group**:
  - Contoh: *Level Gula*, *Pilihan Susu*, *Ekstra Topping*, *Level Pedas*.
  - Batasan seleksi: Min Selection (misal: min 1 untuk level pedas) dan Max Selection (misal: max 3 untuk topping).
- **Modifier Item**:
  - Nama modifier, tambahan harga jual (misal: *Oat Milk +Rp 7.000*, *Boba Topping +Rp 4.000*).
  - Keterkaitan bahan baku: modifier dapat dikaitkan dengan pemotongan stok bahan di Modul 04 (misal: Topping Boba memotong 40 gram boba matang).

### 3.6 Multi-Price & Jadwal Ketersediaan
- **Harga Jual Berbeda Tiap Cabang**:
  - Harga menu di outlet Mall dapat diset lebih tinggi dibanding outlet Standalone.
  - Sakelar ketersediaan (*Availability Toggle*): Tandai menu "Habis" (*Sold Out*) khusus di outlet tertentu tanpa mempengaruhi cabang lain.
- **Harga Bertingkat (Grosir & Member)**:
  - Pembelian $\ge 10$ pcs mendapat diskon otomatis menjadi harga grosir.

### 3.7 Import & Export Excel Massal
- Kemudahan onboarding tenant melalui template Excel (`.xlsx`) standar:
  - Kolom: SKU, Barcode, Nama Produk, Kategori, Tipe, Satuan, Harga Jual, Pajak.
  - Validasi error data baris per baris sebelum commit ke database.

---

## 4. Struktur Database & Skema Tabel

```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL,
    icon VARCHAR(100) NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE units (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(50) NOT NULL,           -- misal: Kilogram, Gram, Liter, Cup, Pcs
    symbol VARCHAR(20) NOT NULL,         -- misal: kg, g, l, cup, pcs
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NULL,
    type ENUM('item', 'recipe', 'service', 'bundle') NOT NULL DEFAULT 'recipe',
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    sku VARCHAR(100) NULL,
    barcode VARCHAR(100) NULL,
    description TEXT NULL,
    unit_id BIGINT UNSIGNED NULL,
    base_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tax_exempt BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    image_url VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL,
    UNIQUE KEY product_sku_unique (tenant_id, sku),
    INDEX product_barcode_idx (tenant_id, barcode)
);

CREATE TABLE product_variants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,           -- misal: Regular, Large
    sku VARCHAR(100) NULL,
    barcode VARCHAR(100) NULL,
    additional_price DECIMAL(12,2) DEFAULT 0.00,
    final_price DECIMAL(12,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE modifier_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,           -- misal: Pilihan Topping, Sugar Level
    min_selection INT DEFAULT 0,
    max_selection INT DEFAULT 1,
    is_required BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE modifiers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    modifier_group_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,           -- misal: Boba, Less Sugar 50%
    price DECIMAL(12,2) DEFAULT 0.00,
    ingredient_id BIGINT UNSIGNED NULL,   -- jika memotong stok bahan
    ingredient_qty DECIMAL(10,4) NULL,    -- jumlah bahan yang dipotong
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (modifier_group_id) REFERENCES modifier_groups(id) ON DELETE CASCADE
);

CREATE TABLE product_modifier_group (
    product_id BIGINT UNSIGNED NOT NULL,
    modifier_group_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (product_id, modifier_group_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (modifier_group_id) REFERENCES modifier_groups(id) ON DELETE CASCADE
);

CREATE TABLE product_outlet (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    outlet_id BIGINT UNSIGNED NOT NULL,
    price_override DECIMAL(12,2) NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    UNIQUE KEY product_outlet_unique (product_id, outlet_id)
);
```

---

## 5. Implementasi Teknis Laravel & Filament

### 5.1 Filament Resources
- `ProductResource`:
  - Form Tab: Data Dasar, Varian Produk (via `HasManyRepeater`), Kaitan Modifier Groups, Pengaturan Multi-Harga Outlet.
  - Relasi `VariantsRelationManager` dan `ModifierGroupsRelationManager`.
- `CategoryResource`: Form CRUD kategori dengan preview hierarki tree.
- `ModifierGroupResource`: Builder grup opsi dengan sub-repeater untuk daftar item modifier.

### 5.2 Actions & Listeners
- `App\Actions\Catalog\GenerateProductBarcodeAction`: Membuat dan menyimpan kode barcode unik serta image asset.
- `App\Actions\Catalog\SyncProductOutletPricesAction`: Menyinkronkan harga default ke seluruh cabang tenant saat produk baru ditambahkan.
- `App\Actions\Catalog\ImportProductsFromExcelAction`: Memproses parsing spreadsheet Excel massal dengan chunking job di antrean Redis.

