<div>
    <!-- Modal de Autenticación -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ $mode === 'login' ? 'Iniciar Sesión' : 'Crear Cuenta' }}
                        </h3>
                        <button
                            wire:click="closeModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Contenido del Modal -->
                <div class="p-6">
                    @if($mode === 'login')
                        <!-- Formulario de Login -->
                        <form wire:submit.prevent="login">
                            <!-- Email -->
                            <div class="mb-4">
                                <label for="login_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Correo Electrónico
                                </label>
                                <input
                                    type="email"
                                    id="login_email"
                                    wire:model="loginEmail"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('loginEmail') border-red-500 @enderror"
                                    placeholder="tu@email.com"
                                    required
                                >
                                @error('loginEmail')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-6">
                                <label for="login_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Contraseña
                                </label>
                                <input
                                    type="password"
                                    id="login_password"
                                    wire:model="loginPassword"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('loginPassword') border-red-500 @enderror"
                                    placeholder="••••••••"
                                    required
                                >
                                @error('loginPassword')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="flex items-center justify-between mb-6">
                                <label for="remember" class="flex items-center">
                                    <input
                                        id="remember"
                                        type="checkbox"
                                        wire:model="remember"
                                        class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600"
                                    >
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Recordarme</span>
                                </label>
                                <a href="#" class="text-sm text-green-600 hover:text-green-500">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>

                            <!-- Login Button -->
                            <button
                                type="submit"
                                class="w-full px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 flex items-center justify-center gap-2"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1"/>
                                </svg>
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" wire:loading>
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove>Iniciar Sesión</span>
                                <span wire:loading>Iniciando...</span>
                            </button>
                        </form>
                    @else
                        <!-- Formulario de Registro -->
                        <form wire:submit.prevent="register">
                            <!-- Nombre -->
                            <div class="mb-4">
                                <label for="register_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nombre Completo
                                </label>
                                <input
                                    type="text"
                                    id="register_name"
                                    wire:model="name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                    placeholder="Tu nombre completo"
                                    required
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="register_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Correo Electrónico
                                </label>
                                <input
                                    type="email"
                                    id="register_email"
                                    wire:model="email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                    placeholder="tu@email.com"
                                    required
                                >
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label for="register_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Contraseña
                                </label>
                                <input
                                    type="password"
                                    id="register_password"
                                    wire:model="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                    placeholder="••••••••"
                                    required
                                >
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="register_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Confirmar Contraseña
                                </label>
                                <input
                                    type="password"
                                    id="register_password_confirmation"
                                    wire:model="password_confirmation"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="••••••••"
                                    required
                                >
                            </div>

                            <!-- Área -->
                            <div class="mb-6">
                                <label for="register_area" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Área
                                </label>
                                <select
                                    id="register_area"
                                    wire:model="area_id"
                                    class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent @error('area_id') border-red-500 @else border-gray-300 @enderror"
                                    required
                                >
                                    <option value="">Seleccione su área</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('area_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mensaje de error general -->
                            @error('general')
                                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                    {{ $message }}
                                </div>
                            @enderror

                            <!-- Register Button -->
                            <button
                                type="submit"
                                class="w-full px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 flex items-center justify-center gap-2"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" wire:loading>
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove>Crear Cuenta</span>
                                <span wire:loading>Creando...</span>
                            </button>
                        </form>
                    @endif

                    <!-- Switch Mode -->
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $mode === 'login' ? '¿No tienes una cuenta?' : '¿Ya tienes una cuenta?' }}
                            <button
                                wire:click="switchMode"
                                class="text-green-600 hover:text-green-500 font-medium"
                            >
                                {{ $mode === 'login' ? 'Crear cuenta' : 'Iniciar sesión' }}
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
