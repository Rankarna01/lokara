# Modul 04: Master Bahan Baku, Resep (BOM) & Engine HPP

Dokumen spesifikasi untuk **Modul Bahan Baku, Resep (Bill of Materials), dan Engine Perhitungan HPP Otomatis**. Modul ini merupakan **keunggulan inti dan pembeda utama (differentiator)** platform POS Lokara di pasar, memungkinkan bisnis F&B dan manufaktur mikro menghitung modal secara presisi hingga ke satuan gram/ml.

---

## 1. Identifikasi Modul

| Item | Deskripsi |
|---|---|
| **Kode Modul** | `MOD-04-BAHAN-RESEP-HPP` |
| **Nama Modul** | Raw Ingredients, Recipe (BOM) & Cost of Goods Sold (COGS/HPP) Engine |
| **Panel Filament** | `AppPanelProvider` (`{tenant}.lokara.id/app`) |
| **Akses Role** | `tenant_owner`, `tenant_manager` |
| **Prioritas Rilis** | 🟢 MVP (Kritis) |

---

## 2. Struktur Navigasi & Menu (Filament Tenant Panel)

```
├── 🧪 Bahan Baku & Resep (BOM)
│   ├── Master Bahan Baku              (/app/ingredients)
│   ├── Sub-Resep (Bahan Setengah Jadi)(/app/sub-recipes)
│   ├── Resep Produk (BOM)             (/app/recipes)
│   ├── Kalkulator & Simulator HPP     (/app/cogs-simulator)
│   ├── Analisis Food Cost %           (/app/food-cost-analysis)
│   └── Riwayat Kenaikan Biaya Bahan   (/app/ingredient-cost-history)
```

---

## 3. Kebutuhan Fungsional & Spesifikasi Fitur

### 3.1 Master Bahan Baku (Raw Material Management)
- **Atribut Bahan**:
  - Kode Bahan, Nama Bahan, Kategori Bahan (Protein, Bumbu & Sayur, Dairy, Sirup, Kemasan & Packaging, Bahan Overhead).
  - Supplier Utama & Lead Time Pengiriman.
  - Stok Minimum (Reorder Point) dan Masa Simpan (Shelf Life dalam hari).
- **Dual Unit & Konversi Satuan (Beli vs Pakai)**:
  - **Satuan Beli (*Purchase Unit*)**: Satuan saat pengadaan faktur (misal: Karung 50kg, Galon 19L, Dus 24pcs).
  - **Satuan Pakai (*Usage Unit*)**: Satuan saat peracikan di resep (misal: Gram, Mililiter, Lembar).
  - **Faktor Konversi**: Nilai pengali eksak.
    $$\text{Harga per Satuan Pakai} = \frac{\text{Harga Beli Terakhir}}{\text{Faktor Konversi}}$$
    *Contoh*: 1 Karung Gula = 50.000 gram. Jika harga beli Rp 750.000, maka harga per gram = Rp 15.

### 3.2 Metode Valuasi Harga Bahan (Costing Methods)
Sistem mendukung 3 metode penetapan harga pokok bahan baku yang dapat dipilih di level tenant:
1. **Harga Beli Terakhir (*Last Purchase Cost*)**:
   Menggunakan harga dari faktur pembelian terbaru (cocok untuk bisnis pemula atau saat inflasi cepat).
2. **Biaya Rata-Rata Bergerak (*Weighted Moving Average Cost*) - Default Rekomendasi**:
   $$\text{Avg Cost Baru} = \frac{(\text{Qty Lama} \times \text{Avg Cost Lama}) + (\text{Qty Masuk} \times \text{Harga Beli Baru})}{\text{Qty Lama} + \text{Qty Masuk}}$$
3. **FIFO (*First-In, First-Out*)**:
   Menghitung biaya pemakaian bahan berdasarkan urutan batch/lot pembelian tertua.

### 3.3 Resep Bertingkat (Sub-Resep / Bahan Setengah Jadi)
Memfasilitasi dapur modern yang mempersiapkan bahan dasar terlebih dahulu (*prep kitchen*):
- Contoh: Membuat **Simple Syrup 1 Liter**:
  - Membutuhkan 1.000 gram gula pasir + 500 ml air + gas LPG.
  - Menghasilkan 1.200 ml sirup cair dengan HPP Rp 18/ml.
- Sub-resep ini dapat dipilih sebagai item bahan pada resep menu utama (misal: *Kopi Susu Gula Aren* memakai 30 ml *Simple Syrup*).

### 3.4 Formula & Kalkulasi Resep (Bill of Materials / BOM)
Setiap menu berresep memiliki formula presisi:
- **Daftar Bahan Baku & Sub-Resep**:
  - Bahan, Qty Pemakaian, Satuan Pakai, dan **Waste / Susut %**.
  - *Waste Factor*: Jika pemotongan daging ayam mengalami susut 15% (kulit/tulang terbuang), sistem memperhitungkan biaya kotor bahan:
    $$\text{Biaya Bahan Riil} = \text{Qty Pakai} \times \text{Harga Satuan} \times (1 + \text{Waste \%})$$
- **Biaya Tambahan Non-Bahan per Porsi**:
  - Biaya Kemasan / Packaging (Cup, Tutup, Sedotan, Kantong).
  - Biaya Tenaga Kerja Langsung (*Direct Labor Cost* per porsi).
  - Biaya Utilitas (*Overhead*: Listrik, Gas LPG per porsi).
