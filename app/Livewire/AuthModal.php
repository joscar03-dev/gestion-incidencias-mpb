<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AuthModal extends Component
{
    public $showModal = false;
    public $mode = 'login'; // 'login' o 'register'

    // Campos para login
    public $loginEmail = '';
    public $loginPassword = '';
    public $remember = false;

    // Campos para registro
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected $listeners = ['openAuthModal' => 'openModal'];

    protected $rules = [
        'loginEmail' => 'required|email',
        'loginPassword' => 'required',
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ];

    public function openModal($mode = 'login')
    {
        $this->mode = $mode;
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function switchMode()
    {
        $this->mode = $this->mode === 'login' ? 'register' : 'login';
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->loginEmail = '';
        $this->loginPassword = '';
        $this->remember = false;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetValidation();
    }

    public function login()
    {
        $this->validate([
            'loginEmail' => 'required|email',
            'loginPassword' => 'required',
        ]);

        if (Auth::attempt([
            'email' => $this->loginEmail,
            'password' => $this->loginPassword
        ], $this->remember)) {

            // Simplemente redirigir a la página de inicio
            return redirect('/');
        } else {
            $this->addError('loginEmail', 'Las credenciales no coinciden con nuestros registros.');
        }
    }   public function register()
    {
        $this->validate();

        $user = \App\Models\User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        Auth::login($user);

        // Simplemente redirigir a la página de inicio
        return redirect('/');
    }

    public function render()
    {
        return view('livewire.auth-modal');
    }
}
