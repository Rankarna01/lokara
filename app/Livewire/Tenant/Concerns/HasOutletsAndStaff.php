<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait HasOutletsAndStaff
{
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

    public function openNewOutletModal(): void
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

    public function editOutlet(int $id): void
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

    public function saveOutlet(): void
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

    public function toggleOutletStatus(int $id): void
    {
        $outlet = Outlet::where('tenant_id', $this->tenantId)->findOrFail($id);
        $outlet->update(['is_active' => !$outlet->is_active]);
        $status = $outlet->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->showToast("Cabang '{$outlet->name}' {$status}.");
    }

    public function openNewStaffModal(): void
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

    public function editStaff(int $id): void
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

    public function saveStaff(): void
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
            $data['password'] = Hash::make($this->staffPassword);
        }

        if ($this->editingStaffId) {
            $staff = User::where('tenant_id', $this->tenantId)->findOrFail($this->editingStaffId);
            $staff->update($data);
            $this->showToast("Data akun staf '{$staff->name}' berhasil diperbarui.");
        } else {
            if (empty($this->staffPassword)) {
                $data['password'] = Hash::make('password');
            }
            $staff = User::create($data);
            $this->showToast("Akun staf baru '{$staff->name}' ({$staff->role}) berhasil dibuat.");
        }

        $this->showStaffModal = false;
    }

    public function toggleStaffStatus(int $id): void
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

    public function deleteStaff(int $id): void
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
}

