# Sistema de Carrusel de Videos - Hero Section

## 🎬 Configuración del Carrusel

El sistema ahora incluye un carrusel de videos que rota automáticamente entre 4 videos diferentes, creando un fondo dinámico y atractivo.

### Estructura del Carrusel

```
Video 1 (8s) → Video 2 (8s) → Video 3 (8s) → Video 4 (8s) → Loop
```

### 1. Videos Locales (Prioridad 1)
- `public/videos/background-1.mp4` - Video principal azul
- `public/videos/background-2.mp4` - Video tecnológico
- `public/videos/background-3.mp4` - Video de redes
- `public/videos/background-4.mp4` - Video de partículas

### 2. Videos Externos de Respaldo (Prioridad 2)
- **Video 1**: Fondo abstracto azul de Mixkit
- **Video 2**: Dispositivos tecnológicos en azul
- **Video 3**: Malla de red animada
- **Video 4**: Partículas púrpuras verticales

### 3. Fallback Animado (Prioridad 3)
- Gradiente CSS animado como último recurso

## 🎮 Controles del Carrusel

### Navegación Automática
- **Duración**: 8 segundos por video
- **Transición**: Fade de 1 segundo
- **Preloading**: Los videos se precargan para transiciones fluidas

### Controles Manuales
- **Botones**: Anterior/Siguiente en los laterales
- **Indicadores**: Puntos clicables en la parte inferior
- **Hover**: Pausa automática al pasar el mouse
- **Visibilidad**: Pausa cuando la pestaña no está activa

### Funcionalidades Avanzadas
- **Auto-reinicio**: Después de navegación manual, se reinicia automáticamente
- **Detección de errores**: Fallback automático si un video falla
- **Responsive**: Adaptable a diferentes tamaños de pantalla
- **Accesibilidad**: Etiquetas ARIA y controles de teclado

## Cómo Agregar Videos Locales

1. **Descargar videos de fondo apropiados:**
   - Resolución recomendada: 1920x1080 (Full HD)
   - Duración: 10-30 segundos para loop seamless
   - Formatos: MP4 (H.264) y WebM (VP9)

2. **Colocar en la carpeta correcta:**
   ```
   public/videos/
   ├── background.mp4
   └── background.webm
   ```

3. **Recursos recomendados para videos gratuitos:**
   - [Mixkit](https://mixkit.co/free-stock-video/) - Videos gratuitos de alta calidad
   - [Pexels](https://www.pexels.com/videos/) - Videos libres de derechos
   - [Pixabay](https://pixabay.com/videos/) - Videos gratuitos

## Personalización

### Cambiar los videos de respaldo:
Editar `resources/views/components/hero.blade.php`:
```blade
<source src="{{ asset('videos/tu-video.mp4') }}" type="video/mp4">
<source src="{{ asset('videos/tu-video.webm') }}" type="video/webm">
```

### Ajustar la animación de fallback:
Editar `resources/css/app.css`:
```css
.animate-gradient-x {
    background-size: 200% 200%;
    animation: gradient-flow 15s ease infinite;
}
```

## Configuración Actual del Video

- **Autoplay**: Sí (muted para cumplir políticas del navegador)
- **Loop**: Sí (reproducción continua)
- **Muted**: Sí (requerido para autoplay)
- **Poster**: Gradiente SVG como imagen de carga
- **Responsive**: Sí (object-cover para mantener proporción)

## Troubleshooting

### El video no se reproduce:
1. Verificar que los archivos estén en `public/videos/`
2. Verificar que los archivos tengan los nombres correctos
3. Verificar que el servidor web tenga permisos de lectura
4. Verificar que los archivos estén en formato soportado

### Fallback activado:
Si ves el gradiente animado en lugar del video, significa que:
- No se encontraron videos locales
- Los videos externos no están disponibles
- El navegador no soporta los formatos de video

## Estructura de Archivos

```
public/
├── videos/
│   ├── background.mp4      # Video principal
│   ├── background.webm     # Video alternativo
│   └── background.html     # Generador de fondo animado
├── css/
└── js/

resources/
├── views/components/
│   └── hero.blade.php      # Componente con video
└── css/
    └── app.css             # Animaciones CSS
```

## Recomendaciones de Rendimiento

1. **Optimizar videos:**
   - Usar compresión H.264 para MP4
   - Usar VP9 para WebM
   - Mantener tamaño de archivo < 10MB

2. **Lazy loading:**
   - Los videos se cargan solo cuando son visibles
   - Poster image se muestra durante la carga

3. **Responsive:**
   - Videos se adaptan a diferentes tamaños de pantalla
   - Fallback funciona en todos los dispositivos
