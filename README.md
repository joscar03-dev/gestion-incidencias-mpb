# 📋 Sistema de Gestión de Incidencias

Un sistema completo de gestión de tickets de soporte técnico desarrollado con Laravel 11, Livewire 3 y Filament 3.

## 🚀 Características Principales

- **Sistema de Tickets**: Creación, asignación y seguimiento de incidencias
- **Gestión de SLA**: Control automático de tiempos de respuesta y resolución
- **Panel Administrativo**: Interfaz completa con Filament para administradores
- **Interfaz de Usuario**: SPA moderna con Livewire para usuarios finales
- **Sistema de Roles**: Control granular de permisos con Spatie Permission
- **Escalamiento Automático**: Escalamiento inteligente basado en SLA
- **Exportación de Datos**: Generación de reportes en Excel y PDF
- **Dashboard Interactivo**: Métricas y gráficos en tiempo real
- **Sistema de Comentarios**: Seguimiento detallado de conversaciones
- **Notificaciones**: Alertas en tiempo real para usuarios y administradores

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **Admin Panel**: Filament 3
- **Base de Datos**: MySQL
- **Autenticación**: Laravel Breeze
- **Permisos**: Spatie Laravel Permission
- **Reportes**: Laravel Excel, DomPDF
- **Gráficos**: Apex Charts
- **Comentarios**: Commentions Package

## 📁 Estructura del Proyecto

```
gestion-incidencias/
├── app/
│   ├── Console/
│   │   └── Commands/           # Comandos personalizados
│   ├── Exports/               # Clases para exportación Excel/PDF
│   ├── Filament/             # Panel administrativo Filament
│   │   ├── Resources/        # Recursos CRUD
│   │   ├── Widgets/          # Widgets del dashboard
│   │   └── Pages/            # Páginas personalizadas
│   ├── Http/
│   │   ├── Controllers/      # Controladores
│   │   ├── Middleware/       # Middleware personalizado
│   │   └── Requests/         # Form Requests
│   ├── Jobs/                 # Jobs para cola de trabajos
│   ├── Livewire/            # Componentes Livewire
│   ├── Models/              # Modelos Eloquent
│   ├── Observers/           # Observadores de modelos
│   └── Policies/            # Políticas de autorización
├── database/
│   ├── factories/           # Factories para testing
│   ├── migrations/          # Migraciones de base de datos
│   └── seeders/            # Seeders para datos iniciales
├── resources/
│   ├── views/              # Vistas Blade
│   │   ├── components/     # Componentes reutilizables
│   │   ├── exports/        # Plantillas para reportes
│   │   └── livewire/       # Vistas de componentes Livewire
│   ├── css/                # Estilos CSS
│   └── js/                 # JavaScript
└── docs/                   # Documentación detallada
    ├── installation.md     # Guía de instalación
    ├── api.md             # Documentación de API
    ├── models.md          # Documentación de modelos
    ├── filament.md        # Documentación de Filament
    └── frontend.md        # Documentación del frontend
```

## 🔧 Instalación

### Prerrequisitos

- PHP 8.2 o superior
- Composer
- Node.js 16+ y npm
- MySQL 8.0+
- Apache/Nginx

### Instalación Paso a Paso

1. **Clonar el repositorio**
```bash
git clone https://github.com/joscar03-dev/gestion-incidencias-mpb.git
cd gestion-incidencias-mpb
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node.js**
```bash
npm install
```

4. **Configurar variables de entorno**
```bash
cp .env.example .env
```

5. **Configurar base de datos en `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_incidencias
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

6. **Generar clave de aplicación**
```bash
php artisan key:generate
```

7. **Ejecutar migraciones y seeders**
```bash
php artisan migrate --seed
```

8. **Crear enlace simbólico para storage**
```bash
php artisan storage:link
```

9. **Compilar assets**
```bash
npm run build
```

10. **Iniciar el servidor**
```bash
php artisan serve
```

### Configuración Adicional

**Configurar colas de trabajo:**
```bash
php artisan queue:work
```

