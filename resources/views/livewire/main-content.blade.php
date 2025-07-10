<div>
    <x-hero />

    @auth
        <!-- Sección de Gestión de Tickets -->
        <section class="py-16 bg-gradient-to-br from-green-50 to-blue-50 dark:from-gray-800 dark:to-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        Gestión de Tickets
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Crea y gestiona tus solicitudes de soporte de manera fácil y eficiente
                    </p>
                </div>

                <!-- Botón para crear nuevo ticket -->
                <div class="flex justify-center mb-8">
                    @livewire('ticket-create', [], key('ticket-create'))
                </div>

                <!-- Lista de tickets -->
                <div class="max-w-6xl mx-auto">
                    @livewire('ticket-list', [], key('ticket-list'))
                </div>
            </div>
        </section>
    @endauth

    <x-quick-report />

    <x-user-stats />

    <x-service-categories />

    <x-faq />

    <x-system-status />
</div>
