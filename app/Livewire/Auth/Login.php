<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = true;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:4',
    ];

    protected $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'password.required' => 'Kata sandi wajib diisi.',
    ];

    public function mount()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
    }

    public function fillDemoAdmin()
    {
        $this->email = 'admin@lokarapos.id';
        $this->password = 'password';
    }

    public function fillDemoSupport()
    {
        $this->email = 'support@lokarapos.id';
        $this->password = 'password';
    }

    public function fillDemoOwner()
    {
        $this->email = 'rian@kopiruangpagi.id';
        $this->password = 'password';
    }

    public function fillDemoCashier()
    {
        $this->email = 'dina@kopiruangpagi.id';
        $this->password = 'password';
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        $this->addError('email', 'Email atau kata sandi tidak sesuai dengan data kami.');
    }

    private function redirectBasedOnRole($user)
    {
        if ($user && in_array($user->role, ['owner', 'manager'])) {
            return redirect()->route('tenant.dashboard');
        } elseif ($user && $user->role === 'cashier') {
            return redirect()->route('pos.terminal');
        }

        return redirect()->intended(route('superadmin.dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.app', ['title' => 'Masuk - Lokara POS']);
    }
}
