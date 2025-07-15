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

    // Inicializar carrusel de videos
    initVideoCarousel();
});

// Función para manejar el carrusel de videos
function initVideoCarousel() {
    const carousels = ['videoCarousel', 'videoCarousel2'];
    
    carousels.forEach(carouselId => {
        const carousel = document.getElementById(carouselId);
        if (!carousel) return;
        
        const videos = carousel.querySelectorAll('.video-slide');
        const indicators = carousel.querySelectorAll('.video-indicator');
        const prevBtn = carousel.querySelector('.video-prev');
        const nextBtn = carousel.querySelector('.video-next');
        
        if (videos.length <= 1) return;
        
        let currentIndex = 0;
        let intervalId;
        
        // Función para actualizar indicadores
        function updateIndicators() {
            indicators.forEach((indicator, index) => {
                if (index === currentIndex) {
                    indicator.classList.remove('bg-white/30');
                    indicator.classList.add('bg-white/80', 'scale-125');
                } else {
                    indicator.classList.remove('bg-white/80', 'scale-125');
                    indicator.classList.add('bg-white/30');
                }
            });
        }
        
        // Configurar videos
        videos.forEach((video, index) => {
            video.addEventListener('loadeddata', () => {
                console.log(`Video ${index + 1} cargado en ${carouselId}`);
            });
            
            video.addEventListener('error', () => {
                console.log(`Error cargando video ${index + 1} en ${carouselId}`);
                // Si hay error, intentar siguiente fuente
                const sources = video.querySelectorAll('source');
                const currentSource = Array.from(sources).find(s => s.src === video.currentSrc);
                const currentSourceIndex = Array.from(sources).indexOf(currentSource);
                
                if (currentSourceIndex < sources.length - 1) {
                    video.src = sources[currentSourceIndex + 1].src;
                    video.load();
                } else {
                    // Si todos los videos fallan, mostrar fallback
                    showFallbackGradient(carouselId);
                }
            });
        });
        
        // Función para cambiar video
        function changeVideo(newIndex) {
            // Ocultar video actual
            videos[currentIndex].style.opacity = '0';
            videos[currentIndex].pause();
            
            // Mostrar nuevo video
            currentIndex = newIndex;
            videos[currentIndex].style.opacity = '1';
            videos[currentIndex].play().catch(error => {
                console.log(`Error reproduciendo video ${currentIndex + 1}:`, error);
            });
            
            // Actualizar indicadores
            updateIndicators();
        }
        
        // Función para siguiente video
        function nextVideo() {
            const newIndex = (currentIndex + 1) % videos.length;
            changeVideo(newIndex);
        }
        
        // Función para video anterior
        function prevVideo() {
            const newIndex = (currentIndex - 1 + videos.length) % videos.length;
            changeVideo(newIndex);
        }
        
        // Iniciar carrusel automático
        function startCarousel() {
            intervalId = setInterval(nextVideo, 8000); // Cambiar cada 8 segundos
        }
        
        // Detener carrusel
        function stopCarousel() {
            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
            }
        }
        
        // Precargar videos
        videos.forEach((video, index) => {
            if (index === 0) {
                video.play().catch(error => {
                    console.log(`Error reproduciendo video inicial:`, error);
                });
            } else {
                video.load(); // Precargar otros videos
            }
        });
        
        // Configurar controles de navegación
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                stopCarousel();
                nextVideo();
                setTimeout(startCarousel, 5000); // Reiniciar después de 5 segundos
            });
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                stopCarousel();
                prevVideo();
                setTimeout(startCarousel, 5000); // Reiniciar después de 5 segundos
            });
        }
        
        // Configurar indicadores clicables
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                stopCarousel();
                changeVideo(index);
                setTimeout(startCarousel, 5000); // Reiniciar después de 5 segundos
            });
            
            // Hacer indicadores clicables visualmente
            indicator.classList.add('cursor-pointer', 'hover:bg-white/60');
        });
        
        // Inicializar indicadores
        updateIndicators();
        
        // Iniciar carrusel después de 3 segundos
        setTimeout(startCarousel, 3000);
        
        // Pausar carrusel cuando la ventana no está visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopCarousel();
                videos.forEach(video => video.pause());
            } else {
                startCarousel();
                videos[currentIndex].play().catch(error => {
                    console.log(`Error reanudando video:`, error);
                });
            }
        });
        
        // Pausar carrusel al hover (opcional)
        carousel.addEventListener('mouseenter', stopCarousel);
        carousel.addEventListener('mouseleave', startCarousel);
    });
}

// Función para mostrar gradiente de respaldo
function showFallbackGradient(carouselId) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;
    
    const fallbackId = carouselId === 'videoCarousel' ? 'fallbackGradient' : 'fallbackGradient2';
    const fallback = document.getElementById(fallbackId);
    
    if (fallback) {
        // Ocultar todos los videos
        carousel.querySelectorAll('.video-slide').forEach(video => {
            video.style.opacity = '0';
            video.pause();
        });
        
        // Mostrar gradiente de respaldo
        fallback.style.opacity = '1';
        console.log(`Mostrando gradiente de respaldo para ${carouselId}`);
    }
}