**Configurar tareas programadas (cron):**
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 👥 Usuarios por Defecto

Después de ejecutar los seeders, tendrás estos usuarios:

- **Super Admin**: admin@admin.com / password
- **Admin**: admin2@admin.com / password
- **Técnico**: tecnico@tecnico.com / password
- **Usuario**: user@user.com / password

## 🎯 Características Detalladas

### Sistema de Tickets

- **Creación**: Formulario intuitivo con validación en tiempo real
- **Asignación**: Automática basada en área y carga de trabajo
- **Estados**: Abierto, En Progreso, Escalado, Cerrado, Cancelado, Archivado
- **Prioridades**: Crítica, Alta, Media, Baja
- **Archivos**: Soporte para adjuntos (PDF, imágenes)

### Gestión de SLA

- **Configuración por Área**: Tiempos personalizados por departamento
- **Escalamiento Automático**: Basado en vencimiento de SLA
- **Factores de Prioridad**: Multiplicadores por tipo de incidencia
- **Monitoreo**: Dashboard con indicadores de cumplimiento

### Panel Administrativo

- **Dashboard**: Métricas, gráficos y KPIs
- **Gestión de Usuarios**: CRUD completo con roles
- **Configuración de Áreas**: Departamentos y responsables
- **Configuración de SLA**: Tiempos y escalamiento
- **Reportes**: Generación de informes detallados

### Interfaz de Usuario

- **Dashboard Personal**: Vista de tickets del usuario
- **Creación de Tickets**: Formulario simplificado
- **Seguimiento**: Estado y progreso en tiempo real
- **Comunicación**: Sistema de comentarios integrado

## 📊 Métricas y Reportes

- **Tickets por Estado**: Distribución actual
- **Tickets por Prioridad**: Análisis de criticidad
- **Cumplimiento de SLA**: Porcentajes de cumplimiento
- **Productividad**: Tickets resueltos por técnico
- **Tendencias**: Análisis temporal de incidencias

## 🔐 Seguridad

- **Autenticación**: Laravel Breeze con verificación de email
- **Autorización**: Roles y permisos granulares
- **Validación**: Validación robusta en frontend y backend
- **Sanitización**: Protección contra XSS y SQL injection
- **CSRF**: Protección contra ataques CSRF

## 📱 Responsividad

- **Mobile First**: Diseño optimizado para dispositivos móviles
- **Tablet Friendly**: Adaptación perfecta para tablets
- **Desktop**: Experiencia completa en escritorio
- **PWA Ready**: Preparado para Progressive Web App

## 🚀 Rendimiento

- **Paginación**: Carga eficiente de datos
- **Lazy Loading**: Carga diferida de componentes
- **Cacheo**: Optimización de consultas frecuentes
- **Compresión**: Assets optimizados y comprimidos

## 🔄 Flujo de Trabajo

1. **Usuario crea ticket** → Sistema asigna automáticamente
2. **Técnico recibe notificación** → Comienza trabajo
3. **Seguimiento de SLA** → Alertas por vencimiento
4. **Escalamiento automático** → Si se vence SLA
5. **Resolución** → Cierre con comentarios
6. **Métricas** → Actualización de dashboard

## 📋 Roadmap

- [ ] API REST completa
- [ ] App móvil nativa
- [ ] Integración con sistemas externos
- [ ] Chatbot automático
- [ ] Análisis predictivo con IA
- [ ] Modo offline

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver `LICENSE` para más detalles.

## 📞 Soporte

Para soporte técnico, contacta:
- Email: locomancocapac@gmail.co

---

**Desarrollado con ❤️**

## 📚 Documentación Adicional

Para más detalles técnicos, consulta la documentación específica en la carpeta `docs/`:

- 📖 [Guía de Instalación](docs/installation.md)
- 🔌 [Documentación de API](docs/api.md)
- 🗄️ [Modelos y Base de Datos](docs/models.md)
- 🎛️ [Panel Administrativo Filament](docs/filament.md)
- 🎨 [Interfaz de Usuario](docs/frontend.md)
