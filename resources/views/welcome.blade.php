<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Centro de Soporte - Sistema de Gestión de Incidencias</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .glass-morphism {
            backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.75);
            border-radius: 12px;
            border: 1px solid rgba(209, 213, 219, 0.3);
        }

        .dark .glass-morphism {
            background-color: rgba(17, 24, 39, 0.8);
            border: 1px solid rgba(75, 85, 99, 0.3);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .pulse-ring {
            animation: pulse-ring 1.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.33); }
            80%, 100% { opacity: 0; }
        }

        .bg-pattern {
            background-image:
                radial-gradient(circle at 1px 1px, rgba(255,255,255,0.15) 1px, transparent 0);
            background-size: 20px 20px;
        }

        .dark .bg-pattern {
            background-image:
                radial-gradient(circle at 1px 1px, rgba(255,255,255,0.05) 1px, transparent 0);
        }
    </style>
</head>

<body
    class="antialiased min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300"
    x-data="{
        dark: (localStorage.getItem('theme') ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')) === 'dark',
        showReportModal: false,
        reportForm: {
            titulo: '',
            descripcion: '',
            prioridad: 'Media'
        },
        showMobileMenu: false
    }"
    x-init="$watch('dark', val => { document.documentElement.classList.toggle('dark', val); localStorage.setItem('theme', val ? 'dark' : 'light') }); document.documentElement.classList.toggle('dark', dark);"
