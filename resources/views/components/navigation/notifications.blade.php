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
    @include('components.navigation.notification-button')
    @include('components.navigation.notification-dropdown')
</div>
