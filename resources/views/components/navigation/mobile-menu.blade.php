<!-- Mobile Menu -->
<div x-show="showMobileMenu" x-transition class="md:hidden border-t border-gray-200 dark:border-gray-700">
    <div class="px-4 py-3 space-y-2">
        @auth
            <div class="py-2">
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->area->nombre ?? 'Sin área' }}</p>
            </div>
            <a href="{{ url('/reportar/tickets') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800">
                Mis Tickets
            </a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800">
                    Cerrar Sesión
                </button>
            </form>
        @else
            <a href="{{ url('/login') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800">
                Iniciar Sesión
            </a>
            <a href="{{ url('/register') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800">
                Registrarse
            </a>
        @endauth
    </div>
</div>