>
    <!-- Navigation Header -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-morphism shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg blur opacity-30 pulse-ring"></div>
                        <div class="relative bg-gradient-to-r from-blue-500 to-purple-600 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-xl font-bold gradient-text">Centro de Soporte</h1>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Sistema de Gestión de Incidencias</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <div class="flex items-center space-x-4">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                <span class="font-medium">{{ auth()->user()->name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ auth()->user()->area->nombre ?? 'Sin área' }}</span>
                            </div>

                            <!-- Notifications Dropdown -->
                            <div class="relative" x-data="{
                                showNotifications: false,
                                notifications: [],
                                unreadCount: 0,
                                loading: false,
                                lastCheck: null,
                                pollingInterval: null,
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

                                        // Verificar si hay nuevas notificaciones
                                        const previousUnreadCount = this.unreadCount;
                                        this.notifications = data.notifications || [];
                                        this.unreadCount = data.unread_count || 0;
                                        this.lastCheck = new Date();

                                        // Mostrar indicador visual si hay nuevas notificaciones
                                        if (this.unreadCount > 0 && this.unreadCount > previousUnreadCount && previousUnreadCount >= 0) {
                                            this.showNewNotificationIndicator();
                                            this.playNotificationSound();
                                            // Disparar evento para mostrar toast
                                            window.dispatchEvent(new CustomEvent('new-notification', {
                                                detail: { message: 'Tienes nuevas notificaciones' }
                                            }));
                                        }
                                    } catch (error) {
                                        console.error('Error fetching notifications:', error);
                                    } finally {
                                        this.loading = false;
                                    }
                                },
                                showNewNotificationIndicator() {
                                    // Hacer que el ícono de notificaciones pulse brevemente
                                    const notificationButton = document.querySelector('[data-notification-button]');
                                    if (notificationButton) {
                                        notificationButton.classList.add('animate-bounce');
                                        setTimeout(() => {
                                            notificationButton.classList.remove('animate-bounce');
                                        }, 2000);
                                    }
                                },
                                playNotificationSound() {
                                    // Reproducir sonido de notificación si está disponible
                                    try {
                                        const audio = new Audio('/sonidos/notification.mp3');
                                        audio.volume = 0.3;
                                        audio.play().catch(e => {
                                            // Ignorar errores de reproducción de audio
                                            console.log('No se pudo reproducir el sonido de notificación');
                                        });
                                    } catch (e) {
                                        // Ignorar errores si no hay archivo de sonido
                                    }
                                },
                                startPolling() {
                                    // Polling cada 10 segundos para ser más reactivo
                                    this.pollingInterval = setInterval(() => {
                                        this.fetchNotifications();
                                    }, 10000);
                                },
                                stopPolling() {
                                    if (this.pollingInterval) {
                                        clearInterval(this.pollingInterval);
                                        this.pollingInterval = null;
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
                                        // Actualizar inmediatamente después de marcar como leída
                                        this.fetchNotifications();
                                    } catch (error) {
                                        console.error('Error marking notification as read:', error);
                                    }
                                },
                                async markAllAsRead() {
                                    try {
                                        await fetch('/web/notifications/mark-all-read', {
                                            method: 'PATCH',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                'Content-Type': 'application/json'
                                            }
                                        });
                                        // Actualizar inmediatamente después de marcar todas como leídas
                                        this.fetchNotifications();
                                    } catch (error) {
                                        console.error('Error marking all notifications as read:', error);
                                    }
                                }
                            }"
                            x-init="
                                fetchNotifications();
                                startPolling();
                                // Detener polling cuando la página no esté visible
                                document.addEventListener('visibilitychange', () => {
                                    if (document.hidden) {
                                        stopPolling();
                                    } else {
                                        startPolling();
                                        fetchNotifications();
                                    }
                                });
                            "
                            x-on:beforeunload.window="stopPolling()">
                                <button @click="showNotifications = !showNotifications; if(showNotifications) fetchNotifications()"
                                        data-notification-button
                                        class="relative p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>

                                    <!-- Badge de notificaciones no leídas -->
                                    <span x-show="unreadCount > 0"
                                          x-text="unreadCount > 99 ? '99+' : unreadCount"
                                          x-transition:enter="transition ease-out duration-200"
                                          x-transition:enter-start="opacity-0 transform scale-50"
                                          x-transition:enter-end="opacity-100 transform scale-100"
                                          x-transition:leave="transition ease-in duration-150"
                                          x-transition:leave-start="opacity-100 transform scale-100"
                                          x-transition:leave-end="opacity-0 transform scale-50"
                                          class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-5 h-5 animate-pulse">
                                    </span>

                                    <!-- Indicador de carga -->
                                    <div x-show="loading"
                                         class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full animate-ping opacity-75">
                                    </div>
                                </button>

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
                            </div>

                            <a href="{{ url('/reportar/tickets') }}"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Mis Tickets
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center space-x-3">
                            <a href="{{ url('/login') }}"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Iniciar Sesión
                            </a>
                            <a href="{{ url('/register') }}"
                                class="text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white font-medium transition-colors duration-200">
                                Registrarse
                            </a>
                        </div>
                    @endauth

                    <!-- Theme Toggle -->
                    <button
                        @click="dark = !dark"
                        class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200"
                        :aria-label="dark ? 'Modo claro' : 'Modo oscuro'"
                    >
                        <svg x-show="!dark" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.07l-.71.71M21 12h-1M4 12H3m16.66 5.66l-.71-.71M4.05 4.93l-.71-.71M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="dark" class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="showMobileMenu = !showMobileMenu" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

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
    </nav>

    <!-- Main Content -->
    <main class="pt-16 min-h-screen bg-pattern">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-8 mt-8">
                    <div class="glass-morphism p-4 border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                    {{ session('success') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Hero Section -->
            <section class="py-20 text-center">
                <div class="floating-animation">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg mb-8">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"/>
                        </svg>
                    </div>
                </div>

                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                    <span class="gradient-text">Centro de Soporte</span>
                </h1>

                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-12 leading-relaxed">
                    Tu portal integral para gestionar incidencias y solicitudes de soporte técnico.
                    Reporta problemas, realiza seguimiento y mantente informado del estado de tus tickets.
                </p>

                <!-- Call to Action -->
                @auth
                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-center mb-16">
                        <button @click="showReportModal = true"
                               class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl shadow-lg hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 hover:shadow-xl">
                            <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Reportar Incidencia
                        </button>

                        <a href="{{ url('/reportar') }}"
                           class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 hover:shadow-xl">
                            <svg class="w-6 h-6 mr-3 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Ver Mis Tickets
                        </a>
                    </div>

                    <div class="inline-flex items-center px-4 py-2 bg-green-50 dark:bg-green-900/20 rounded-full">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-green-700 dark:text-green-300">
                            Bienvenido de vuelta, {{ auth()->user()->name }}
                        </span>
                    </div>
                @else
                    <div class="text-center mb-8">
                        <div class="glass-morphism p-6 max-w-md mx-auto mb-8">
                            <div class="flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                Acceso Restringido
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Solo <strong>empleados de la empresa</strong> pueden reportar incidencias y acceder al sistema de soporte.
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ url('/login') }}"
                               class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 hover:shadow-xl">
                                <svg class="w-6 h-6 mr-3 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Iniciar Sesión
                            </a>

                            <a href="{{ url('/register') }}"
                               class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl shadow-lg hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200 hover:shadow-xl">
                                <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                Crear Cuenta
                            </a>
                        </div>
                    </div>
                @endauth
            </section>

            @auth
            <!-- Quick Report Form -->
            <section class="py-16">
                <div class="glass-morphism p-8 rounded-2xl shadow-xl">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-lg mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            Reportar Incidencia
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                            Reporta tu incidencia rápidamente desde aquí. Tu área se asignará automáticamente según tu perfil.
                        </p>
                    </div>

                    <form action="{{ route('tickets.quick-create') }}" method="POST" class="max-w-4xl mx-auto">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="lg:col-span-1">
                                <label for="titulo_rapid" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Título de la Incidencia *
                                </label>
                                <input type="text"
                                       id="titulo_rapid"
                                       name="titulo"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors duration-200"
                                       placeholder="Ej: No puedo acceder al sistema">
                            </div>

                            <div class="lg:col-span-1">
                                <label for="area_rapid" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Área Asignada
                                </label>
                                <div class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        {{ auth()->user()->area->nombre ?? 'Sin área asignada' }}
                                    </div>
                                </div>
                                <input type="hidden" name="area_id" value="{{ auth()->user()->area->id ?? '' }}">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Área asignada automáticamente según tu perfil
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <label for="descripcion_rapid" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Descripción del Problema *
                                </label>
                                <textarea id="descripcion_rapid"
                                          name="descripcion"
                                          rows="4"
                                          required
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors duration-200"
                                          placeholder="Describe el problema en detalle. Incluye información sobre cuándo ocurrió, qué estabas haciendo, mensajes de error, etc."></textarea>
                            </div>

                            <div class="lg:col-span-1">
                                <label for="prioridad_rapid" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Nivel de Prioridad
                                </label>
                                <select id="prioridad_rapid"
                                        name="prioridad"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors duration-200">
                                    <option value="Baja">🟢 Baja - No urgente</option>
                                    <option value="Media" selected>🟡 Media - Normal</option>
                                    <option value="Alta">🟠 Alta - Requiere atención</option>
                                    <option value="Urgente">🔴 Urgente - Crítico</option>
                                </select>
                            </div>

                            <div class="lg:col-span-1 flex items-end">
                                <button type="submit"
                                        class="w-full px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-lg shadow-lg hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-red-300">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Reportar Incidencia
                                </button>
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                ¿Necesitas opciones más avanzadas o adjuntar archivos?
                                <a href="{{ url('/reportar/tickets/create') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium underline">
                                    Usar el formulario completo
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </section>

            <!-- User Statistics -->
            <section class="py-16">
                <div class="glass-morphism p-8 rounded-2xl shadow-xl">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            Resumen de tus Tickets
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300">
                            Estado actual de todas tus solicitudes de soporte
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center card-hover">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="text-3xl font-bold mb-2">{{ auth()->user()->tickets()->count() }}</div>
                                <div class="text-sm font-medium opacity-90">Total de Tickets</div>
                            </div>
                        </div>

                        <div class="text-center card-hover">
                            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="text-3xl font-bold mb-2">{{ auth()->user()->tickets()->where('estado', 'Abierto')->count() }}</div>
                                <div class="text-sm font-medium opacity-90">Tickets Abiertos</div>
                            </div>
                        </div>

                        <div class="text-center card-hover">
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div class="text-3xl font-bold mb-2">{{ auth()->user()->tickets()->where('estado', 'En Progreso')->count() }}</div>
                                <div class="text-sm font-medium opacity-90">En Progreso</div>
                            </div>
                        </div>

                        <div class="text-center card-hover">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="text-3xl font-bold mb-2">{{ auth()->user()->tickets()->where('estado', 'Cerrado')->count() }}</div>
                                <div class="text-sm font-medium opacity-90">Completados</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endauth

            <!-- Service Categories -->
            <section class="py-16">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        Tipos de Soporte Disponibles
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Nuestro equipo de soporte está especializado en diferentes áreas para brindarte la mejor asistencia
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="glass-morphism p-8 rounded-2xl shadow-xl card-hover text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-full shadow-lg mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Soporte Técnico</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Hardware, software, redes, sistemas operativos y aplicaciones empresariales.
                        </p>
                        <div class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-full text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            SLA: 30 minutos
                        </div>
                    </div>

                    <div class="glass-morphism p-8 rounded-2xl shadow-xl card-hover text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full shadow-lg mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Incidencias Generales</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Solicitudes generales, consultas y problemas que no requieren soporte técnico especializado.
                        </p>
                        <div class="inline-flex items-center px-4 py-2 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-full text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            SLA: 1 hora
                        </div>
                    </div>

                    <div class="glass-morphism p-8 rounded-2xl shadow-xl card-hover text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full shadow-lg mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Servicios Administrativos</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Trámites, permisos, gestión documental y otros servicios administrativos corporativos.
                        </p>
                        <div class="inline-flex items-center px-4 py-2 bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-full text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            SLA: 2 horas
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ Section -->
            <section class="py-16">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        Preguntas Frecuentes
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Encuentra respuestas rápidas a las consultas más comunes sobre nuestro sistema de soporte
                    </p>
                </div>

                <div class="max-w-4xl mx-auto">
                    <div x-data="{ openFaq: null }" class="space-y-4">
                        <div class="glass-morphism rounded-xl shadow-lg overflow-hidden">
                            <button @click="openFaq === 1 ? openFaq = null : openFaq = 1"
                                    class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 dark:text-white">¿Cómo puedo crear una cuenta?</span>
                                <svg :class="{'rotate-180': openFaq === 1}" class="w-6 h-6 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openFaq === 1" x-collapse class="px-6 pb-6">
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <p class="text-gray-700 dark:text-gray-300">
                                        Para crear una cuenta, haz clic en "Crear Cuenta" en la parte superior de la página. Solo los empleados de la empresa pueden registrarse con su correo corporativo.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="glass-morphism rounded-xl shadow-lg overflow-hidden">
                            <button @click="openFaq === 2 ? openFaq = null : openFaq = 2"
                                    class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 dark:text-white">¿Dónde puedo ver mis tickets?</span>
                                <svg :class="{'rotate-180': openFaq === 2}" class="w-6 h-6 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openFaq === 2" x-collapse class="px-6 pb-6">
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <p class="text-gray-700 dark:text-gray-300">
                                        Una vez autenticado, puedes ver todos tus tickets haciendo clic en "Ver Mis Tickets" o visitando el panel de control para gestionar y dar seguimiento a tus solicitudes.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="glass-morphism rounded-xl shadow-lg overflow-hidden">
                            <button @click="openFaq === 3 ? openFaq = null : openFaq = 3"
                                    class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 dark:text-white">¿Cuánto tiempo tarda la respuesta?</span>
                                <svg :class="{'rotate-180': openFaq === 3}" class="w-6 h-6 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openFaq === 3" x-collapse class="px-6 pb-6">
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <p class="text-gray-700 dark:text-gray-300">
                                        Los tiempos de respuesta varían según el tipo de solicitud: Soporte Técnico (30 min), Incidencias Generales (1 hora), Servicios Administrativos (2 horas). Los tickets urgentes son priorizados.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="glass-morphism rounded-xl shadow-lg overflow-hidden">
                            <button @click="openFaq === 4 ? openFaq = null : openFaq = 4"
                                    class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                                <span class="text-lg font-semibold text-gray-900 dark:text-white">¿Cómo puedo adjuntar archivos?</span>
                                <svg :class="{'rotate-180': openFaq === 4}" class="w-6 h-6 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openFaq === 4" x-collapse class="px-6 pb-6">
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <p class="text-gray-700 dark:text-gray-300">
                                        Para adjuntar archivos como capturas de pantalla o documentos, utiliza el "formulario completo" en lugar del reporte rápido. Allí encontrarás la opción para subir archivos.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- System Status -->
            <section class="py-16">
                <div class="glass-morphism p-8 rounded-2xl shadow-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                                    <div class="absolute inset-0 w-4 h-4 bg-green-500 rounded-full animate-ping opacity-75"></div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Estado del Sistema</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Todos los servicios operativos</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Última actualización: {{ now()->format('d/m/Y H:i') }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                Próxima verificación: {{ now()->addMinutes(5)->format('H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
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
                            <span>{{ Illuminate\Foundation\Application::VERSION }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        @auth
                            <li><a href="{{ url('/reportar') }}" class="hover:text-white transition-colors">Mis Tickets</a></li>
                            <li><a href="{{ url('/reportar/tickets/create') }}" class="hover:text-white transition-colors">Crear Ticket</a></li>
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

    <!-- Enhanced Modal -->
    <div x-show="showReportModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-50 backdrop-blur-sm" @click="showReportModal = false"></div>

            <div class="inline-block w-full max-w-2xl p-0 my-8 overflow-hidden text-left align-middle transition-all transform glass-morphism shadow-2xl rounded-2xl">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white/20 rounded-full p-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Reportar Incidencia</h3>
                                <p class="text-red-100 text-sm">Rápido y sencillo</p>
                            </div>
                        </div>
                        <button @click="showReportModal = false" class="text-white hover:text-red-200 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form action="{{ url('/reportar/tickets') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="titulo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Título de la Incidencia *
                                </label>
                                <input type="text"
                                       id="titulo"
                                       name="titulo"
                                       x-model="reportForm.titulo"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:text-white transition-colors duration-200"
                                       placeholder="Describe brevemente el problema">
                            </div>

                            <div>
                                <label for="area_modal" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Área Asignada
                                </label>
                                <div class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        {{ auth()->user()->area->nombre ?? 'Sin área asignada' }}
                                    </div>
                                </div>
                                <input type="hidden" name="area_id" value="{{ auth()->user()->area->id ?? '' }}">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    Área asignada automáticamente según tu perfil
                                </p>
                            </div>
                        </div>

                        <div>
                            <label for="descripcion" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Descripción del Problema *
                            </label>
                            <textarea id="descripcion"
                                      name="descripcion"
                                      x-model="reportForm.descripcion"
                                      rows="4"
                                      required
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:text-white transition-colors duration-200"
                                      placeholder="Describe el problema en detalle. Incluye cuándo ocurrió, qué estabas haciendo, mensajes de error, etc."></textarea>
                        </div>

                        <div>
                            <label for="prioridad" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Nivel de Prioridad
                            </label>
                            <select id="prioridad"
                                    name="prioridad"
                                    x-model="reportForm.prioridad"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:text-white transition-colors duration-200">
                                <option value="Baja">🟢 Baja - No urgente</option>
                                <option value="Media">🟡 Media - Normal</option>
                                <option value="Alta">🟠 Alta - Requiere atención</option>
                                <option value="Urgente">🔴 Urgente - Crítico</option>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button"
                                    @click="showReportModal = false"
                                    class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-red-500 to-red-600 rounded-lg hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 shadow-lg focus:outline-none focus:ring-4 focus:ring-red-300">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Reportar Incidencia
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div x-data="{
        toasts: [],
        addToast(message, type = 'info') {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => this.removeToast(id), 5000);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(toast => toast.id !== id);
        }
    }"
    x-on:new-notification.window="addToast($event.detail.message, 'info')"
    class="fixed bottom-4 right-4 z-50 space-y-2">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform translate-x-full"
                 class="glass-morphism p-4 rounded-lg shadow-lg max-w-sm">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="toast.message"></p>
                    </div>
                    <button @click="removeToast(toast.id)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
</body>

</html>