- **Yield / Hasil Porsi**:
  - Resep yang dibuat sekaligus banyak (misal masak 1 panci saus bolognese menghasilkan 20 porsi).
  - Formula HPP Total:
    $$\text{HPP per Porsi} = \frac{\sum (\text{Biaya Bahan Riil}) + \text{Biaya Kemasan} + \text{Labor} + \text{Overhead}}{\text{Yield Porsi}}$$

### 3.5 Analisis Margin & Target Food Cost %
- **Indikator Rasio Keuangan Menu**:
  $$\text{Food Cost \%} = \frac{\text{HPP per Porsi}}{\text{Harga Jual Bersih}} \times 100\%$$
  $$\text{Gross Margin} = \text{Harga Jual} - \text{HPP per Porsi}$$
  $$\text{Gross Margin \%} = \frac{\text{Gross Margin}}{\text{Harga Jual}} \times 100\%$$
- **Rekomendasi Harga Jual Cerdas**:
  Tenant memasukkan target Food Cost yang diinginkan (misal 30%), sistem secara otomatis menyarankan harga jual ideal:
  $$\text{Saran Harga Jual} = \frac{\text{HPP per Porsi}}{\text{Target Food Cost Ratio}}$$
- **Peringatan Margin Kritis (*Low Margin Warning*)**:
  Jika ada kenaikan harga beli bahan di modul pembelian yang menyebabkan Food Cost melonjak di atas batas aman (misal $>40\%$), sistem memberikan indikator merah di dashboard.

### 3.6 Simulator Kenaikan Harga Bahan (*What-If Simulation*)
Fitur analitik interaktif:
- Pemilik dapat menguji skenario: *"Jika harga daging sapi naik 20% dan minyak goreng naik 15%, bagaimana dampaknya terhadap HPP seluruh menu dan laba bersih bulanan?"*
- Menghasilkan simulasi rekomendasi penyesuaian harga jual menu tanpa mengubah data master sebelum disetujui.

---

## 4. Struktur Database & Skema Tabel

```sql
CREATE TABLE ingredients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NULL,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    purchase_unit_id BIGINT UNSIGNED NOT NULL,
    usage_unit_id BIGINT UNSIGNED NOT NULL,
    conversion_factor DECIMAL(12,4) NOT NULL DEFAULT 1.0000, -- 1 purchase_unit = X usage_unit
    last_cost DECIMAL(12,4) NOT NULL DEFAULT 0.0000,         -- harga per purchase_unit
    avg_cost DECIMAL(12,4) NOT NULL DEFAULT 0.0000,          -- harga rata-rata per usage_unit
    cost_per_usage_unit DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
    min_stock DECIMAL(12,4) NOT NULL DEFAULT 0.0000,         -- dalam usage_unit
    shelf_life_days INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (purchase_unit_id) REFERENCES units(id),
    FOREIGN KEY (usage_unit_id) REFERENCES units(id),
    UNIQUE KEY ingredient_code_unique (tenant_id, code)
);

CREATE TABLE recipes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED NULL,
    yield_qty DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    packaging_cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    labor_cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    overhead_cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_ingredient_cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_recipe_cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    cost_per_portion DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
);

CREATE TABLE recipe_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipe_id BIGINT UNSIGNED NOT NULL,
    ingredient_id BIGINT UNSIGNED NULL,
    sub_recipe_id BIGINT UNSIGNED NULL,
    qty DECIMAL(12,4) NOT NULL,                   -- kuantitas dalam usage_unit
    unit_id BIGINT UNSIGNED NOT NULL,
    waste_percent DECIMAL(5,2) DEFAULT 0.00,      -- persentase susut
    unit_cost DECIMAL(12,4) NOT NULL,             -- snapshot harga bahan saat resep disimpan
    subtotal_cost DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE RESTRICT,
    FOREIGN KEY (sub_recipe_id) REFERENCES recipes(id) ON DELETE RESTRICT,
    FOREIGN KEY (unit_id) REFERENCES units(id)
);

CREATE TABLE ingredient_cost_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    ingredient_id BIGINT UNSIGNED NOT NULL,
    old_cost DECIMAL(12,4) NOT NULL,
    new_cost DECIMAL(12,4) NOT NULL,
    trigger_source VARCHAR(50) NOT NULL,          -- 'goods_receipt', 'manual_adjustment'
    reference_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
);
```

---

## 5. Implementasi Teknis & Algoritma Kalkulasi

### 5.1 Service & Action Classes
- `App\Services\Costing\HppCalculationEngine`:
  Engine pusat untuk menghitung total biaya resep secara rekursif (termasuk sub-resep), menerapkan faktor waste, dan memperbarui `cost_per_portion`.
- `App\Actions\Costing\RecalculateHppOnPriceChangeAction`:
  Job asinkron via Redis Queue yang dipanggil saat ada faktur pembelian baru (`goods_receipts`) dengan harga beli berbeda. Job ini menelusuri seluruh resep yang menggunakan bahan tersebut dan memperbarui nilai HPP secara otomatis.

### 5.2 Filament Resource & Live Calculation UI
- `RecipeResource`:
  Menggunakan Filament `Repeater` dengan event listener reaktif (`reactive()`):
  - Saat kasir/admin mengubah bahan atau kuantitas gram/ml, kolom `subtotal_cost`, `total_ingredient_cost`, dan `cost_per_portion` langsung terhitung di layar browser tanpa perlu reload halaman.

