import './bootstrap';

// Importar Alpine.js
import Alpine from 'alpinejs';

// Configurar data global para Alpine
Alpine.data('appData', () => ({
    dark: (localStorage.getItem('theme') ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')) === 'dark',
    showReportModal: false,
    reportForm: {
        titulo: '',
        descripcion: '',
        prioridad: 'Media'
    },
    showMobileMenu: false,

    init() {
        this.$watch('dark', val => {
            document.documentElement.classList.toggle('dark', val);
            localStorage.setItem('theme', val ? 'dark' : 'light');
        });
        document.documentElement.classList.toggle('dark', this.dark);
    }
}));

// Configurar Alpine.js
window.Alpine = Alpine;

// Configuración simplificada para Alpine + Livewire
document.addEventListener('DOMContentLoaded', function() {
    // Aplicar Alpine data solo a elementos específicos
    const elementsToInitialize = document.querySelectorAll('[data-alpine-navigation], .report-modal, .toast');

    elementsToInitialize.forEach(element => {
        if (!element.hasAttribute('x-data')) {
            element.setAttribute('x-data', 'appData()');
        }
    });

    // Inicializar Alpine
    Alpine.start();
});
