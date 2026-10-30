<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Expense;

trait HasExpenses
{
    public bool $showExpenseModal = false;
    public string $expenseCategory = 'Listrik & Air';
    public string $expenseTitle = '';
    public float $expenseAmount = 0;
    public string $expensePaymentMethod = 'Kas Toko / Tunai';
    public string $expenseNotes = '';

    public function openExpenseModal(): void
    {
        $this->expenseCategory = 'Listrik & Air';
        $this->expenseTitle = '';
        $this->expenseAmount = 250000;
        $this->expensePaymentMethod = 'Kas Toko / Tunai';
        $this->expenseNotes = '';
        $this->showExpenseModal = true;
    }

    public function saveExpense(): void
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
}

