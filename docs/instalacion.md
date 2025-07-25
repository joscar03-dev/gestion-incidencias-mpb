# 🚀 Guía de Instalación y Despliegue

Esta guía detalla el proceso completo de instalación, configuración y despliegue del Sistema de Gestión de Incidencias en diferentes entornos.

## 📋 Índice

1. [Requisitos del Sistema](#requisitos-del-sistema)
2. [Instalación Local](#instalación-local)
3. [Configuración de Base de Datos](#configuración-de-base-de-datos)
4. [Configuración de la Aplicación](#configuración-de-la-aplicación)
5. [Configuración de Servicios](#configuración-de-servicios)
6. [Configuración de Roles y Permisos](#configuración-de-roles-y-permisos)
7. [Despliegue en Producción](#despliegue-en-producción)
8. [Optimizaciones](#optimizaciones)
9. [Mantenimiento](#mantenimiento)
10. [Troubleshooting](#troubleshooting)

---

## 💻 Requisitos del Sistema

### Requisitos Mínimos

#### Servidor Web
- **Sistema Operativo:** Ubuntu 20.04+ / CentOS 8+ / Windows Server 2019+
- **Servidor Web:** Apache 2.4+ o Nginx 1.18+
- **PHP:** 8.2 o superior
- **Memoria RAM:** 2GB mínimo (4GB recomendado)
- **Almacenamiento:** 10GB mínimo (50GB recomendado)
- **CPU:** 2 cores mínimo

#### Base de Datos
- **MySQL:** 8.0+ (recomendado)
- **PostgreSQL:** 13+ (alternativo)
- **MariaDB:** 10.6+ (alternativo)

#### Extensiones PHP Requeridas
```bash
# Verificar extensiones instaladas
php -m

# Extensiones necesarias:
- bcmath
- ctype
- fileinfo
- json
- mbstring
- openssl
- pdo
- pdo_mysql
- tokenizer
- xml
- curl
- zip
- gd
- intl
```

#### Software Adicional
- **Composer:** 2.4+
- **Node.js:** 18+ (para assets frontend)
- **NPM:** 8+
- **Git:** Para control de versiones

---

## 🔧 Instalación Local

### 1. Clonar el Repositorio

```bash
# Clonar desde el repositorio
git clone https://github.com/tu-usuario/gestion-incidencias.git
cd gestion-incidencias

# O descargar desde ZIP y extraer
wget https://github.com/tu-usuario/gestion-incidencias/archive/main.zip
unzip main.zip
cd gestion-incidencias-main
```
### Configurar Variables de Entorno

```bash
# Copiar el archivo de configuración
cp .env.example .env

### 2. Instalar Dependencias PHP

```bash
# Instalar dependencias con Composer
composer install

# Para producción (sin dependencias de desarrollo)
composer install --optimize-autoloader --no-dev
```

### 3. Instalar Dependencias Frontend

```bash
# Instalar dependencias Node.js
npm install

# Compilar assets para desarrollo
npm run dev

# Compilar assets para producción
npm run build
```

# Generar la clave de aplicación
php artisan key:generate
```

### 5. Editar Configuración

Edita el archivo `.env` con la configuración de tu entorno (NO QUITAR variables de REVERB):

```env
# Configuración de la aplicación
APP_NAME="Sistema de Gestión de Incidencias"
APP_ENV=local
APP_KEY=base64:tu-clave-generada
APP_DEBUG=true
APP_URL=http://localhost:8000

# Configuración de base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_incidencias
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

# Configuración de correo
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-password-aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tu-dominio.com
MAIL_FROM_NAME="${APP_NAME}"

# Configuración de colas
QUEUE_CONNECTION=database

# Configuración de sesiones
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Configuración de caché
CACHE_DRIVER=file

# Configuración de logging
LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Configuración de archivos
FILESYSTEM_DISK=local

# Configuración de broadcasting (opcional)
BROADCAST_DRIVER=log
```

---

## 🗄️ Configuración de Base de Datos

### 1. Crear Base de Datos

#### MySQL
```sql
-- Conectar como root
mysql -u root -p

-- Crear base de datos
CREATE DATABASE gestion_incidencias CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario específico
CREATE USER 'gestion_user'@'localhost' IDENTIFIED BY 'password_seguro';
GRANT ALL PRIVILEGES ON gestion_incidencias.* TO 'gestion_user'@'localhost';
FLUSH PRIVILEGES;

-- Verificar conexión
USE gestion_incidencias;
SHOW TABLES;
```

#### PostgreSQL
```sql
-- Conectar como postgres
sudo -u postgres psql

-- Crear base de datos
CREATE DATABASE gestion_incidencias;

-- Crear usuario
CREATE USER gestion_user WITH PASSWORD 'password_seguro';
GRANT ALL PRIVILEGES ON DATABASE gestion_incidencias TO gestion_user;
```

### 2. Ejecutar Migraciones

```bash
# Verificar estado de migraciones
php artisan migrate:status

# Ejecutar migraciones
php artisan migrate

# Ejecutar migraciones con confirmación en producción
php artisan migrate --force
```

### 3. Ejecutar Seeders

```bash
# Ejecutar todos los seeders
php artisan db:seed


# Refrescar base de datos completa (CUIDADO: borra datos)
php artisan migrate:fresh --seed
```

### 4. Verificar Datos Iniciales (OPCIONAL)

```bash
# Verificar usuarios creados
php artisan tinker
>>> App\Models\User::with('roles')->get();

# Verificar áreas
>>> App\Models\Area::all();

# Verificar roles
>>> Spatie\Permission\Models\Role::all();
```

---

## ⚙️ Configuración de la Aplicación

### 1. Configurar Almacenamiento

```bash
# Crear enlaces simbólicos para storage
php artisan storage:link

# Configurar permisos (Linux/Mac)
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

### 2. Configurar Caché

```bash
# Limpiar todas las cachés
php artisan optimize:clear

# Configurar cachés para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Limpiar cachés específicas
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 3. Configurar Colas de Trabajo

```bash
# Configurar las tablas de colas (SI SALE EISTENTE OBVIAR ESTE PASO)
php artisan queue:table
php artisan migrate

# Configurar trabajos fallidos (SI SALE EISTENTE OBVIAR ESTE PASO)
php artisan queue:failed-table
php artisan migrate

# Probar cola
php artisan queue:work

# Para producción, usar supervisor
php artisan queue:work --queue=default --sleep=3 --tries=3 --max-time=3600
```

### 4. Configurar Tareas Programadas

Agregar al crontab del servidor:

```bash
# Editar crontab
crontab -e

# Agregar línea para Laravel Scheduler
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

O crear archivo en `/etc/cron.d/gestion-incidencias`:

```bash
# /etc/cron.d/gestion-incidencias
* * * * * www-data cd /var/www/gestion-incidencias && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔧 Configuración de Servicios

### 1. Configurar Apache

Crear VirtualHost en `/etc/apache2/sites-available/gestion-incidencias.conf`:

```apache
<VirtualHost *:80>
    ServerName gestion-incidencias.local
    DocumentRoot /var/www/gestion-incidencias/public

    <Directory /var/www/gestion-incidencias>
        AllowOverride All
        Require all granted
    </Directory>

    # Configuración de archivos estáticos
    <Directory /var/www/gestion-incidencias/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/gestion-incidencias_error.log
    CustomLog ${APACHE_LOG_DIR}/gestion-incidencias_access.log combined

    # Configuración de seguridad
    <Files ".env">
        Require all denied
    </Files>
</VirtualHost>
```

Habilitar sitio y módulos:

```bash
# Habilitar módulos necesarios
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers

# Habilitar sitio
sudo a2ensite gestion-incidencias.conf
sudo systemctl reload apache2
```

### 2. Configurar Nginx

Crear configuración en `/etc/nginx/sites-available/gestion-incidencias`:

```nginx
server {
    listen 80;
    server_name gestion-incidencias.local;
    root /var/www/gestion-incidencias/public;

    index index.php index.html index.htm;

    # Configuración de archivos estáticos
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Configuración PHP
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    # Seguridad
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ /\.env {
        deny all;
    }

    # Logs
    access_log /var/log/nginx/gestion-incidencias_access.log;
    error_log /var/log/nginx/gestion-incidencias_error.log;

    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
}
```

Habilitar sitio:

```bash
# Crear enlace simbólico
sudo ln -s /etc/nginx/sites-available/gestion-incidencias /etc/nginx/sites-enabled/

# Verificar configuración
sudo nginx -t

# Recargar Nginx
sudo systemctl reload nginx
```

### 3. Configurar SSL con Let's Encrypt

```bash
# Instalar Certbot
sudo apt update
sudo apt install certbot python3-certbot-apache

# Para Apache
sudo certbot --apache -d tu-dominio.com

# Para Nginx
sudo apt install python3-certbot-nginx
sudo certbot --nginx -d tu-dominio.com

# Verificar renovación automática
sudo certbot renew --dry-run
```

---

## 👥 Configuración de Roles y Permisos

### 1. Verificar Roles Creados

```bash
php artisan tinker
>>> Spatie\Permission\Models\Role::all();
```

### 2. Crear Usuario Super Admin

```bash
php artisan tinker

# Crear usuario Super Admin
>>> $user = App\Models\User::create([
...     'name' => 'Super Administrador',
...     'email' => 'admin@tudominio.com',
...     'password' => Hash::make('password_seguro'),
...     'area_id' => 1,
...     'activo' => true
... ]);

# Asignar rol
>>> $user->assignRole('Super Admin');
```

### 3. Configurar Permisos Personalizados

```bash
# En database/seeders/RoleSeeder.php
# Ya están configurados los roles básicos:
# - Super Admin (todos los permisos)
# - Admin (gestión de su área)
# - Tecnico (tickets asignados)
# - Usuario (creación de tickets)
```

---

## 🚀 Despliegue en Producción

### 1. Configuración del Servidor

#### Ubuntu 20.04/22.04

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependencias
sudo apt install -y apache2 mysql-server php8.2 php8.2-fpm \
  php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-gd \
  php8.2-mbstring php8.2-bcmath php8.2-intl composer git nodejs npm

# Configurar MySQL
sudo mysql_secure_installation

# Configurar PHP
sudo nano /etc/php/8.2/apache2/php.ini
# Configurar:
# memory_limit = 256M
# upload_max_filesize = 10M
# post_max_size = 10M
# max_execution_time = 300
```

### 2. Configuración de Producción

Editar `.env` para producción:

```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning

# Configurar base de datos de producción
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=gestion_incidencias_prod
DB_USERNAME=gestion_prod
DB_PASSWORD=password_muy_seguro

# Configurar correo de producción
MAIL_MAILER=smtp
MAIL_HOST=smtp.tu-empresa.com
MAIL_PORT=587
MAIL_USERNAME=sistema@tu-empresa.com
MAIL_PASSWORD=password_correo

# Configurar colas para producción
QUEUE_CONNECTION=database

# Configurar caché para producción
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### 3. Optimizar para Producción

```bash
# Instalar dependencias optimizadas
composer install --optimize-autoloader --no-dev

# Compilar assets optimizados
npm run build

# Optimizar aplicación
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Configurar permisos finales
sudo chown -R www-data:www-data /var/www/gestion-incidencias
sudo chmod -R 755 /var/www/gestion-incidencias
sudo chmod -R 775 /var/www/gestion-incidencias/storage
sudo chmod -R 775 /var/www/gestion-incidencias/bootstrap/cache
```

### 4. Configurar Supervisor para Colas

Crear archivo `/etc/supervisor/conf.d/gestion-incidencias-worker.conf`:

```ini
[program:gestion-incidencias-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/gestion-incidencias/artisan queue:work --queue=default --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/gestion-incidencias/storage/logs/worker.log
stopwaitsecs=3600
```

Activar supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start gestion-incidencias-worker:*
```

### 5. Configurar Backup Automático

Crear script de backup `/opt/scripts/backup-gestion.sh`:

```bash
#!/bin/bash

# Variables
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/gestion-incidencias"
PROJECT_DIR="/var/www/gestion-incidencias"
DB_NAME="gestion_incidencias_prod"
DB_USER="gestion_prod"
DB_PASS="password_muy_seguro"

# Crear directorio de backup
mkdir -p $BACKUP_DIR

# Backup de base de datos
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Backup de archivos
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz $PROJECT_DIR/storage/app/public

# Limpiar backups antiguos (mantener 7 días)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completado: $DATE"
```

Agregar al crontab:

```bash
# Backup diario a las 2:00 AM
0 2 * * * /opt/scripts/backup-gestion.sh >> /var/log/backup-gestion.log 2>&1
```

---

## 🔧 Optimizacion

### 1. Configurar Redis (Opcional)

```bash
# Instalar Redis
sudo apt install redis-server

# Configurar Laravel para usar Redis
# En .env:
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Configurar Redis en config/database.php si es necesario
```

### 2. Configurar OPcache

En `/etc/php/8.2/apache2/php.ini`:

```ini
# Habilitar OPcache
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=12
opcache.max_accelerated_files=4000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=0
```

### 3. Configurar Gzip

En Apache (`/etc/apache2/mods-enabled/deflate.conf`):

```apache
<IfModule mod_deflate.c>
    <IfModule mod_filter.c>
        AddOutputFilterByType DEFLATE text/plain
        AddOutputFilterByType DEFLATE text/html
        AddOutputFilterByType DEFLATE text/xml
        AddOutputFilterByType DEFLATE text/css
        AddOutputFilterByType DEFLATE application/xml
        AddOutputFilterByType DEFLATE application/xhtml+xml
        AddOutputFilterByType DEFLATE application/rss+xml
        AddOutputFilterByType DEFLATE application/javascript
        AddOutputFilterByType DEFLATE application/x-javascript
    </IfModule>
</IfModule>
```

---

## 🔧 Mantenimiento

### 1. Actualizaciones

```bash
# Backup antes de actualizar
sudo cp -r /var/www/gestion-incidencias /backup/gestion-incidencias-$(date +%Y%m%d)

# Actualizar código
cd /var/www/gestion-incidencias
git pull origin main

# Actualizar dependencias
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Limpiar cachés
php artisan optimize:clear
php artisan optimize
```

### 2. Monitoreo

```bash
# Verificar estado de la aplicación
php artisan about

# Verificar colas
php artisan queue:monitor

# Verificar logs
tail -f storage/logs/laravel.log

# Verificar trabajos fallidos
php artisan queue:failed
```

### 3. Limpieza Regular

```bash
# Limpiar logs antiguos
find storage/logs -name "*.log" -mtime +30 -delete

# Limpiar trabajos fallidos antiguos
php artisan queue:prune-failed --hours=72

# Limpiar sesiones expiradas
php artisan session:gc

# Optimizar base de datos
php artisan db:optimize
```

---

## 🔍 Troubleshooting

### 1. Problemas Comunes

#### Error 500 - Internal Server Error

```bash
# Verificar permisos
sudo chown -R www-data:www-data /var/www/gestion-incidencias
sudo chmod -R 755 /var/www/gestion-incidencias

# Verificar logs
tail -f /var/log/apache2/error.log
tail -f storage/logs/laravel.log

# Limpiar cachés
php artisan optimize:clear
```

#### Error de Base de Datos

```bash
# Verificar conexión
php artisan tinker
>>> DB::connection()->getPdo();

# Verificar configuración
php artisan config:show database

# Probar migraciones
php artisan migrate:status
```

#### Problemas con Colas

```bash
# Verificar estado de trabajos
php artisan queue:work --once

# Revisar trabajos fallidos
php artisan queue:failed

# Reiniciar supervisor
sudo supervisorctl restart gestion-incidencias-worker:*
```

### 2. Herramientas de Diagnóstico

```bash
# Información general de Laravel
php artisan about

# Verificar configuración
php artisan config:show

# Verificar rutas
php artisan route:list

# Verificar proveedores de servicios
php artisan make:command DiagnoseSystem
```

### 3. Logs Importantes

```bash
# Logs de Laravel
tail -f storage/logs/laravel.log

# Logs de Apache
tail -f /var/log/apache2/error.log
tail -f /var/log/apache2/access.log

# Logs de Nginx
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# Logs de MySQL
tail -f /var/log/mysql/error.log

# Logs del sistema
tail -f /var/log/syslog
```

---

## 📞 Soporte

### Contacto
- **Email:** locomancocapac@gmail.com
- **Teléfono:** +51 927885314

### Recursos Adicionales
- **Repositorio:** https://github.com/joscar03-dev/gestion-incidencias-mpb.git

---

Esta guía cubre la instalación completa del sistema. Para configuraciones específicas o problemas no cubiertos, consulta la documentación oficial de Laravel o contacta al equipo de soporte.
