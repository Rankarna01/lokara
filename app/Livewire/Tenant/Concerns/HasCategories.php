<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

trait HasCategories
{
    public bool $showCategoryModal = false;
    public bool $showManageCategoriesModal = false;
    public ?int $editingCategoryId = null;
    public string $categoryName = '';
    public string $categoryIcon = 'fa-tag';
    public int $categorySortOrder = 1;
    public bool $categoryIsActive = true;

    public function openNewCategoryModal(): void
    {
        $this->resetCategoryForm();
        $nextOrder = (Category::where('tenant_id', $this->tenantId)->max('sort_order') ?? 0) + 1;
        $this->categorySortOrder = $nextOrder;
        $this->showCategoryModal = true;
    }

    public function openEditCategoryModal(int $id): void
    {
        $category = Category::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingCategoryId = $category->id;
        $this->categoryName = $category->name;
        $this->categoryIcon = $category->icon ?: 'fa-tag';
        $this->categorySortOrder = (int) ($category->sort_order ?? 1);
        $this->categoryIsActive = (bool) $category->is_active;
        $this->showCategoryModal = true;
    }

    public function openManageCategoriesModal(): void
    {
        $this->showManageCategoriesModal = true;
    }

    public function saveCategory(): void
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

    public function deleteCategory(int $id): void
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

    private function resetCategoryForm(): void
    {
        $this->editingCategoryId = null;
        $this->categoryName = '';
        $this->categoryIcon = 'fa-tag';
        $this->categorySortOrder = 1;
        $this->categoryIsActive = true;
    }
}

