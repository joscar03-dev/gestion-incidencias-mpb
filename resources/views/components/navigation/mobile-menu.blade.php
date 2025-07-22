<!-- Mobile Menu -->
<div x-show="showMobileMenu" x-transition class="md:hidden border-t border-gray-200 dark:border-gray-700">
    <div class="px-4 py-3 space-y-2">
        @auth
            <div class="py-2">
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->area->nombre ?? 'Sin área' }}</p>
            </div>

            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 1v4" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 1v4" />
                    </svg>
                    <span>Mis Tickets</span>
                </div>
            </a>

            <!-- Dispositivos Link -->
            <a href="{{ route('dispositivos.usuario') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                    <span>Mis Dispositivos</span>
                </div>
            </a>

            <!-- Notifications Section -->
            <div class="py-2" x-data="{
                showNotifications: false,
                notifications: [],
                unreadCount: 0,
                loading: false,
                async fetchNotifications() {
                    this.loading = true;
                    try {
                        const response = await fetch('/web/notifications', {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Content-Type': 'application/json'
                            }
                        });
                        const data = await response.json();
                        this.notifications = data.notifications;
                        this.unreadCount = data.unreadCount;
                    } catch (error) {
                        console.error('Error fetching notifications:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                async markAsRead(notificationId) {
                    try {
                        await fetch(`/web/notifications/${notificationId}/read`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Content-Type': 'application/json'
                            }
                        });
                        this.fetchNotifications();
                    } catch (error) {
                        console.error('Error marking notification as read:', error);
                    }
                }
            }" x-init="fetchNotifications()">
                <!-- Notifications Toggle -->
                <button @click="showNotifications = !showNotifications; if(showNotifications) fetchNotifications()"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800">
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V9a6 6 0 00-6-6 6 6 0 00-6 6v3l-5 5h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span x-show="unreadCount > 0"
                                  x-text="unreadCount"
                                  class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full min-w-[16px] h-4">
                            </span>
                        </div>
                        <span>Notificaciones</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200"
                         :class="{ 'rotate-180': showNotifications }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Mobile Notifications Dropdown -->
                <div x-show="showNotifications"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="mt-2 mx-3 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 max-h-64 overflow-y-auto">

                    <!-- Loading State -->
                    <div x-show="loading" class="flex items-center justify-center py-4">
                        <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <!-- No Notifications -->
                    <div x-show="!loading && notifications.length === 0" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V9a6 6 0 00-6-6 6 6 0 00-6 6v3l-5 5h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-sm">No hay notificaciones</p>
                    </div>

                    <!-- Notifications List -->
                    <div x-show="!loading && notifications.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="notification in notifications.slice(0, 5)" :key="notification.id">
                            <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                                 :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notification.read_at }">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"
                                             x-show="!notification.read_at"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="notification.data.title"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="notification.data.message"></p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="new Date(notification.created_at).toLocaleString()"></p>
                                    </div>
                                    <button @click="markAsRead(notification.id)"
                                            x-show="!notification.read_at"
                                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        Marcar leída
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Theme Toggle Section -->
            <div class="py-2">
                <div class="flex items-center justify-between px-3 py-2">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.07l-.71.71M21 12h-1M4 12H3m16.66 5.66l-.71-.71M4.05 4.93l-.71-.71M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tema</span>
                    </div>
                    @include('components.navigation.theme-toggle')
                </div>
            </div>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Cerrar Sesión</span>
                    </div>
                </button>
            </form>
        @else
            <button onclick="Livewire.dispatch('openAuthModal', { mode: 'login' })" class="block w-full text-left px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>Iniciar Sesión</span>
                </div>
            </button>
            <button onclick="Livewire.dispatch('openAuthModal', { mode: 'register' })" class="block w-full text-left px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Registrarse</span>
                </div>
            </button>

            <!-- Theme Toggle para usuarios no autenticados -->
            <div class="py-2">
                <div class="flex items-center justify-between px-3 py-2">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.07l-.71.71M21 12h-1M4 12H3m16.66 5.66l-.71-.71M4.05 4.93l-.71-.71M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tema</span>
                    </div>
                    @include('components.navigation.theme-toggle')
                </div>
            </div>
        @endauth
    </div>
</div>
