# 🎬 Configuración del Carrusel de Videos

## Videos Recomendados para Descargar

### 1. Video Principal (background-1.mp4)
**Tema**: Tecnología/Abstracto Azul
- **URL**: https://assets.mixkit.co/videos/preview/mixkit-digital-abstract-blue-background-4031-large.mp4
- **Descripción**: Fondo abstracto digital en tonos azules
- **Duración**: 15 segundos (perfecto para loop)

### 2. Video Tecnológico (background-2.mp4)
**Tema**: Dispositivos Tecnológicos
- **URL**: https://assets.mixkit.co/videos/preview/mixkit-tech-devices-background-in-blue-4033-large.mp4
- **Descripción**: Fondo con dispositivos tecnológicos en azul
- **Duración**: 20 segundos

### 3. Video de Red (background-3.mp4)
**Tema**: Conexiones de Red
- **URL**: https://assets.mixkit.co/videos/preview/mixkit-network-mesh-4166-large.mp4
- **Descripción**: Malla de red animada con conexiones
- **Duración**: 10 segundos

### 4. Video de Partículas (background-4.mp4)
**Tema**: Partículas Animadas
- **URL**: https://assets.mixkit.co/videos/preview/mixkit-purple-particles-moving-vertically-26074-large.mp4
- **Descripción**: Partículas púrpuras en movimiento vertical
- **Duración**: 12 segundos

## Instrucciones de Instalación

### Paso 1: Crear la carpeta de videos
```bash
mkdir public/videos
```

### Paso 2: Descargar los videos
```bash
# Descargar video 1
curl -o public/videos/background-1.mp4 "https://assets.mixkit.co/videos/preview/mixkit-digital-abstract-blue-background-4031-large.mp4"

# Descargar video 2
curl -o public/videos/background-2.mp4 "https://assets.mixkit.co/videos/preview/mixkit-tech-devices-background-in-blue-4033-large.mp4"

# Descargar video 3
curl -o public/videos/background-3.mp4 "https://assets.mixkit.co/videos/preview/mixkit-network-mesh-4166-large.mp4"

# Descargar video 4
curl -o public/videos/background-4.mp4 "https://assets.mixkit.co/videos/preview/mixkit-purple-particles-moving-vertically-26074-large.mp4"
```

### Paso 3: Verificar la instalación
```bash
ls -la public/videos/
# Deberías ver 4 archivos .mp4
```

## Alternativas de Descarga

### Opción A: Descarga Manual
1. Visita cada URL en tu navegador
2. Haz clic derecho → "Guardar video como..."
3. Guarda en `public/videos/` con el nombre correspondiente

### Opción B: Usar PowerShell (Windows)
```powershell
# Crear carpeta
New-Item -ItemType Directory -Force -Path "public/videos"

# Descargar videos
Invoke-WebRequest -Uri "https://assets.mixkit.co/videos/preview/mixkit-digital-abstract-blue-background-4031-large.mp4" -OutFile "public/videos/background-1.mp4"
Invoke-WebRequest -Uri "https://assets.mixkit.co/videos/preview/mixkit-tech-devices-background-in-blue-4033-large.mp4" -OutFile "public/videos/background-2.mp4"
Invoke-WebRequest -Uri "https://assets.mixkit.co/videos/preview/mixkit-network-mesh-4166-large.mp4" -OutFile "public/videos/background-3.mp4"
Invoke-WebRequest -Uri "https://assets.mixkit.co/videos/preview/mixkit-purple-particles-moving-vertically-26074-large.mp4" -OutFile "public/videos/background-4.mp4"
```

## Personalización

### Cambiar Videos
Para reemplazar cualquier video:
1. Coloca tu video en `public/videos/`
2. Renómbralo como `background-X.mp4` (donde X es 1-4)
3. El carrusel lo detectará automáticamente

### Agregar Más Videos
Para agregar más videos al carrusel:
1. Edita `resources/views/components/hero.blade.php`
2. Agrega un nuevo elemento `<video>` con `data-video="5"`
3. Actualiza los indicadores en ambas secciones del hero
4. Los videos se rotarán automáticamente

### Cambiar Tiempos
Para modificar la duración de cada video:
- Edita `resources/js/app.js`
- Cambia el valor `8000` (8 segundos) en `setInterval(nextVideo, 8000)`

## Características del Sistema

### ✅ Funcionalidades Incluidas
- **Rotación automática** cada 8 segundos
- **Navegación manual** con botones y indicadores
- **Preloading** para transiciones fluidas
- **Fallback automático** si los videos fallan
- **Pausa inteligente** al hover o pestaña inactiva
- **Responsive** en todos los dispositivos
- **Accesibilidad** con controles ARIA

### 🎮 Controles
- **Flechas laterales**: Navegación manual
- **Indicadores**: Puntos clicables en la parte inferior
- **Hover**: Pausa automática al pasar el mouse
- **Auto-reinicio**: Después de navegación manual

### 🔧 Optimización
- Videos externos como respaldo
- Gradiente CSS como último recurso
- Detección de errores automática
- Gestión de memoria eficiente

¡Tu carrusel de videos está listo para usar! 🎉
