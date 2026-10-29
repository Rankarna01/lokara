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
            return redirect()->route('superadmin.dashboard');
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

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return redirect()->intended(route('superadmin.dashboard'));
        }

        $this->addError('email', 'Email atau kata sandi tidak sesuai dengan data kami.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.app', ['title' => 'Masuk - Lokara POS Super Admin']);
    }
}

