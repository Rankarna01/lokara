<?php

namespace App\Livewire\Pos\Concerns;

use App\Models\Expense;
use App\Models\ExpenseCategory;

trait HasPosExpenses
{
    public bool $showPosExpenseModal = false;

    public string $posExpenseCategory = '';

    public string $posExpenseNewCategory = '';

    public bool $showPosNewExpenseCategory = false;

    public string $posExpenseDate = '';

    public string $posExpenseTitle = '';

    public float $posExpenseAmount = 0;

    public string $posExpensePaymentMethod = 'Kas Toko / Tunai';

    public function openPosExpenseModal(): void
    {
        $tenant = $this->getCurrentTenant();
        if (! $tenant) {
            $this->notify('Tenant tidak ditemukan.', 'error');

            return;
        }

        $this->posExpenseCategory = ExpenseCategory::namesForTenant($tenant->id)[0] ?? 'Lainnya';
        $this->posExpenseDate = now()->toDateString();
        $this->posExpenseTitle = '';
        $this->posExpenseAmount = 0;
        $this->posExpensePaymentMethod = 'Kas Toko / Tunai';
        $this->posExpenseNewCategory = '';
        $this->showPosNewExpenseCategory = false;
        $this->showPosExpenseModal = true;
    }

    public function addPosExpenseCategory(): void
    {
        $tenant = $this->getCurrentTenant();
        $this->validate([
            'posExpenseNewCategory' => 'required|string|min:2|max:100',
        ], [
            'posExpenseNewCategory.required' => 'Nama kategori wajib diisi.',
            'posExpenseNewCategory.min' => 'Nama kategori minimal 2 karakter.',
        ]);

        $name = trim($this->posExpenseNewCategory);
        ExpenseCategory::firstOrCreate(['tenant_id' => $tenant->id, 'name' => $name]);

        $this->posExpenseCategory = $name;
        $this->posExpenseNewCategory = '';
        $this->showPosNewExpenseCategory = false;
        $this->notify("Kategori biaya '{$name}' berhasil ditambahkan.");
    }

    public function savePosExpense(): void
    {
        $tenant = $this->getCurrentTenant();
        if (! $tenant) {
            $this->notify('Tenant tidak ditemukan.', 'error');

            return;
        }

        $this->validate([
            'posExpenseTitle' => 'required|min:3',
            'posExpenseAmount' => 'required|numeric|min:1000',
            'posExpenseCategory' => 'required|string|max:100',
            'posExpenseDate' => 'required|date|before_or_equal:today',
        ], [
            'posExpenseDate.before_or_equal' => 'Tanggal pengeluaran tidak boleh di masa depan.',
        ]);

        ExpenseCategory::firstOrCreate(['tenant_id' => $tenant->id, 'name' => $this->posExpenseCategory]);

        Expense::create([
            'tenant_id' => $tenant->id,
            'outlet_id' => $this->selectedOutletId,
            'category' => $this->posExpenseCategory,
            'title' => $this->posExpenseTitle,
            'amount' => $this->posExpenseAmount,
            'expense_date' => $this->posExpenseDate,
            'payment_method' => $this->posExpensePaymentMethod,
        ]);

        $this->showPosExpenseModal = false;
        $this->notify("Pengeluaran '{$this->posExpenseTitle}' senilai Rp ".number_format($this->posExpenseAmount, 0, ',', '.').' berhasil dicatat.');
    }
}
