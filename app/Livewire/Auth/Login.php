<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:6')]
    public string $password = '';

    public bool $remember = false;

    public function authenticate(): void
    {
        $this->validate();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Email atau password salah. Silakan coba lagi.');
            return;
        }

        $user = Auth::user();

        session()->regenerate();

        match ($user->role) {
            'admin' => $this->redirect(route('dashboard'), navigate: true),
            'guru'  => $this->redirect(route('dashboard'), navigate: true),
            'siswa' => $this->redirect(route('dashboard'), navigate: true),
            default => $this->redirect(route('dashboard'), navigate: true),
        };
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
