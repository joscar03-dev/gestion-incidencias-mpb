<footer class="bg-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Centro de Soporte</h3>
                        <p class="text-gray-400 text-sm">Sistema de Gestión de Incidencias</p>
                    </div>
                </div>
                <p class="text-gray-400 mb-4 max-w-md">
                    Plataforma integral para la gestión eficiente de incidencias y solicitudes de soporte técnico empresarial.
                </p>
                <div class="flex space-x-4">
                    <div class="text-sm text-gray-400">
                        <span class="font-medium">Estado:</span>
                        <span class="text-green-400">Operativo</span>
                    </div>
                    <div class="text-sm text-gray-400">
                        <span class="font-medium">Versión:</span>
                        <span>{{ env('VERSION_ID') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Enlaces Rápidos</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    @auth
                        <li><a href="{{ url('/dashboard') }}" class="hover:text-white transition-colors">Mis Tickets</a></li>
                      <li><a href="{{ url('/dashboard?view=create') }}" class="hover:text-white transition-colors">Crear Ticket</a></li>
                    @else
                        <li><a href="{{ url('/login') }}" class="hover:text-white transition-colors">Iniciar Sesión</a></li>
                        <li><a href="{{ url('/register') }}" class="hover:text-white transition-colors">Registrarse</a></li>
                    @endauth
                    <li><a href="#" class="hover:text-white transition-colors">Documentación</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Políticas de Uso</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Soporte</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        24/7 Disponible
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        soporte@empresa.com
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Ext. 1234
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
            <div class="text-sm text-gray-400">
                © {{ date('Y') }} Centro de Soporte. Todos los derechos reservados.
            </div>
            <div class="text-sm text-gray-400 mt-4 md:mt-0">
                Desarrollado con Laravel v{{ Illuminate\Foundation\Application::VERSION }}
            </div>
        </div>
    </div>
</footer>
