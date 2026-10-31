<?php

namespace App\Livewire\SuperAdmin\Concerns;

use App\Models\PlatformActivityLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

trait HasTenantUsersAndPasswords
{
    // Reset Password Modal State
    public bool $showResetPasswordModal = false;
    public ?int $resetUserId = null;
    public string $resetUserName = '';
    public string $resetUserEmail = '';
    public string $resetTenantName = '';
    public string $resetUserRole = 'Owner';
    public string $newPassword = '';
    public string $newPassword_confirmation = '';
    public bool $showPasswordText = false;

    // Create User Modal State
    public bool $showCreateUserModal = false;
    public string $createUserName = '';
    public string $createUserEmail = '';
    public ?int $createUserTenantId = null;
    public string $createUserRole = 'cashier';
    public string $createUserPassword = '';
    public string $createUserPassword_confirmation = '';

    // --- RESET & BUAT PASSWORD USER TENANT ---
    public function openResetPasswordModal(string $email, string $name = '', string $tenantName = '', string $role = 'Owner', ?int $userId = null)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->resetUserId = $userId;
        $this->resetUserEmail = $email;
        $this->resetUserName = $name ?: 'Pengguna Tenant';
        $this->resetTenantName = $tenantName ?: 'Tenant Lokara';
        $this->resetUserRole = $role ?: 'Owner';
        $this->newPassword = '';
        $this->newPassword_confirmation = '';
        $this->showPasswordText = false;
        $this->showResetPasswordModal = true;
    }

    public function closeResetPasswordModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showResetPasswordModal = false;
    }

    public function generateRandomPassword()
    {
        $randomPass = 'Lokara#' . rand(1000, 9999);
        $this->newPassword = $randomPass;
        $this->newPassword_confirmation = $randomPass;
        $this->showPasswordText = true;
        $this->notify("Password acak dibuat: {$randomPass}", 'info');
    }

    public function updatePassword()
    {
        try {
            $this->validate([
                'newPassword' => 'required|min:6|confirmed',
            ], [
                'newPassword.required' => 'Password baru wajib diisi.',
                'newPassword.min' => 'Password minimal 6 karakter demi keamanan akun.',
                'newPassword.confirmed' => 'Konfirmasi password tidak cocok dengan password baru.',
            ]);
        } catch (ValidationException $e) {
            $this->notify('Gagal: Periksa kembali password dan konfirmasi!', 'error');
            throw $e;
        }

        // Find user by email or ID
        $user = null;
        if ($this->resetUserId) {
            $user = User::find($this->resetUserId);
        }
        if (!$user) {
            $user = User::where('email', $this->resetUserEmail)->first();
        }

        if (!$user) {
            $tenant = Tenant::where('owner_email', $this->resetUserEmail)->first();
            $user = User::create([
                'name' => $this->resetUserName,
                'email' => $this->resetUserEmail,
                'password' => Hash::make($this->newPassword),
                'role' => strtolower($this->resetUserRole) === 'owner' ? 'owner' : 'cashier',
                'tenant_id' => $tenant?->id,
                'is_active' => true,
            ]);
        } else {
            $user->update([
                'password' => Hash::make($this->newPassword),
            ]);
        }

        PlatformActivityLog::create([
            'action' => "Password akun {$this->resetUserEmail} direset",
            'description' => "Password baru telah diperbarui dan dienkripsi oleh Super Admin",
            'icon' => 'fa-key',
            'icon_color' => 'text-amber-500 bg-amber-50',
            'badge_text' => 'Baru saja',
        ]);

        $savedEmail = $this->resetUserEmail;
        $this->showResetPasswordModal = false;
        $this->reset(['newPassword', 'newPassword_confirmation']);
        $this->resetErrorBag();

        $this->notify("Password untuk akun '{$savedEmail}' berhasil direset!");
    }

    // --- TAMBAH / BUAT USER TENANT BARU ---
    public function openCreateUserModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->createUserName = '';
        $this->createUserEmail = '';
        $this->createUserTenantId = Tenant::first()?->id;
        $this->createUserRole = 'cashier';
        $this->createUserPassword = '';
        $this->createUserPassword_confirmation = '';
        $this->showCreateUserModal = true;
    }

    public function closeCreateUserModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showCreateUserModal = false;
    }

    public function generateCreateUserPassword()
    {
        $randomPass = 'Lokara#' . rand(1000, 9999);
        $this->createUserPassword = $randomPass;
        $this->createUserPassword_confirmation = $randomPass;
        $this->notify("Password acak dibuat: {$randomPass}", 'info');
    }

    public function createUser()
    {
        try {
            $this->validate([
                'createUserName' => 'required|min:3',
                'createUserEmail' => 'required|email|unique:users,email',
                'createUserTenantId' => 'required|exists:tenants,id',
                'createUserRole' => 'required|in:owner,manager,cashier',
                'createUserPassword' => 'required|min:6|confirmed',
            ], [
                'createUserName.required' => 'Nama lengkap pengguna wajib diisi.',
                'createUserName.min' => 'Nama pengguna minimal 3 karakter.',
                'createUserEmail.required' => 'Alamat email wajib diisi.',
                'createUserEmail.email' => 'Format email tidak valid.',
                'createUserEmail.unique' => 'Email ini sudah terdaftar sebagai pengguna lain.',
                'createUserTenantId.required' => 'Pilih salah satu tenant bisnis.',
                'createUserRole.required' => 'Pilih salah satu peran (role).',
                'createUserPassword.required' => 'Password wajib diisi.',
                'createUserPassword.min' => 'Password minimal 6 karakter.',
                'createUserPassword.confirmed' => 'Konfirmasi password tidak cocok.',
            ]);
        } catch (ValidationException $e) {
            $this->notify('Gagal: Periksa kembali kolom pembuatan akun!', 'error');
            throw $e;
        }

        $tenant = Tenant::find($this->createUserTenantId);

        $user = User::create([
            'name' => $this->createUserName,
            'email' => $this->createUserEmail,
            'password' => Hash::make($this->createUserPassword),
            'role' => $this->createUserRole,
            'tenant_id' => $tenant?->id,
            'is_active' => true,
        ]);

        PlatformActivityLog::create([
            'action' => "Akun baru {$user->name} dibuat",
            'description' => "Role: " . strtoupper($user->role) . " • Tenant: " . ($tenant->name ?? '-'),
            'icon' => 'fa-user-plus',
            'icon_color' => 'text-emerald-500 bg-emerald-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->showCreateUserModal = false;
        $this->reset(['createUserName', 'createUserEmail', 'createUserPassword', 'createUserPassword_confirmation']);
        $this->resetErrorBag();

        $this->notify("Akun pengguna '{$user->name}' ({$user->email}) berhasil dibuat!");
    }
}

