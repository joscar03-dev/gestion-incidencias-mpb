<!-- Notifications Dropdown -->
<div x-show="showNotifications"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     @click.away="showNotifications = false"
     class="absolute right-0 mt-2 w-96 glass-morphism rounded-xl shadow-2xl z-50 max-h-96 overflow-hidden"
     style="display: none;">

    <!-- Header -->
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notificaciones</h3>
                <span x-show="lastCheck"
                      x-text="'Actualizado: ' + (lastCheck ? lastCheck.toLocaleTimeString() : '')"
                      class="text-xs text-gray-500 dark:text-gray-400">
                </span>
            </div>
            <div class="flex items-center space-x-2">
                <button @click="fetchNotifications()"
                        :disabled="loading"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200 disabled:opacity-50">
                    <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
                <button @click="markAllAsRead()"
                        x-show="unreadCount > 0"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                    Marcar todas como leídas
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="max-h-80 overflow-y-auto">
        <div x-show="loading" class="flex items-center justify-center py-8">
            <svg class="animate-spin h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div x-show="!loading && notifications.length === 0" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-sm">No tienes notificaciones</p>
        </div>

        <template x-for="notification in notifications" :key="notification.id">
            <div class="border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200"
                 :class="notification.read_at ? 'opacity-75' : 'bg-blue-50 dark:bg-blue-900/20'">
                <div class="p-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.data.title || 'Notificación'"></p>
                                <div class="flex items-center space-x-2">
                                    <span x-show="!notification.read_at" class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                    <button @click="markAsRead(notification.id)"
                                            x-show="!notification.read_at"
                                            class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="notification.data.body || notification.data.message || 'Nueva notificación'"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2" x-text="new Date(notification.created_at).toLocaleString()"></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Footer -->
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
        <button @click="showNotifications = false"
                class="w-full text-center text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
            Cerrar
        </button>
    </div>
</div>
