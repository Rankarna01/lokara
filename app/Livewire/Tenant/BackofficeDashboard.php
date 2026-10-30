<?php

namespace App\Livewire\Tenant;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\PurchaseOrder;
use App\Models\Shift;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class BackofficeDashboard extends Component
{
    use WithFileUploads;

    public string $activeTab = 'dashboard';
    public ?int $tenantId = null;
    public ?int $selectedOutletId = null;

    // Search and filter states
    public string $searchProduct = '';
    public string $selectedCategoryFilter = 'all';
    public string $searchIngredient = '';
    public string $searchTransaction = '';
    public string $transactionStatusFilter = 'all';

    // Service Management (Salon & Barbershop)
    public bool $showServiceModal = false;
    public ?int $editingServiceId = null;
    public string $serviceName = '';
    public ?int $serviceCategoryId = null;
    public string $serviceCategoryTag = 'Haircut • Pria';
    public float $servicePrice = 60000;
    public int $serviceDuration = 40; // Menit
    public string $serviceCommissionType = 'fixed'; // 'fixed' or 'percentage'
    public float $serviceCommissionValue = 20000;
    public float $serviceCostPrice = 5000;
    public string $serviceImageUrl = '';
    public $serviceImageFile = null;
    public bool $serviceIsAvailable = true;
    public string $searchService = '';
    public string $selectedServiceCategoryFilter = 'all';

    // Discount & Promo Management
    public bool $showDiscountModal = false;
    public ?int $editingDiscountId = null;
    public string $discountName = '';
    public string $discountCode = '';
    public string $discountType = 'percentage'; // 'percentage' or 'fixed'
    public float $discountValue = 15;
    public float $discountMinPurchase = 50000;
    public ?float $discountMaxAmount = 25000;
    public string $discountApplicableTo = 'all'; // 'all', 'services', 'products'
    public ?string $discountStartDate = null;
    public ?string $discountEndDate = null;
    public ?int $discountMaxUsage = null;
    public bool $discountIsActive = true;
    public string $discountNotes = '';
    public string $searchDiscount = '';
    public string $discountStatusFilter = 'all'; // 'all', 'active', 'inactive'

    // Modals
    public bool $showProductModal = false;
    public ?int $editingProductId = null;
    public string $productName = '';
    public ?int $productCategoryId = null;
    public string $productCategoryTag = 'Kopi • Hot / Ice';
    public float $productBasePrice = 0;
    public float $productCostPrice = 0;
    public string $productImageUrl = '';
    public bool $productIsActive = true;
    public bool $productIsAvailable = true;

    // Category Modal & Management
    public bool $showCategoryModal = false;
    public bool $showManageCategoriesModal = false;
    public ?int $editingCategoryId = null;
    public string $categoryName = '';
    public string $categoryIcon = 'fa-tag';
    public int $categorySortOrder = 1;
    public bool $categoryIsActive = true;

    // Ingredient Modal
    public bool $showIngredientModal = false;
    public string $ingredientName = '';
    public string $ingredientCode = '';
    public string $ingredientCategory = 'Biji Kopi';
    public string $ingredientPurchaseUnit = 'Kg';
    public string $ingredientUsageUnit = 'Gram';
    public float $ingredientConversionFactor = 1000;
    public float $ingredientCostPerUsage = 180;
    public float $ingredientCurrentStock = 5000;
    public float $ingredientMinStock = 2000;
    public string $ingredientLocation = 'Bar Depan';

    // Recipe Builder Modal
    public bool $showRecipeModal = false;
    public ?int $recipeProductId = null;
    public ?Product $selectedRecipeProduct = null;
    public ?int $selectedRecipeIngredientId = null;
    public float $recipeQuantity = 10;

    // Stock Opname Modal
    public bool $showOpnameModal = false;
    public ?int $opnameIngredientId = null;
    public float $opnamePhysicalQty = 0;
    public string $opnameReason = 'Pemeriksaan rutin mingguan';

    // Expense Modal
    public bool $showExpenseModal = false;
    public string $expenseCategory = 'Listrik & Air';
    public string $expenseTitle = '';
    public float $expenseAmount = 0;
    public string $expensePaymentMethod = 'Kas Toko / Tunai';
    public string $expenseNotes = '';

    // Purchase Order Modal & Filters
    public bool $showPoModal = false;
    public ?int $poSupplierId = null;
    public float $poTotalAmount = 0;
    public string $poNotes = '';
    public string $poExpectedDate = '';
    public string $searchPo = '';
    public string $poStatusFilter = 'all'; // 'all', 'ordered', 'received', 'cancelled'
    public string $poSupplierFilter = 'all';
    public string $poDateFilter = 'all'; // 'all', 'today', '7_days', '30_days', 'this_month', 'custom'
    public string $poStartDate = '';
    public string $poEndDate = '';
    public bool $showPoDetailModal = false;
    public ?int $viewingPoId = null;

    // Supplier Management Modal & Form
    public bool $showSupplierModal = false;
    public ?int $editingSupplierId = null;
    public string $supplierName = '';
    public string $supplierContactPerson = '';
    public string $supplierPhone = '';
    public string $supplierEmail = '';
    public string $supplierAddress = '';
    public int $supplierTermsDays = 0;
    public bool $supplierIsActive = true;
    public string $searchSupplier = '';
    public string $supplierStatusFilter = 'all'; // 'all', 'active', 'inactive'

    // Void Order Modal
    public bool $showVoidModal = false;
    public ?int $voidOrderId = null;
    public string $voidPin = '';
    public string $voidReason = 'Kesalahan input kasir';

    // Outlet / Branch Management
    public bool $showOutletModal = false;
    public ?int $editingOutletId = null;
    public string $outletName = '';
    public string $outletAddress = '';
    public string $outletPhone = '';
    public float $outletTaxRate = 11.0;
    public float $outletServiceRate = 0.0;
    public string $outletReceiptFooter = 'Terima kasih atas kunjungan Anda!';
    public bool $outletIsActive = true;

    // Staff / Cashier Account Management
    public bool $showStaffModal = false;
    public ?int $editingStaffId = null;
    public string $staffName = '';
    public string $staffEmail = '';
    public string $staffPassword = '';
    public string $staffRole = 'cashier'; // 'cashier', 'manager', 'staff'
    public ?int $staffOutletId = null;
    public string $staffPinCode = '123456';
    public string $staffPhone = '';
    public bool $staffIsActive = true;

    // Master Shift Management
    public bool $showShiftTemplateModal = false;
    public ?int $editingShiftTemplateId = null;
    public string $shiftTemplateName = '';
    public string $shiftTemplateStartTime = '07:00';
    public string $shiftTemplateEndTime = '15:00';
    public float $shiftTemplateDefaultCash = 200000;
    public bool $shiftTemplateIsActive = true;

    // Shift Policy Rules
    public float $policyDefaultCash = 200000;
    public bool $policyEnforceLock = true;
    public bool $policyRequireReconciliation = true;

    // Toast
    public string $toastMessage = '';
    public string $toastType = 'success';

    public function mount()
    {
        // Check authenticated user
        $user = Auth::user();
        if ($user) {
            // Cashier / Staff are restricted from Back Office
            if (in_array($user->role, ['cashier', 'staff'])) {
                return redirect()->route('pos.terminal');
            }
            if ($user->tenant_id) {
                $this->tenantId = $user->tenant_id;
                $this->selectedOutletId = $user->outlet_id;
            }
        } else {
            $tenant = Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();
            $this->tenantId = $tenant?->id;
        }

        if (!$this->selectedOutletId && $this->tenantId) {
            $outlet = Outlet::where('tenant_id', $this->tenantId)->first();
            $this->selectedOutletId = $outlet?->id;
        }

        $tenantModel = Tenant::find($this->tenantId);
        if ($tenantModel && isset($tenantModel->settings['shift_policies'])) {
            $policies = $tenantModel->settings['shift_policies'];
            $this->policyDefaultCash = (float) ($policies['default_cash'] ?? 200000);
            $this->policyEnforceLock = (bool) ($policies['enforce_lock'] ?? true);
            $this->policyRequireReconciliation = (bool) ($policies['require_reconciliation'] ?? true);
        }
    }

    public function switchTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function switchOutlet(int $outletId)
    {
        $outlet = Outlet::where('tenant_id', $this->tenantId)->find($outletId);
        if ($outlet) {
            $this->selectedOutletId = $outlet->id;
            $this->showToast("Outlet aktif dialihkan ke '{$outlet->name}'.", 'info');
        }
    }

    public function switchTenant(int $tenantId)
    {
        // Security check: Only Super Admin can switch between tenants in Back Office
        if (Auth::check() && !Auth::user()->isSuperAdmin()) {
            $this->showToast('Akses ditolak: Anda hanya dapat mengelola bisnis Anda sendiri.', 'error');
            return;
        }

        $tenant = Tenant::with('businessType')->findOrFail($tenantId);
        $this->tenantId = $tenant->id;
        $firstOutlet = Outlet::where('tenant_id', $tenant->id)->first();
        $this->selectedOutletId = $firstOutlet?->id;
        $this->selectedCategoryFilter = 'all';
        $this->selectedServiceCategoryFilter = 'all';

        if ($tenant->isSalon() && in_array($this->activeTab, ['recipes', 'stock'])) {
            $this->activeTab = 'services';
        }

        $this->showToast("Mode Super Admin: Menampilkan tenant '{$tenant->name}' ({$tenant->businessType?->name}).", 'info');
    }

    // --- PRODUCT MANAGEMENT ---
    public function openNewProductModal()
    {
        $this->resetProductForm();
        $this->showProductModal = true;
    }

    public function editProduct(int $id)
    {
        $product = Product::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingProductId = $product->id;
        $this->productName = $product->name;
        $this->productCategoryId = $product->category_id;
        $this->productCategoryTag = $product->category_tag ?? 'Kopi • Ice';
        $this->productBasePrice = (float) $product->base_price;
        $this->productCostPrice = (float) $product->cost_price;
        $this->productImageUrl = $product->image_url ?? '';
        $this->productIsActive = (bool) $product->is_active;
        $this->productIsAvailable = (bool) $product->is_available;
        $this->showProductModal = true;
    }

    public function saveProduct()
    {
        $this->validate([
            'productName' => 'required|min:2',
            'productBasePrice' => 'required|numeric|min:0',
            'productCostPrice' => 'nullable|numeric|min:0',
        ]);

        if ($this->editingProductId) {
            $product = Product::where('tenant_id', $this->tenantId)->findOrFail($this->editingProductId);
            $product->update([
                'name' => $this->productName,
                'category_id' => $this->productCategoryId,
                'category_tag' => $this->productCategoryTag,
                'base_price' => $this->productBasePrice,
                'cost_price' => $this->productCostPrice,
                'image_url' => $this->productImageUrl ?: null,
                'is_active' => $this->productIsActive,
                'is_available' => $this->productIsAvailable,
            ]);
            $this->showToast("Produk '{$product->name}' berhasil diperbarui.");
        } else {
            Product::create([
                'tenant_id' => $this->tenantId,
                'category_id' => $this->productCategoryId,
                'category_tag' => $this->productCategoryTag,
                'type' => 'item',
                'name' => $this->productName,
                'slug' => Str::slug($this->productName) . '-' . rand(100, 999),
                'sku' => 'PRD-' . strtoupper(Str::random(5)),
                'barcode' => '899' . rand(100000000, 999999999),
                'base_price' => $this->productBasePrice,
                'cost_price' => $this->productCostPrice,
                'image_url' => $this->productImageUrl ?: 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=300&h=300&q=80',
                'is_active' => $this->productIsActive,
                'is_available' => $this->productIsAvailable,
            ]);
            $this->showToast("Produk '{$this->productName}' berhasil ditambahkan ke katalog!");
        }

        $this->showProductModal = false;
        $this->resetProductForm();
    }

    public function toggleProductAvailability(int $id)
    {
        $product = Product::where('tenant_id', $this->tenantId)->findOrFail($id);
        $product->is_available = !$product->is_available;
        $product->save();
        $status = $product->is_available ? 'Tersedia' : 'Habis / Stok Kosong';
        $this->showToast("Status {$product->name} diubah menjadi '{$status}'");
    }

    private function resetProductForm()
    {
        $this->editingProductId = null;
        $this->productName = '';
        $this->productCategoryId = Category::where('tenant_id', $this->tenantId)->first()?->id;
        $this->productCategoryTag = 'Kopi • Ice';
        $this->productBasePrice = 20000;
        $this->productCostPrice = 7000;
        $this->productImageUrl = '';
        $this->productIsActive = true;
        $this->productIsAvailable = true;
    }

    // --- CATEGORY MANAGEMENT ---
    public function openNewCategoryModal()
    {
        $this->resetCategoryForm();
        $nextOrder = (Category::where('tenant_id', $this->tenantId)->max('sort_order') ?? 0) + 1;
        $this->categorySortOrder = $nextOrder;
        $this->showCategoryModal = true;
    }

    public function openEditCategoryModal(int $id)
    {
        $category = Category::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingCategoryId = $category->id;
        $this->categoryName = $category->name;
        $this->categoryIcon = $category->icon ?: 'fa-tag';
        $this->categorySortOrder = (int) ($category->sort_order ?? 1);
        $this->categoryIsActive = (bool) $category->is_active;
        $this->showCategoryModal = true;
    }

    public function openManageCategoriesModal()
    {
        $this->showManageCategoriesModal = true;
    }

    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required|min:2|max:50',
            'categorySortOrder' => 'required|integer|min:0',
        ]);

        $slugBase = Str::slug($this->categoryName);
        if (empty($slugBase)) {
            $slugBase = 'kategori-' . rand(100, 999);
        }

        if ($this->editingCategoryId) {
            $category = Category::where('tenant_id', $this->tenantId)->findOrFail($this->editingCategoryId);

            $slug = $slugBase;
            if ($slug !== $category->slug) {
                $exists = Category::where('tenant_id', $this->tenantId)
                    ->where('slug', $slug)
                    ->where('id', '!=', $category->id)
                    ->exists();
                if ($exists) {
                    $slug .= '-' . rand(10, 99);
                }
            }

            $category->update([
                'name' => $this->categoryName,
                'slug' => $slug,
                'icon' => $this->categoryIcon ?: 'fa-tag',
                'sort_order' => $this->categorySortOrder,
                'is_active' => $this->categoryIsActive,
            ]);

            $this->showToast("Kategori '{$category->name}' berhasil diperbarui.");
        } else {
            $slug = $slugBase;
            $exists = Category::where('tenant_id', $this->tenantId)->where('slug', $slug)->exists();
            if ($exists) {
                $slug .= '-' . rand(10, 99);
            }

            $category = Category::create([
                'tenant_id' => $this->tenantId,
                'name' => $this->categoryName,
                'slug' => $slug,
                'icon' => $this->categoryIcon ?: 'fa-tag',
                'sort_order' => $this->categorySortOrder,
                'is_active' => $this->categoryIsActive,
            ]);

            $this->showToast("Kategori '{$this->categoryName}' berhasil ditambahkan!");

            // Auto-select this new category in whichever modal is currently open
            if ($this->showServiceModal) {
                $this->serviceCategoryId = $category->id;
            } elseif ($this->showProductModal) {
                $this->productCategoryId = $category->id;
            } else {
                $this->selectedCategoryFilter = (string) $category->id;
                $this->selectedServiceCategoryFilter = (string) $category->id;
            }
        }

        $this->showCategoryModal = false;
        $this->resetCategoryForm();
    }

    public function deleteCategory(int $id)
    {
        $category = Category::where('tenant_id', $this->tenantId)->findOrFail($id);

        Product::where('category_id', $category->id)->update(['category_id' => null]);

        $name = $category->name;
        $category->delete();

        if ($this->selectedCategoryFilter == (string) $id) {
            $this->selectedCategoryFilter = 'all';
        }

        if ($this->selectedServiceCategoryFilter == (string) $id) {
            $this->selectedServiceCategoryFilter = 'all';
        }

        if ($this->productCategoryId == $id) {
            $this->productCategoryId = Category::where('tenant_id', $this->tenantId)->first()?->id;
        }

        if ($this->serviceCategoryId == $id) {
            $this->serviceCategoryId = Category::where('tenant_id', $this->tenantId)->first()?->id;
        }

        $this->showToast("Kategori '{$name}' berhasil dihapus.");
    }

    private function resetCategoryForm()
    {
        $this->editingCategoryId = null;
        $this->categoryName = '';
        $this->categoryIcon = 'fa-tag';
        $this->categorySortOrder = 1;
        $this->categoryIsActive = true;
    }

    // --- SERVICE (LAYANAN) MANAGEMENT (Khusus Salon & Barbershop) ---
    public function openNewServiceModal()
    {
        $this->resetServiceForm();
        $this->showServiceModal = true;
    }

    public function editService(int $id)
    {
        $service = Product::where('tenant_id', $this->tenantId)->where('type', 'service')->findOrFail($id);
        $this->editingServiceId = $service->id;
        $this->serviceName = $service->name;
        $this->serviceCategoryId = $service->category_id;
        $this->serviceCategoryTag = $service->category_tag ?? 'Layanan Salon';
        $this->servicePrice = (float) $service->base_price;
        $this->serviceDuration = (int) ($service->duration_minutes ?? 40);
        $this->serviceCommissionType = $service->commission_type ?? 'fixed';
        $this->serviceCommissionValue = (float) ($service->commission_value ?? 0);
        $this->serviceCostPrice = (float) ($service->cost_price ?? 0);
        $this->serviceImageUrl = $service->image_url ?? '';
        $this->serviceImageFile = null;
        $this->serviceIsAvailable = (bool) $service->is_available;
        $this->showServiceModal = true;
    }

    public function removeServiceImage()
    {
        $this->serviceImageFile = null;
        $this->serviceImageUrl = '';
    }

    public function saveService()
    {
        $this->validate([
            'serviceName' => 'required|min:2',
            'servicePrice' => 'required|numeric|min:0',
            'serviceDuration' => 'required|integer|min:5',
            'serviceCommissionValue' => 'required|numeric|min:0',
            'serviceCostPrice' => 'nullable|numeric|min:0',
            'serviceImageFile' => 'nullable|image|max:3072',
        ]);

        if ($this->serviceCommissionType === 'percentage' && $this->serviceCommissionValue > 100) {
            $this->addError('serviceCommissionValue', 'Komisi persentase tidak boleh melebihi 100%.');
            return;
        }

        if ($this->serviceImageFile) {
            $path = $this->serviceImageFile->store('services', 'public');
            $this->serviceImageUrl = '/storage/' . $path;
        }

        if ($this->editingServiceId) {
            $service = Product::where('tenant_id', $this->tenantId)->where('type', 'service')->findOrFail($this->editingServiceId);
            $service->update([
                'name' => $this->serviceName,
                'category_id' => $this->serviceCategoryId,
                'category_tag' => $this->serviceCategoryTag,
                'base_price' => $this->servicePrice,
                'cost_price' => $this->serviceCostPrice,
                'duration_minutes' => $this->serviceDuration,
                'commission_type' => $this->serviceCommissionType,
                'commission_value' => $this->serviceCommissionValue,
                'image_url' => $this->serviceImageUrl ?: null,
                'is_available' => $this->serviceIsAvailable,
            ]);
            $this->showToast("Layanan '{$service->name}' berhasil diperbarui.");
        } else {
            Product::create([
                'tenant_id' => $this->tenantId,
                'type' => 'service',
                'category_id' => $this->serviceCategoryId,
                'category_tag' => $this->serviceCategoryTag,
                'name' => $this->serviceName,
                'slug' => Str::slug($this->serviceName) . '-' . rand(100, 999),
                'sku' => 'SRV-' . strtoupper(Str::random(5)),
                'base_price' => $this->servicePrice,
                'cost_price' => $this->serviceCostPrice,
                'duration_minutes' => $this->serviceDuration,
                'commission_type' => $this->serviceCommissionType,
                'commission_value' => $this->serviceCommissionValue,
                'image_url' => $this->serviceImageUrl ?: 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=300&h=300&q=80',
                'is_active' => true,
                'is_available' => $this->serviceIsAvailable,
            ]);
            $this->showToast("Layanan '{$this->serviceName}' berhasil ditambahkan ke katalog!");
        }

        $this->showServiceModal = false;
        $this->resetServiceForm();
    }

    public function toggleServiceAvailability(int $id)
    {
        $service = Product::where('tenant_id', $this->tenantId)->where('type', 'service')->findOrFail($id);
        $service->is_available = !$service->is_available;
        $service->save();
        $status = $service->is_available ? 'Tersedia' : 'Tidak Aktif / Libur';
        $this->showToast("Status layanan '{$service->name}' diubah menjadi '{$status}'");
    }

    public function deleteService(int $id)
    {
        $service = Product::where('tenant_id', $this->tenantId)->where('type', 'service')->findOrFail($id);
        $name = $service->name;
        $service->delete();
        $this->showToast("Layanan '{$name}' berhasil dihapus.");
    }

    private function resetServiceForm()
    {
        $this->editingServiceId = null;
        $this->serviceName = '';
        $this->serviceCategoryId = Category::where('tenant_id', $this->tenantId)->first()?->id;
        $this->serviceCategoryTag = 'Haircut & Styling';
        $this->servicePrice = 60000;
        $this->serviceDuration = 45;
        $this->serviceCommissionType = 'fixed';
        $this->serviceCommissionValue = 20000;
        $this->serviceCostPrice = 5000;
        $this->serviceImageFile = null;
        $this->serviceImageUrl = '';
        $this->serviceIsAvailable = true;
    }

    // --- DISCOUNT & PROMO MANAGEMENT ---
    public function openNewDiscountModal()
    {
        $this->resetDiscountForm();
        $this->showDiscountModal = true;
    }

    public function editDiscount(int $id)
    {
        $discount = Discount::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingDiscountId = $discount->id;
        $this->discountName = $discount->name;
        $this->discountCode = $discount->code ?? '';
        $this->discountType = $discount->type;
        $this->discountValue = (float) $discount->value;
        $this->discountMinPurchase = (float) $discount->min_purchase;
        $this->discountMaxAmount = $discount->max_discount ? (float) $discount->max_discount : null;
        $this->discountApplicableTo = $discount->applicable_to ?? 'all';
        $this->discountStartDate = $discount->start_date?->format('Y-m-d');
        $this->discountEndDate = $discount->end_date?->format('Y-m-d');
        $this->discountMaxUsage = $discount->max_usage;
        $this->discountIsActive = (bool) $discount->is_active;
        $this->discountNotes = $discount->notes ?? '';
        $this->showDiscountModal = true;
    }

    public function saveDiscount()
    {
        $this->validate([
            'discountName' => 'required|min:2',
            'discountType' => 'required|in:percentage,fixed',
            'discountValue' => 'required|numeric|min:1',
            'discountMinPurchase' => 'nullable|numeric|min:0',
        ]);

        if ($this->discountType === 'percentage' && $this->discountValue > 100) {
            $this->addError('discountValue', 'Diskon persentase maksimal 100%.');
            return;
        }

        $code = !empty($this->discountCode) ? strtoupper(trim($this->discountCode)) : null;

        $payload = [
            'tenant_id' => $this->tenantId,
            'name' => $this->discountName,
            'code' => $code,
            'type' => $this->discountType,
            'value' => $this->discountValue,
            'min_purchase' => $this->discountMinPurchase ?: 0,
            'max_discount' => ($this->discountType === 'percentage' && $this->discountMaxAmount) ? $this->discountMaxAmount : null,
            'applicable_to' => $this->discountApplicableTo,
            'start_date' => $this->discountStartDate ?: null,
            'end_date' => $this->discountEndDate ?: null,
            'max_usage' => $this->discountMaxUsage ?: null,
            'is_active' => $this->discountIsActive,
            'notes' => $this->discountNotes ?: null,
        ];

        if ($this->editingDiscountId) {
            $discount = Discount::where('tenant_id', $this->tenantId)->findOrFail($this->editingDiscountId);
            $discount->update($payload);
            $this->showToast("Diskon '{$discount->name}' berhasil diperbarui.");
        } else {
            Discount::create($payload);
            $this->showToast("Diskon '{$this->discountName}' berhasil dibuat!");
        }

        $this->showDiscountModal = false;
        $this->resetDiscountForm();
    }

    public function toggleDiscountStatus(int $id)
    {
        $discount = Discount::where('tenant_id', $this->tenantId)->findOrFail($id);
        $discount->is_active = !$discount->is_active;
        $discount->save();
        $status = $discount->is_active ? 'Aktif' : 'Nonaktif';
        $this->showToast("Status promo {$discount->name} diubah menjadi '{$status}'");
    }

    public function deleteDiscount(int $id)
    {
        $discount = Discount::where('tenant_id', $this->tenantId)->findOrFail($id);
        $name = $discount->name;
        $discount->delete();
        $this->showToast("Diskon '{$name}' berhasil dihapus.");
    }

    private function resetDiscountForm()
    {
        $this->editingDiscountId = null;
        $this->discountName = '';
        $this->discountCode = '';
        $this->discountType = 'percentage';
        $this->discountValue = 15;
        $this->discountMinPurchase = 50000;
        $this->discountMaxAmount = 25000;
        $this->discountApplicableTo = 'all';
        $this->discountStartDate = now()->toDateString();
        $this->discountEndDate = now()->addMonth()->toDateString();
        $this->discountMaxUsage = 100;
        $this->discountIsActive = true;
        $this->discountNotes = '';
    }

    // --- INGREDIENT & RECIPE MANAGEMENT ---
    public function openNewIngredientModal()
    {
        $this->ingredientName = '';
        $this->ingredientCode = 'RAW-' . str_pad((string) rand(10, 99), 3, '0', STR_PAD_LEFT);
        $this->ingredientCategory = 'Biji Kopi';
        $this->ingredientPurchaseUnit = 'Kg';
        $this->ingredientUsageUnit = 'Gram';
        $this->ingredientConversionFactor = 1000;
        $this->ingredientCostPerUsage = 180;
        $this->ingredientCurrentStock = 5000;
        $this->ingredientMinStock = 2000;
        $this->ingredientLocation = 'Bar Depan';
        $this->showIngredientModal = true;
    }

    public function saveIngredient()
    {
        $this->validate([
            'ingredientName' => 'required|min:2',
            'ingredientCostPerUsage' => 'required|numeric|min:0',
            'ingredientCurrentStock' => 'required|numeric|min:0',
        ]);

        Ingredient::create([
            'tenant_id' => $this->tenantId,
            'name' => $this->ingredientName,
            'code' => $this->ingredientCode,
            'category' => $this->ingredientCategory,
            'purchase_unit' => $this->ingredientPurchaseUnit,
            'usage_unit' => $this->ingredientUsageUnit,
            'conversion_factor' => $this->ingredientConversionFactor,
            'cost_per_usage_unit' => $this->ingredientCostPerUsage,
            'current_stock' => $this->ingredientCurrentStock,
            'minimum_stock' => $this->ingredientMinStock,
            'storage_location' => $this->ingredientLocation,
        ]);

        $this->showIngredientModal = false;
        $this->showToast("Bahan baku '{$this->ingredientName}' berhasil didaftarkan!");
    }

    public function openRecipeModal(int $productId)
    {
        $this->recipeProductId = $productId;
        $this->selectedRecipeProduct = Product::with(['recipes.ingredient'])->where('tenant_id', $this->tenantId)->findOrFail($productId);
        $firstIng = Ingredient::where('tenant_id', $this->tenantId)->first();
        $this->selectedRecipeIngredientId = $firstIng?->id;
        $this->recipeQuantity = 15;
        $this->showRecipeModal = true;
    }

    public function addRecipeItem()
    {
        if (!$this->recipeProductId || !$this->selectedRecipeIngredientId || $this->recipeQuantity <= 0) {
            return;
        }

        ProductRecipe::updateOrCreate(
            ['product_id' => $this->recipeProductId, 'ingredient_id' => $this->selectedRecipeIngredientId],
            ['quantity' => $this->recipeQuantity]
        );

        // Recalculate HPP from recipes!
        $this->recalculateProductHpp($this->recipeProductId);

        $this->selectedRecipeProduct = Product::with(['recipes.ingredient'])->where('tenant_id', $this->tenantId)->find($this->recipeProductId);
        $this->showToast("Komposisi bahan berhasil ditambahkan & HPP diperbarui!");
    }

    public function removeRecipeItem(int $recipeId)
    {
        $item = ProductRecipe::findOrFail($recipeId);
        $productId = $item->product_id;
        $item->delete();

        $this->recalculateProductHpp($productId);
        $this->selectedRecipeProduct = Product::with(['recipes.ingredient'])->where('tenant_id', $this->tenantId)->find($productId);
        $this->showToast("Bahan resep dihapus.");
    }

    private function recalculateProductHpp(int $productId)
    {
        $product = Product::with('recipes.ingredient')->find($productId);
        if (!$product) return;

        $totalHpp = 0;
        foreach ($product->recipes as $r) {
            if ($r->ingredient) {
                $totalHpp += ($r->quantity * $r->ingredient->cost_per_usage_unit);
            }
        }

        if ($totalHpp > 0) {
            $product->update(['cost_price' => round($totalHpp)]);
        }
    }

    // --- STOCK & OPNAME ---
    public function openOpnameModal(int $ingredientId)
    {
        $this->opnameIngredientId = $ingredientId;
        $ing = Ingredient::findOrFail($ingredientId);
        $this->opnamePhysicalQty = (float) $ing->current_stock;
        $this->opnameReason = 'Pemeriksaan rutin mingguan';
        $this->showOpnameModal = true;
    }

    public function executeOpname()
    {
        $ing = Ingredient::where('tenant_id', $this->tenantId)->findOrFail($this->opnameIngredientId);
        $diff = $this->opnamePhysicalQty - $ing->current_stock;

        $ing->current_stock = $this->opnamePhysicalQty;
        $ing->save();

        // Record stock movement
        StockMovement::create([
            'tenant_id' => $this->tenantId,
            'outlet_id' => $this->selectedOutletId,
            'ingredient_id' => $ing->id,
            'type' => 'opname',
            'qty' => $diff,
            'balance_after' => $this->opnamePhysicalQty,
            'reference_note' => "Stok Opname: Selisih " . ($diff >= 0 ? "+{$diff}" : "{$diff}") . " {$ing->usage_unit}. Alasan: {$this->opnameReason}",
        ]);

        $this->showOpnameModal = false;
        $this->showToast("Stok Opname '{$ing->name}' dicatat. Stok fisik disinkronkan ke {$this->opnamePhysicalQty} {$ing->usage_unit}.");
    }

    // --- PURCHASES & SUPPLIER ---
    public function openNewSupplierModal()
    {
        $this->resetSupplierForm();
        $this->showSupplierModal = true;
    }

    public function editSupplier(int $id)
    {
        $supplier = Supplier::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingSupplierId = $supplier->id;
        $this->supplierName = $supplier->name;
        $this->supplierContactPerson = $supplier->contact_person ?? '';
        $this->supplierPhone = $supplier->phone ?? '';
        $this->supplierEmail = $supplier->email ?? '';
        $this->supplierAddress = $supplier->address ?? '';
        $this->supplierTermsDays = (int) ($supplier->terms_days ?? 0);
        $this->supplierIsActive = (bool) $supplier->is_active;
        $this->showSupplierModal = true;
    }

    public function saveSupplier()
    {
        $this->validate([
            'supplierName' => 'required|min:2|max:100',
            'supplierContactPerson' => 'nullable|max:50',
            'supplierPhone' => 'nullable|max:30',
            'supplierEmail' => 'nullable|email|max:100',
            'supplierAddress' => 'nullable|max:255',
            'supplierTermsDays' => 'required|integer|min:0|max:180',
        ]);

        if ($this->editingSupplierId) {
            $supplier = Supplier::where('tenant_id', $this->tenantId)->findOrFail($this->editingSupplierId);
            $supplier->update([
                'name' => $this->supplierName,
                'contact_person' => $this->supplierContactPerson ?: null,
                'phone' => $this->supplierPhone ?: null,
                'email' => $this->supplierEmail ?: null,
                'address' => $this->supplierAddress ?: null,
                'terms_days' => $this->supplierTermsDays,
                'is_active' => $this->supplierIsActive,
            ]);
            $this->showToast("Data rekanan supplier '{$supplier->name}' berhasil diperbarui.");
        } else {
            $supplier = Supplier::create([
                'tenant_id' => $this->tenantId,
                'name' => $this->supplierName,
                'contact_person' => $this->supplierContactPerson ?: null,
                'phone' => $this->supplierPhone ?: null,
                'email' => $this->supplierEmail ?: null,
                'address' => $this->supplierAddress ?: null,
                'terms_days' => $this->supplierTermsDays,
                'is_active' => $this->supplierIsActive,
            ]);
            $this->showToast("Supplier '{$supplier->name}' berhasil ditambahkan!");

            if ($this->showPoModal) {
                $this->poSupplierId = $supplier->id;
            }
        }

        $this->showSupplierModal = false;
        $this->resetSupplierForm();
    }

    public function deleteSupplier(int $id)
    {
        $supplier = Supplier::where('tenant_id', $this->tenantId)->findOrFail($id);
        $poCount = $supplier->purchaseOrders()->count();

        if ($poCount > 0) {
            $supplier->update(['is_active' => false]);
            $this->showToast("Supplier '{$supplier->name}' memiliki {$poCount} riwayat PO, status dinonaktifkan.", 'warning');
            return;
        }

        $name = $supplier->name;
        $supplier->delete();
        $this->showToast("Supplier '{$name}' berhasil dihapus.");
    }

    public function toggleSupplierStatus(int $id)
    {
        $supplier = Supplier::where('tenant_id', $this->tenantId)->findOrFail($id);
        $supplier->is_active = !$supplier->is_active;
        $supplier->save();
        $status = $supplier->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->showToast("Status supplier '{$supplier->name}' {$status}.");
    }

    public function resetSupplierForm()
    {
        $this->editingSupplierId = null;
        $this->supplierName = '';
        $this->supplierContactPerson = '';
        $this->supplierPhone = '';
        $this->supplierEmail = '';
        $this->supplierAddress = '';
        $this->supplierTermsDays = 0;
        $this->supplierIsActive = true;
    }

    public function openPoModal(?int $supplierId = null)
    {
        $suppliers = Supplier::where('tenant_id', $this->tenantId)->where('is_active', true)->get();
        if ($suppliers->isEmpty()) {
            $this->showToast('Belum ada supplier terdaftar. Silakan tambahkan supplier terlebih dahulu.', 'warning');
            $this->openNewSupplierModal();
            return;
        }

        $this->poSupplierId = $supplierId ?? ($this->poSupplierId && $suppliers->contains('id', $this->poSupplierId) ? $this->poSupplierId : $suppliers->first()?->id);
        $this->poTotalAmount = 1500000;
        $this->poNotes = 'Pengadaan bahan baku & kebutuhan operasional';
        $this->poExpectedDate = now()->addDays(3)->toDateString();
        $this->showPoModal = true;
    }

    public function savePurchaseOrder()
    {
        $this->validate([
            'poSupplierId' => 'required|exists:suppliers,id',
            'poTotalAmount' => 'required|numeric|min:1000',
            'poExpectedDate' => 'nullable|date',
            'poNotes' => 'nullable|max:255',
        ]);

        $poCount = PurchaseOrder::where('tenant_id', $this->tenantId)->count() + 1;
        $poNumber = 'PO-' . date('Ym') . '-' . str_pad((string) $poCount, 3, '0', STR_PAD_LEFT);

        PurchaseOrder::create([
            'tenant_id' => $this->tenantId,
            'outlet_id' => $this->selectedOutletId,
            'supplier_id' => $this->poSupplierId,
            'po_number' => $poNumber,
            'order_date' => now()->toDateString(),
            'expected_date' => $this->poExpectedDate ?: now()->addDays(3)->toDateString(),
            'status' => 'ordered',
            'total_amount' => $this->poTotalAmount,
            'notes' => $this->poNotes ?: 'Pengadaan stok operasional',
        ]);

        $this->showPoModal = false;
        $this->showToast("Purchase Order {$poNumber} senilai Rp " . number_format($this->poTotalAmount, 0, ',', '.') . " berhasil diterbitkan!");
    }

    public function markPoReceived(int $id)
    {
        $po = PurchaseOrder::where('tenant_id', $this->tenantId)->findOrFail($id);
        $po->update(['status' => 'received']);
        $this->showToast("PO {$po->po_number} berhasil ditandai Diterima (GRN). Stok operasional diperbarui!");
    }

    public function cancelPo(int $id)
    {
        $po = PurchaseOrder::where('tenant_id', $this->tenantId)->findOrFail($id);
        if ($po->status === 'received') {
            $this->showToast("PO {$po->po_number} sudah berstatus Diterima, tidak dapat dibatalkan.", 'error');
            return;
        }
        $po->update(['status' => 'cancelled']);
        $this->showToast("PO {$po->po_number} berhasil dibatalkan.", 'info');
    }

    public function viewPoDetail(int $id)
    {
        $this->viewingPoId = $id;
        $this->showPoDetailModal = true;
    }

    public function resetPoFilters()
    {
        $this->searchPo = '';
        $this->poStatusFilter = 'all';
        $this->poSupplierFilter = 'all';
        $this->poDateFilter = 'all';
        $this->poStartDate = '';
        $this->poEndDate = '';
    }

    public function seedDefaultSuppliers()
    {
        $tenant = Tenant::find($this->tenantId);
        $isSalon = $tenant?->isSalon();

        if ($isSalon) {
            $defaults = [
                [
                    'name' => "PT L'Oreal Professional Indonesia",
                    'contact_person' => 'Maya Indriani',
                    'phone' => '081233445566',
                    'email' => 'order@loreal-pro.co.id',
                    'address' => 'Jl. TB Simatupang No. 88, Jakarta Selatan',
                    'terms_days' => 14,
                ],
                [
                    'name' => 'CV Barbershop Supplies Nusantara',
                    'contact_person' => 'Budi Santoso',
                    'phone' => '081988776655',
                    'email' => 'sales@barbersupplies.id',
                    'address' => 'Komp. Ruko Darmo Park II Blok B, Surabaya',
                    'terms_days' => 7,
                ],
                [
                    'name' => 'Toko Handuk & Aksesoris Salon Berkah',
                    'contact_person' => 'Bu Hj. Nur',
                    'phone' => '081344556677',
                    'email' => 'toko.berkah@gmail.com',
                    'address' => 'Pasar Turi Baru Blok C-12, Surabaya',
                    'terms_days' => 0,
                ],
            ];
        } else {
            $defaults = [
                [
                    'name' => 'PT Kopi Nusantara Makmur',
                    'contact_person' => 'Hendro Setiawan',
                    'phone' => '081122334455',
                    'email' => 'sales@kopinusantara.co.id',
                    'address' => 'Kawasan Industri Sentul Kav. 12, Bogor',
                    'terms_days' => 14,
                ],
                [
                    'name' => 'CV Dairy Fresh Sejahtera',
                    'contact_person' => 'Siti Nurhaliza',
                    'phone' => '081299887766',
                    'email' => 'order@dairyfresh.com',
                    'address' => 'Jl. Pasar Minggu Raya No. 88, Jakarta Selatan',
                    'terms_days' => 7,
                ],
                [
                    'name' => 'Toko Kemasan Jaya Mandiri',
                    'contact_person' => 'Aheng Salim',
                    'phone' => '081765432100',
                    'email' => 'sales@kemasanjaya.id',
                    'address' => 'Glodok Plaza Blok B-14, Jakarta Barat',
                    'terms_days' => 0,
                ],
            ];
        }

        foreach ($defaults as $d) {
            Supplier::firstOrCreate(
                ['tenant_id' => $this->tenantId, 'name' => $d['name']],
                array_merge($d, ['tenant_id' => $this->tenantId, 'is_active' => true])
            );
        }

        $this->showToast('Rekomendasi pemasok standar berhasil ditambahkan!');
    }

    // --- EXPENSES (OPEX) ---
    public function openExpenseModal()
    {
        $this->expenseCategory = 'Listrik & Air';
        $this->expenseTitle = '';
        $this->expenseAmount = 250000;
        $this->expensePaymentMethod = 'Kas Toko / Tunai';
        $this->expenseNotes = '';
        $this->showExpenseModal = true;
    }

    public function saveExpense()
    {
        $this->validate([
            'expenseTitle' => 'required|min:3',
            'expenseAmount' => 'required|numeric|min:1000',
        ]);

        Expense::create([
            'tenant_id' => $this->tenantId,
            'outlet_id' => $this->selectedOutletId,
            'category' => $this->expenseCategory,
            'title' => $this->expenseTitle,
            'amount' => $this->expenseAmount,
            'expense_date' => now()->toDateString(),
            'payment_method' => $this->expensePaymentMethod,
            'notes' => $this->expenseNotes,
        ]);

        $this->showExpenseModal = false;
        $this->showToast("Pengeluaran OPEX '{$this->expenseTitle}' senilai Rp " . number_format($this->expenseAmount, 0, ',', '.') . " berhasil dicatat.");
    }

    // --- VOID ORDER ---
    public function openVoidModal(int $orderId)
    {
        $this->voidOrderId = $orderId;
        $this->voidPin = '';
        $this->voidReason = 'Kesalahan pesanan';
        $this->showVoidModal = true;
    }

    public function executeVoidOrder()
    {
        if ($this->voidPin !== '888888' && $this->voidPin !== '123456') {
            $this->showToast('PIN Otorisasi Manajer salah!', 'error');
            return;
        }

        $order = Order::where('tenant_id', $this->tenantId)->findOrFail($this->voidOrderId);
        $order->update([
            'status' => 'voided',
            'notes' => ($order->notes ? $order->notes . ' | ' : '') . "VOID oleh Manajer. Alasan: {$this->voidReason}",
        ]);

        $this->showVoidModal = false;
        $this->showToast("Pesanan {$order->order_number} berhasil di-VOID.", 'info');
    }

    // --- OUTLET / CABANG MANAGEMENT ---
    public function openNewOutletModal()
    {
        $this->editingOutletId = null;
        $this->outletName = '';
        $this->outletAddress = '';
        $this->outletPhone = '';
        $this->outletTaxRate = 11.0;
        $this->outletServiceRate = 0.0;
        $this->outletReceiptFooter = 'Terima kasih atas kunjungan Anda!';
        $this->outletIsActive = true;
        $this->showOutletModal = true;
    }

    public function editOutlet(int $id)
    {
        $outlet = Outlet::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingOutletId = $outlet->id;
        $this->outletName = $outlet->name;
        $this->outletAddress = $outlet->address ?? '';
        $this->outletPhone = $outlet->phone ?? '';
        $this->outletTaxRate = (float) $outlet->tax_rate;
        $this->outletServiceRate = (float) $outlet->service_rate;
        $receipt = $outlet->receipt_settings ?? [];
        $this->outletReceiptFooter = $receipt['footer'] ?? 'Terima kasih atas kunjungan Anda!';
        $this->outletIsActive = (bool) $outlet->is_active;
        $this->showOutletModal = true;
    }

    public function saveOutlet()
    {
        $this->validate([
            'outletName' => 'required|min:3|max:100',
            'outletAddress' => 'required|min:5',
            'outletTaxRate' => 'required|numeric|min:0|max:100',
            'outletServiceRate' => 'required|numeric|min:0|max:100',
        ], [
            'outletName.required' => 'Nama cabang wajib diisi.',
            'outletAddress.required' => 'Alamat cabang wajib diisi.',
            'outletTaxRate.required' => 'Tarif pajak wajib diisi.',
        ]);

        $data = [
            'tenant_id' => $this->tenantId,
            'name' => $this->outletName,
            'address' => $this->outletAddress,
            'phone' => $this->outletPhone,
            'tax_rate' => $this->outletTaxRate,
            'service_rate' => $this->outletServiceRate,
            'receipt_settings' => ['footer' => $this->outletReceiptFooter],
            'is_active' => $this->outletIsActive,
        ];

        if ($this->editingOutletId) {
            $outlet = Outlet::where('tenant_id', $this->tenantId)->findOrFail($this->editingOutletId);
            $outlet->update($data);
            $this->showToast("Cabang '{$outlet->name}' berhasil diperbarui.");
        } else {
            $data['slug'] = Str::slug($this->outletName) . '-' . substr(md5(microtime()), 0, 4);
            $outlet = Outlet::create($data);
            $this->showToast("Cabang baru '{$outlet->name}' berhasil ditambahkan.");
        }

        $this->showOutletModal = false;
    }

    public function toggleOutletStatus(int $id)
    {
        $outlet = Outlet::where('tenant_id', $this->tenantId)->findOrFail($id);
        $outlet->update(['is_active' => !$outlet->is_active]);
        $status = $outlet->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->showToast("Cabang '{$outlet->name}' {$status}.");
    }

    // --- STAFF & CASHIER ACCOUNT MANAGEMENT ---
    public function openNewStaffModal()
    {
        $this->editingStaffId = null;
        $this->staffName = '';
        $this->staffEmail = '';
        $this->staffPassword = 'password';
        $this->staffRole = 'cashier';
        $this->staffOutletId = $this->selectedOutletId;
        $this->staffPinCode = (string) rand(100000, 999999);
        $this->staffPhone = '';
        $this->staffIsActive = true;
        $this->showStaffModal = true;
    }

    public function editStaff(int $id)
    {
        $staff = User::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingStaffId = $staff->id;
        $this->staffName = $staff->name;
        $this->staffEmail = $staff->email;
        $this->staffPassword = ''; // Tetap kosong jika tidak ingin ganti sandi
        $this->staffRole = $staff->role;
        $this->staffOutletId = $staff->outlet_id;
        $this->staffPinCode = $staff->pin_code ?? '123456';
        $this->staffPhone = $staff->phone ?? '';
        $this->staffIsActive = (bool) $staff->is_active;
        $this->showStaffModal = true;
    }

    public function saveStaff()
    {
        $emailRule = 'required|email|unique:users,email';
        if ($this->editingStaffId) {
            $emailRule .= ',' . $this->editingStaffId;
        }

        $this->validate([
            'staffName' => 'required|min:3|max:100',
            'staffEmail' => $emailRule,
            'staffRole' => 'required|in:cashier,manager,staff,owner',
            'staffOutletId' => 'required|exists:outlets,id',
            'staffPinCode' => 'required|digits:6',
        ], [
            'staffName.required' => 'Nama staf wajib diisi.',
            'staffEmail.required' => 'Alamat email wajib diisi.',
            'staffEmail.unique' => 'Email ini sudah terdaftar oleh pengguna lain.',
            'staffPinCode.required' => 'PIN kasir wajib diisi.',
            'staffPinCode.digits' => 'PIN kasir harus tepat 6 digit angka.',
        ]);

        $data = [
            'tenant_id' => $this->tenantId,
            'name' => $this->staffName,
            'email' => $this->staffEmail,
            'role' => $this->staffRole,
            'outlet_id' => $this->staffOutletId,
            'pin_code' => $this->staffPinCode,
            'phone' => $this->staffPhone,
            'is_active' => $this->staffIsActive,
        ];

        if (!empty($this->staffPassword)) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($this->staffPassword);
        }

        if ($this->editingStaffId) {
            $staff = User::where('tenant_id', $this->tenantId)->findOrFail($this->editingStaffId);
            $staff->update($data);
            $this->showToast("Data akun staf '{$staff->name}' berhasil diperbarui.");
        } else {
            if (empty($this->staffPassword)) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make('password');
            }
            $staff = User::create($data);
            $this->showToast("Akun staf baru '{$staff->name}' ({$staff->role}) berhasil dibuat.");
        }

        $this->showStaffModal = false;
    }

    public function toggleStaffStatus(int $id)
    {
        $staff = User::where('tenant_id', $this->tenantId)->findOrFail($id);
        if ($staff->role === 'owner') {
            $this->showToast('Akun Pemilik (Owner) tidak dapat dinonaktifkan.', 'error');
            return;
        }
        $staff->update(['is_active' => !$staff->is_active]);
        $status = $staff->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->showToast("Akun staf '{$staff->name}' {$status}.");
    }

    public function deleteStaff(int $id)
    {
        $staff = User::where('tenant_id', $this->tenantId)->findOrFail($id);
        if ($staff->role === 'owner') {
            $this->showToast('Akun Pemilik (Owner) tidak dapat dihapus.', 'error');
            return;
        }
        $name = $staff->name;
        $staff->delete();
        $this->showToast("Akun staf '{$name}' berhasil dihapus.");
    }

    // --- MASTER SHIFT & POLICY MANAGEMENT ---
    public function openNewShiftTemplateModal()
    {
        $this->editingShiftTemplateId = null;
        $this->shiftTemplateName = '';
        $this->shiftTemplateStartTime = '07:00';
        $this->shiftTemplateEndTime = '15:00';
        $this->shiftTemplateDefaultCash = 200000;
        $this->shiftTemplateIsActive = true;
        $this->showShiftTemplateModal = true;
    }

    public function editShiftTemplate(int $id)
    {
        $templates = $this->getShiftTemplates();
        $template = collect($templates)->firstWhere('id', $id);
        if ($template) {
            $this->editingShiftTemplateId = $template['id'];
            $this->shiftTemplateName = $template['name'];
            $this->shiftTemplateStartTime = $template['start_time'];
            $this->shiftTemplateEndTime = $template['end_time'];
            $this->shiftTemplateDefaultCash = (float) $template['default_cash'];
            $this->shiftTemplateIsActive = (bool) $template['is_active'];
            $this->showShiftTemplateModal = true;
        }
    }

    public function saveShiftTemplate()
    {
        $this->validate([
            'shiftTemplateName' => 'required|string|max:100',
            'shiftTemplateStartTime' => 'required',
            'shiftTemplateEndTime' => 'required',
            'shiftTemplateDefaultCash' => 'required|numeric|min:0',
        ]);

        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $settings = $tenant->settings ?? [];
        $templates = $this->getShiftTemplates();

        if ($this->editingShiftTemplateId) {
            foreach ($templates as &$item) {
                if ($item['id'] === $this->editingShiftTemplateId) {
                    $item['name'] = $this->shiftTemplateName;
                    $item['start_time'] = $this->shiftTemplateStartTime;
                    $item['end_time'] = $this->shiftTemplateEndTime;
                    $item['default_cash'] = $this->shiftTemplateDefaultCash;
                    $item['is_active'] = $this->shiftTemplateIsActive;
                }
            }
        } else {
            $newId = count($templates) > 0 ? max(array_column($templates, 'id')) + 1 : 1;
            $templates[] = [
                'id' => $newId,
                'name' => $this->shiftTemplateName,
                'start_time' => $this->shiftTemplateStartTime,
                'end_time' => $this->shiftTemplateEndTime,
                'default_cash' => $this->shiftTemplateDefaultCash,
                'is_active' => $this->shiftTemplateIsActive,
            ];
        }

        $settings['shift_templates'] = $templates;
        $tenant->update(['settings' => $settings]);
        $this->showShiftTemplateModal = false;
        $this->showToast('Master template shift berhasil disimpan.');
    }

    public function toggleShiftTemplateStatus(int $id)
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $settings = $tenant->settings ?? [];
        $templates = $this->getShiftTemplates();

        foreach ($templates as &$item) {
            if ($item['id'] === $id) {
                $item['is_active'] = !($item['is_active'] ?? true);
            }
        }

        $settings['shift_templates'] = $templates;
        $tenant->update(['settings' => $settings]);
        $this->showToast('Status template shift berhasil diubah.');
    }

    public function deleteShiftTemplate(int $id)
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $settings = $tenant->settings ?? [];
        $templates = array_values(array_filter($this->getShiftTemplates(), fn($t) => $t['id'] !== $id));

        $settings['shift_templates'] = $templates;
        $tenant->update(['settings' => $settings]);
        $this->showToast('Template shift berhasil dihapus.', 'info');
    }

    public function saveShiftPolicies()
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $settings = $tenant->settings ?? [];
        $settings['shift_policies'] = [
            'default_cash' => $this->policyDefaultCash,
            'enforce_lock' => $this->policyEnforceLock,
            'require_reconciliation' => $this->policyRequireReconciliation,
        ];
        $tenant->update(['settings' => $settings]);
        $this->showToast('Kebijakan & aturan master shift berhasil disimpan.');
    }

    public function getShiftTemplates(): array
    {
        $tenant = Tenant::find($this->tenantId);
        if ($tenant && !empty($tenant->settings['shift_templates'])) {
            return $tenant->settings['shift_templates'];
        }

        return [
            ['id' => 1, 'name' => 'Shift 1 (Pagi)', 'start_time' => '07:00', 'end_time' => '15:00', 'default_cash' => 200000, 'is_active' => true],
            ['id' => 2, 'name' => 'Shift 2 (Sore - Malam)', 'start_time' => '15:00', 'end_time' => '23:00', 'default_cash' => 200000, 'is_active' => true],
            ['id' => 3, 'name' => 'Shift Full Day', 'start_time' => '08:00', 'end_time' => '22:00', 'default_cash' => 300000, 'is_active' => false],
        ];
    }

    // --- TOAST NOTIFICATION ---
    private function showToast(string $message, string $type = 'success')
    {
        $this->toastMessage = $message;
        $this->toastType = $type;
    }

    public function closeToast()
    {
        $this->toastMessage = '';
    }

    public function render()
    {
        $tenant = Tenant::with('businessType')->find($this->tenantId);
        $isSuperAdmin = Auth::check() && Auth::user()->isSuperAdmin();
        $allTenants = $isSuperAdmin ? Tenant::with('businessType')->get() : collect($tenant ? [$tenant] : []);
        $outlets = Outlet::withCount('users')->where('tenant_id', $this->tenantId)->get();
        $selectedOutlet = Outlet::find($this->selectedOutletId) ?? $outlets->first();

        // 1. Dashboard Stats
        $todayOrders = Order::where('tenant_id', $this->tenantId)
            ->where('status', 'completed')
            ->whereDate('created_at', now()->today())
            ->get();

        $todaySales = $todayOrders->sum('total_amount');
        $todayTxCount = $todayOrders->count();
        $avgTicket = $todayTxCount > 0 ? ($todaySales / $todayTxCount) : 0;

        // Estimated COGS (HPP) and Gross Margin
        $allCompletedOrders = Order::with('items.product')->where('tenant_id', $this->tenantId)->where('status', 'completed')->get();
        $monthGrossSales = $allCompletedOrders->sum('total_amount');
        $estimatedCogs = 0;
        foreach ($allCompletedOrders as $ord) {
            foreach ($ord->items as $it) {
                $cost = $it->product?->cost_price ?? ($it->unit_price * 0.35);
                $estimatedCogs += ($cost * $it->qty);
            }
        }
        $grossProfit = max(0, $monthGrossSales - $estimatedCogs);
        $grossMarginPct = $monthGrossSales > 0 ? round(($grossProfit / $monthGrossSales) * 100, 1) : 68.5;

        // Total OPEX this month
        $totalOpex = Expense::where('tenant_id', $this->tenantId)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');
        $netProfit = $grossProfit - $totalOpex;

        // Low stock items count
        $lowStockIngredients = Ingredient::where('tenant_id', $this->tenantId)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->get();

        // 2. Products & Services Data
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('type', '!=', 'service');
        }])->where('tenant_id', $this->tenantId)->orderBy('sort_order')->get();

        $serviceCategories = Category::withCount(['products' => function ($q) {
            $q->where('type', 'service');
        }])->where('tenant_id', $this->tenantId)->orderBy('sort_order')->get();

        $totalProductsCount = Product::where('tenant_id', $this->tenantId)->where('type', '!=', 'service')->count();
        $productsQuery = Product::with(['category', 'recipes.ingredient'])
            ->where('tenant_id', $this->tenantId)
            ->where('type', '!=', 'service');

        if ($this->selectedCategoryFilter !== 'all') {
            $productsQuery->where('category_id', $this->selectedCategoryFilter);
        }

        if (!empty($this->searchProduct)) {
            $productsQuery->where('name', 'like', '%' . $this->searchProduct . '%');
        }
        $products = $productsQuery->orderBy('id', 'desc')->get();

        // 2b. Services Data (Khusus Salon & Barbershop)
        $servicesQuery = Product::with('category')
            ->where('tenant_id', $this->tenantId)
            ->where('type', 'service');

        if ($this->selectedServiceCategoryFilter !== 'all') {
            $servicesQuery->where('category_id', $this->selectedServiceCategoryFilter);
        }

        if (!empty($this->searchService)) {
            $servicesQuery->where('name', 'like', '%' . $this->searchService . '%');
        }
        $services = $servicesQuery->orderBy('id', 'desc')->get();

        // 2c. Discounts & Promos Data
        $discountsQuery = Discount::where('tenant_id', $this->tenantId);

        if ($this->discountStatusFilter === 'active') {
            $discountsQuery->where('is_active', true);
        } elseif ($this->discountStatusFilter === 'inactive') {
            $discountsQuery->where('is_active', false);
        }

        if (!empty($this->searchDiscount)) {
            $discountsQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchDiscount . '%')
                    ->orWhere('code', 'like', '%' . $this->searchDiscount . '%');
            });
        }
        $discounts = $discountsQuery->orderBy('id', 'desc')->get();

        // 3. Ingredients Data
        $ingredientsQuery = Ingredient::with(['recipes.product'])->where('tenant_id', $this->tenantId);
        if (!empty($this->searchIngredient)) {
            $ingredientsQuery->where('name', 'like', '%' . $this->searchIngredient . '%');
        }
        $ingredients = $ingredientsQuery->get();

        // 4. Transactions Data
        $transactionsQuery = Order::with(['items', 'payments'])->where('tenant_id', $this->tenantId);
        if ($this->transactionStatusFilter !== 'all') {
            $transactionsQuery->where('status', $this->transactionStatusFilter);
        }
        if (!empty($this->searchTransaction)) {
            $transactionsQuery->where('order_number', 'like', '%' . $this->searchTransaction . '%');
        }
        $transactions = $transactionsQuery->orderBy('id', 'desc')->take(20)->get();

        // 5. Suppliers & Purchase Orders Data (Filtered)
        $poQuery = PurchaseOrder::with(['supplier', 'outlet', 'items'])
            ->where('tenant_id', $this->tenantId);

        if (!empty($this->searchPo)) {
            $poQuery->where(function ($q) {
                $q->where('po_number', 'like', '%' . $this->searchPo . '%')
                  ->orWhere('notes', 'like', '%' . $this->searchPo . '%')
                  ->orWhereHas('supplier', function ($sq) {
                      $sq->where('name', 'like', '%' . $this->searchPo . '%')
                        ->orWhere('contact_person', 'like', '%' . $this->searchPo . '%');
                  });
            });
        }

        if ($this->poStatusFilter !== 'all') {
            $poQuery->where('status', $this->poStatusFilter);
        }

        if ($this->poSupplierFilter !== 'all') {
            $poQuery->where('supplier_id', $this->poSupplierFilter);
        }

        if ($this->poDateFilter === 'today') {
            $poQuery->whereDate('order_date', now()->today());
        } elseif ($this->poDateFilter === '7_days') {
            $poQuery->where('order_date', '>=', now()->subDays(7)->toDateString());
        } elseif ($this->poDateFilter === '30_days') {
            $poQuery->where('order_date', '>=', now()->subDays(30)->toDateString());
        } elseif ($this->poDateFilter === 'this_month') {
            $poQuery->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year);
        } elseif ($this->poDateFilter === 'custom' && !empty($this->poStartDate) && !empty($this->poEndDate)) {
            $poQuery->whereBetween('order_date', [$this->poStartDate, $this->poEndDate]);
        }

        $purchaseOrders = $poQuery->orderBy('id', 'desc')->get();

        // PO Statistics
        $allTenantPos = PurchaseOrder::where('tenant_id', $this->tenantId)->get();
        $totalPoCount = $allTenantPos->count();
        $totalPoAmount = $allTenantPos->sum('total_amount');
        $orderedPoCount = $allTenantPos->where('status', 'ordered')->count();
        $receivedPoCount = $allTenantPos->where('status', 'received')->count();
        $cancelledPoCount = $allTenantPos->where('status', 'cancelled')->count();

        // Suppliers query with search & filter
        $supplierQuery = Supplier::withCount('purchaseOrders')->where('tenant_id', $this->tenantId);
        if (!empty($this->searchSupplier)) {
            $supplierQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchSupplier . '%')
                  ->orWhere('contact_person', 'like', '%' . $this->searchSupplier . '%')
                  ->orWhere('phone', 'like', '%' . $this->searchSupplier . '%')
                  ->orWhere('email', 'like', '%' . $this->searchSupplier . '%')
                  ->orWhere('address', 'like', '%' . $this->searchSupplier . '%');
            });
        }
        if ($this->supplierStatusFilter === 'active') {
            $supplierQuery->where('is_active', true);
        } elseif ($this->supplierStatusFilter === 'inactive') {
            $supplierQuery->where('is_active', false);
        }
        $suppliers = $supplierQuery->orderBy('name')->get();
        $allSuppliersList = Supplier::where('tenant_id', $this->tenantId)->orderBy('name')->get();
        $viewingPo = $this->viewingPoId ? PurchaseOrder::with(['supplier', 'outlet', 'items'])->where('tenant_id', $this->tenantId)->find($this->viewingPoId) : null;

        // 6. Expenses Data
        $expenses = Expense::where('tenant_id', $this->tenantId)->orderBy('expense_date', 'desc')->get();

        // 7. Customers / CRM Data
        $customers = Customer::where('tenant_id', $this->tenantId)->orderBy('points', 'desc')->get();

        // 8. Staff / Cashiers Data
        $cashiers = User::with('outlet')->where('tenant_id', $this->tenantId)->orderBy('role')->get();

        // 9. Stock Movements (Kartu Stok)
        $stockMovements = StockMovement::with(['ingredient', 'outlet'])->where('tenant_id', $this->tenantId)->orderBy('id', 'desc')->take(15)->get();

        return view('livewire.tenant.backoffice-dashboard', [
            'tenant' => $tenant,
            'allTenants' => $allTenants,
            'outlets' => $outlets,
            'selectedOutlet' => $selectedOutlet,
            'todaySales' => $todaySales,
            'todayTxCount' => $todayTxCount,
            'avgTicket' => $avgTicket,
            'monthGrossSales' => $monthGrossSales,
            'estimatedCogs' => $estimatedCogs,
            'grossProfit' => $grossProfit,
            'grossMarginPct' => $grossMarginPct,
            'totalOpex' => $totalOpex,
            'netProfit' => $netProfit,
            'lowStockIngredients' => $lowStockIngredients,
            'categories' => $categories,
            'serviceCategories' => $serviceCategories,
            'totalProductsCount' => $totalProductsCount,
            'products' => $products,
            'services' => $services,
            'discounts' => $discounts,
            'ingredients' => $ingredients,
            'transactions' => $transactions,
            'suppliers' => $suppliers,
            'allSuppliersList' => $allSuppliersList,
            'purchaseOrders' => $purchaseOrders,
            'totalPoCount' => $totalPoCount,
            'totalPoAmount' => $totalPoAmount,
            'orderedPoCount' => $orderedPoCount,
            'receivedPoCount' => $receivedPoCount,
            'cancelledPoCount' => $cancelledPoCount,
            'viewingPo' => $viewingPo,
            'expenses' => $expenses,
            'customers' => $customers,
            'cashiers' => $cashiers,
            'stockMovements' => $stockMovements,
            'shiftTemplates' => $this->getShiftTemplates(),
            'shiftHistory' => \App\Models\Shift::with(['user', 'outlet'])->where('tenant_id', $this->tenantId)->latest()->take(10)->get(),
        ])->layout('layouts.app', ['title' => 'Admin Back Office - ' . ($tenant->name ?? 'Lokara POS')]);
    }
}

