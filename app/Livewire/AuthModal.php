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
    public $area_id = '';

    protected $listeners = ['openAuthModal' => 'openModal'];

    // Reglas de validación
    protected $loginRules = [
        'loginEmail' => 'required|email',
        'loginPassword' => 'required',
    ];

    protected $registerRules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'area_id' => 'required|exists:areas,id',
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
        $this->resetValidation();
        $this->resetErrorBag();
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
        $this->area_id = '';
        $this->resetValidation();
    }

    public function login()
    {
        $this->validate($this->loginRules);

        if (Auth::attempt([
            'email' => $this->loginEmail,
            'password' => $this->loginPassword
        ], $this->remember)) {

            // Simplemente redirigir a la página de inicio
            return redirect('/');
        } else {
            $this->addError('loginEmail', 'Las credenciales no coinciden con nuestros registros.');
        }
    }    public function register()
    {
        try {
            // Log de depuración para ver los datos que se están enviando
            \Illuminate\Support\Facades\Log::info('Intento de registro', [
                'name' => $this->name,
                'email' => $this->email,
                'area_id' => $this->area_id,
            ]);

            // Validar solo los campos de registro
            $this->validate($this->registerRules);

            // Verifica si ya existe un usuario con este correo
            $existingUser = \App\Models\User::where('email', $this->email)->first();
            if ($existingUser) {
                $this->addError('email', 'Este correo electrónico ya está en uso.');
                return;
            }

            // Verificar si el área existe antes de crear el usuario
            $area = \App\Models\Area::find($this->area_id);
            if (!$area) {
                throw new \Exception('El área seleccionada no es válida.');
            }

            $user = \App\Models\User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'area_id' => $area->id, // Usar el ID del área directamente del objeto
            ]);

            // Log de depuración para ver los datos creados
            \Illuminate\Support\Facades\Log::info('Usuario creado', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'area_id' => $user->area_id
            ]);

            // Asignar el rol básico de "Usuario"
            $user->assignRole('Usuario');

            // Iniciar sesión automáticamente
            Auth::login($user);

            // Almacenar el mensaje de éxito en la sesión (usar 'success' para el componente success-message)
            session()->flash('success', '¡Cuenta creada exitosamente!');

            // Cerrar modal, resetear formulario y refrescar el navegador para mostrar el cambio
            $this->showModal = false;
            $this->resetForm();

            // Redirigir usando el helper de Laravel para asegurar que se aplique correctamente
            return redirect('/');
        } catch (\Exception $e) {
            // Capturar cualquier error, registrarlo y mostrarlo como mensaje de error
            \Illuminate\Support\Facades\Log::error('Error en registro de usuario', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => [
                    'name' => $this->name,
                    'email' => $this->email,
                    'area_id' => $this->area_id
                ]
            ]);

            // Mostrar error en el formulario y en la sesión
            $this->addError('general', 'Error al crear la cuenta: ' . $e->getMessage());
            session()->flash('error', 'Error al crear la cuenta: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        // Obtener todas las áreas para el selector
        $areas = \App\Models\Area::orderBy('nombre')->get();

        return view('livewire.auth-modal', [
            'areas' => $areas,
        ]);
    }
}
