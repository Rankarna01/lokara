<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;

trait HasCustomers
{
    // Search, Filter & Sort
    public string $searchCustomer = '';
    public string $customerTierFilter = 'all'; // all, silver, gold, platinum
    public string $customerSortBy = 'latest'; // latest, name_asc, points_desc, spent_desc, visits_desc
    public int $customerPerPage = 20;

    // Modal Form States
    public bool $showCustomerModal = false;
    public ?int $editingCustomerId = null;
    public string $customerName = '';
    public string $customerPhone = '';
    public string $customerEmail = '';
    public string $customerGender = ''; // male, female, other
    public string $customerBirthDate = '';
    public string $customerAddress = '';
    public string $customerMembershipTier = 'silver';
    public int $customerPoints = 0;
    public string $customerNotes = '';

    // Detail Modal States
    public bool $showCustomerDetailModal = false;
    public ?int $selectedCustomerId = null;

    public function resetCustomerFilters(): void
    {
        $this->searchCustomer = '';
        $this->customerTierFilter = 'all';
        $this->customerSortBy = 'latest';
    }

    public function openNewCustomerModal(): void
    {
        $this->editingCustomerId = null;
        $this->customerName = '';
        $this->customerPhone = '';
        $this->customerEmail = '';
        $this->customerGender = '';
        $this->customerBirthDate = '';
        $this->customerAddress = '';
        $this->customerMembershipTier = 'silver';
        $this->customerPoints = 0;
        $this->customerNotes = '';
        $this->showCustomerModal = true;
    }

    public function editCustomer(int $id): void
    {
        $customer = Customer::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingCustomerId = $customer->id;
        $this->customerName = $customer->name;
        $this->customerPhone = $customer->phone ?? '';
        $this->customerEmail = $customer->email ?? '';
        $this->customerGender = $customer->gender ?? '';
        $this->customerBirthDate = $customer->birth_date ? $customer->birth_date->format('Y-m-d') : '';
        $this->customerAddress = $customer->address ?? '';
        $this->customerMembershipTier = $customer->membership_tier ?? 'silver';
        $this->customerPoints = (int) $customer->points;
        $this->customerNotes = $customer->notes ?? '';
        $this->showCustomerModal = true;
    }

    public function saveCustomer(): void
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'nullable|string|max:50',
            'customerEmail' => 'nullable|email|max:255',
            'customerGender' => 'nullable|string|max:20',
            'customerBirthDate' => 'nullable|date',
            'customerAddress' => 'nullable|string',
            'customerMembershipTier' => 'nullable|string|max:30',
            'customerPoints' => 'nullable|integer|min:0',
            'customerNotes' => 'nullable|string',
        ]);

        $tier = $this->customerMembershipTier ?: ($this->customerPoints >= 1000 ? 'platinum' : ($this->customerPoints >= 500 ? 'gold' : 'silver'));

        $data = [
            'tenant_id' => $this->tenantId,
            'name' => trim($this->customerName),
            'phone' => trim($this->customerPhone) ?: null,
            'email' => trim($this->customerEmail) ?: null,
            'gender' => $this->customerGender ?: null,
            'birth_date' => $this->customerBirthDate ?: null,
            'address' => trim($this->customerAddress) ?: null,
            'membership_tier' => $tier,
            'points' => (int) $this->customerPoints,
            'notes' => trim($this->customerNotes) ?: null,
        ];

        if ($this->editingCustomerId) {
            $customer = Customer::where('tenant_id', $this->tenantId)->findOrFail($this->editingCustomerId);
            $customer->update($data);
            $this->showToast("Data pelanggan {$customer->name} berhasil diperbarui.");
        } else {
            $customer = Customer::create($data);
            $this->showToast("Pelanggan baru {$customer->name} berhasil ditambahkan ke CRM.");
        }

        $this->showCustomerModal = false;
    }

    public function deleteCustomer(int $id): void
    {
        $customer = Customer::where('tenant_id', $this->tenantId)->findOrFail($id);
        $name = $customer->name;
        $customer->delete();
        $this->showToast("Pelanggan {$name} berhasil dihapus dari database CRM.", 'info');
    }

    public function viewCustomerDetail(int $id): void
    {
        $this->selectedCustomerId = $id;
        $this->showCustomerDetailModal = true;
    }

    public function closeCustomerDetailModal(): void
    {
        $this->showCustomerDetailModal = false;
        $this->selectedCustomerId = null;
    }

    protected function getFilteredCustomersData(): array
    {
        $query = Customer::where('tenant_id', $this->tenantId)
            ->withCount(['orders as completed_orders_count' => fn($q) => $q->where('status', 'completed')])
            ->withSum(['orders as orders_total_spent' => fn($q) => $q->where('status', 'completed')], 'total_amount');

        // Search Filter
        if (!empty($this->searchCustomer)) {
            $search = trim($this->searchCustomer);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Tier Filter
        if ($this->customerTierFilter !== 'all') {
            $query->where('membership_tier', $this->customerTierFilter);
        }

        // Sorting
        match ($this->customerSortBy) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'points_desc' => $query->orderBy('points', 'desc'),
            'spent_desc' => $query->orderByDesc('orders_total_spent'),
            'visits_desc' => $query->orderByDesc('completed_orders_count'),
            default => $query->orderBy('id', 'desc'),
        };

        $totalCustomersCount = (clone $query)->count();
        $customers = $query->take($this->customerPerPage)->get();

        // Calculate Executive CRM Metrics for Tenant
        $allTenantCustomers = Customer::where('tenant_id', $this->tenantId)
            ->withCount(['orders as completed_orders_count' => fn($q) => $q->where('status', 'completed')])
            ->withSum(['orders as orders_total_spent' => fn($q) => $q->where('status', 'completed')], 'total_amount')
            ->get();

        $totalCustomers = $allTenantCustomers->count();
        $totalPoints = (int) $allTenantCustomers->sum('points');
        $goldVipCount = $allTenantCustomers->filter(fn($c) => in_array(strtolower($c->membership_tier ?? 'silver'), ['gold', 'platinum']) || $c->points >= 500)->count();
        $totalLifetimeRevenue = (float) $allTenantCustomers->sum('orders_total_spent');
        $avgSpendingPerCustomer = $totalCustomers > 0 ? ($totalLifetimeRevenue / $totalCustomers) : 0;

        $viewingCustomer = null;
        if ($this->selectedCustomerId) {
            $viewingCustomer = Customer::where('tenant_id', $this->tenantId)
                ->with(['orders' => fn($q) => $q->with(['items', 'payments', 'outlet'])->orderBy('created_at', 'desc')])
                ->find($this->selectedCustomerId);
        }

        return [
            'customers' => $customers,
            'totalCustomersCount' => $totalCustomersCount,
            'viewingCustomer' => $viewingCustomer,
            'crmStats' => [
                'totalCustomers' => $totalCustomers,
                'totalPoints' => $totalPoints,
                'goldVipCount' => $goldVipCount,
                'totalLifetimeRevenue' => $totalLifetimeRevenue,
                'avgSpendingPerCustomer' => $avgSpendingPerCustomer,
            ],
        ];
    }
}

