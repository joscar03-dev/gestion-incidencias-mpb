/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `areas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `local_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `areas_parent_id_foreign` (`parent_id`),
  KEY `areas_local_id_foreign` (`local_id`),
  CONSTRAINT `areas_local_id_foreign` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `areas_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categoria_dispositivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria_dispositivos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categoria_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria_ticket` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `categoria_id` bigint unsigned NOT NULL,
  `ticket_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_ticket_categoria_id_foreign` (`categoria_id`),
  KEY `categoria_ticket_ticket_id_foreign` (`ticket_id`),
  CONSTRAINT `categoria_ticket_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `categoria_ticket_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tipo_categoria` enum('incidente','solicitud_servicio','cambio','problema','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `itil_category` tinyint(1) NOT NULL DEFAULT '0',
  `prioridad_default` enum('baja','media','alta','critica') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'media',
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6B7280',
  `icono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorias_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `comment_reactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comment_reactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint unsigned NOT NULL,
  `reactor_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reactor_id` bigint unsigned NOT NULL,
  `reaction` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comment_reactions_comment_id_foreign` (`comment_id`),
  KEY `comment_reactions_reactor_type_reactor_id_index` (`reactor_type`,`reactor_id`),
  CONSTRAINT `comment_reactions_comment_id_foreign` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `author_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_id` bigint unsigned NOT NULL,
  `commentable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentable_id` bigint unsigned NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_author_type_author_id_index` (`author_type`,`author_id`),
  KEY `comments_commentable_type_commentable_id_index` (`commentable_type`,`commentable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dispositivo_asignacions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispositivo_asignacions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dispositivo_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `fecha_asignacion` timestamp NOT NULL,
  `fecha_desasignacion` timestamp NULL DEFAULT NULL,
  `asignado_por` bigint unsigned DEFAULT NULL,
  `confirmado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_confirmacion` datetime DEFAULT NULL,
  `motivo_asignacion` text COLLATE utf8mb4_unicode_ci,
  `motivo_desasignacion` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `solicitud_dispositivo_id` bigint unsigned DEFAULT NULL,
  `solicitud_transferencia_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispositivo_asignacions_dispositivo_id_foreign` (`dispositivo_id`),
  KEY `dispositivo_asignacions_user_id_foreign` (`user_id`),
  KEY `dispositivo_asignacions_confirmado_index` (`confirmado`),
  KEY `dispositivo_asignacions_fecha_confirmacion_index` (`fecha_confirmacion`),
  KEY `dispositivo_asignacions_solicitud_dispositivo_id_index` (`solicitud_dispositivo_id`),
  KEY `dispositivo_asignacions_solicitud_transferencia_id_index` (`solicitud_transferencia_id`),
  KEY `dispositivo_asignacions_asignado_por_foreign` (`asignado_por`),
  CONSTRAINT `dispositivo_asignacions_asignado_por_foreign` FOREIGN KEY (`asignado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispositivo_asignacions_dispositivo_id_foreign` FOREIGN KEY (`dispositivo_id`) REFERENCES `dispositivos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dispositivo_asignacions_solicitud_dispositivo_id_foreign` FOREIGN KEY (`solicitud_dispositivo_id`) REFERENCES `solicitud_dispositivos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispositivo_asignacions_solicitud_transferencia_id_foreign` FOREIGN KEY (`solicitud_transferencia_id`) REFERENCES `solicitud_transferencias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispositivo_asignacions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dispositivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispositivos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria_id` bigint unsigned NOT NULL,
  `numero_serie` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_activo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etiqueta_inventario` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  `area_id` bigint unsigned DEFAULT NULL,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  `costo_adquisicion` decimal(12,2) DEFAULT NULL,
  `moneda` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `proveedor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_garantia` date DEFAULT NULL,
  `tipo_garantia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_instalacion` date DEFAULT NULL,
  `vida_util_anos` int DEFAULT NULL,
  `especificaciones_tecnicas` json DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_conexion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `accesorios_incluidos` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dispositivos_numero_serie_unique` (`numero_serie`),
  UNIQUE KEY `dispositivos_codigo_activo_unique` (`codigo_activo`),
  KEY `dispositivos_categoria_id_foreign` (`categoria_id`),
  KEY `dispositivos_area_id_foreign` (`area_id`),
  KEY `dispositivos_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `dispositivos_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispositivos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categoria_dispositivos` (`id`),
  CONSTRAINT `dispositivos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `locales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sla_prioridad_factores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sla_prioridad_factores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `factor` decimal(4,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `orden` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sla_prioridad_factores_codigo_unique` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sla_tipo_factores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sla_tipo_factores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `factor` decimal(4,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `orden` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sla_tipo_factores_codigo_unique` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `slas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `area_id` bigint unsigned NOT NULL,
  `nivel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tiempo_respuesta` int NOT NULL,
  `tiempo_resolucion` int NOT NULL,
  `canal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `prioridad_ticket` enum('critico','alto','medio','bajo') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `override_area` tinyint(1) NOT NULL DEFAULT '0',
  `escalamiento_automatico` tinyint(1) NOT NULL DEFAULT '0',
  `tiempo_escalamiento` int DEFAULT NULL COMMENT 'Tiempo en minutos para escalamiento',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slas_area_id_unique` (`area_id`),
  KEY `slas_activo_prioridad_ticket_index` (`activo`,`prioridad_ticket`),
  CONSTRAINT `slas_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `solicitud_dispositivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitud_dispositivos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `categoria_dispositivo_id` bigint unsigned NOT NULL,
  `justificacion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento_requerimiento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prioridad` enum('Baja','Media','Alta','Critica') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Media',
  `estado` enum('Pendiente','Aprobado','Rechazado','Completado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `fecha_solicitud` datetime NOT NULL,
  `fecha_respuesta` datetime DEFAULT NULL,
  `fecha_aprobacion` timestamp NULL DEFAULT NULL,
  `fecha_rechazo` timestamp NULL DEFAULT NULL,
  `observaciones_admin` text COLLATE utf8mb4_unicode_ci,
  `admin_respuesta_id` bigint unsigned DEFAULT NULL,
  `dispositivo_asignado_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ticket_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `solicitud_dispositivos_admin_respuesta_id_foreign` (`admin_respuesta_id`),
  KEY `solicitud_dispositivos_dispositivo_asignado_id_foreign` (`dispositivo_asignado_id`),
  KEY `solicitud_dispositivos_user_id_estado_index` (`user_id`,`estado`),
  KEY `solicitud_dispositivos_categoria_dispositivo_id_estado_index` (`categoria_dispositivo_id`,`estado`),
  KEY `solicitud_dispositivos_fecha_solicitud_index` (`fecha_solicitud`),
  KEY `solicitud_dispositivos_prioridad_estado_index` (`prioridad`,`estado`),
  KEY `solicitud_dispositivos_documento_requerimiento_index` (`documento_requerimiento`),
  KEY `solicitud_dispositivos_ticket_id_foreign` (`ticket_id`),
  CONSTRAINT `solicitud_dispositivos_admin_respuesta_id_foreign` FOREIGN KEY (`admin_respuesta_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `solicitud_dispositivos_categoria_dispositivo_id_foreign` FOREIGN KEY (`categoria_dispositivo_id`) REFERENCES `categoria_dispositivos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitud_dispositivos_dispositivo_asignado_id_foreign` FOREIGN KEY (`dispositivo_asignado_id`) REFERENCES `dispositivos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `solicitud_dispositivos_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `solicitud_dispositivos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `solicitud_transferencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitud_transferencias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dispositivo_id` bigint unsigned NOT NULL,
  `usuario_origen_id` bigint unsigned NOT NULL,
  `usuario_destino_id` bigint unsigned NOT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Pendiente','Aprobada','Rechazada','Ejecutada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `fecha_solicitud` datetime NOT NULL,
  `fecha_respuesta` datetime DEFAULT NULL,
  `fecha_ejecucion` datetime DEFAULT NULL,
  `observaciones_admin` text COLLATE utf8mb4_unicode_ci,
  `admin_respuesta_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `solicitud_transferencias_admin_respuesta_id_foreign` (`admin_respuesta_id`),
  KEY `solicitud_transferencias_dispositivo_id_estado_index` (`dispositivo_id`,`estado`),
  KEY `solicitud_transferencias_usuario_origen_id_estado_index` (`usuario_origen_id`,`estado`),
  KEY `solicitud_transferencias_usuario_destino_id_estado_index` (`usuario_destino_id`,`estado`),
  KEY `solicitud_transferencias_fecha_solicitud_index` (`fecha_solicitud`),
  KEY `solicitud_transferencias_estado_index` (`estado`),
  CONSTRAINT `solicitud_transferencias_admin_respuesta_id_foreign` FOREIGN KEY (`admin_respuesta_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `solicitud_transferencias_dispositivo_id_foreign` FOREIGN KEY (`dispositivo_id`) REFERENCES `dispositivos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitud_transferencias_usuario_destino_id_foreign` FOREIGN KEY (`usuario_destino_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitud_transferencias_usuario_origen_id_foreign` FOREIGN KEY (`usuario_origen_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_satisfactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_satisfactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `rating` tinyint NOT NULL COMMENT '1-5: Muy malo a Excelente',
  `time_satisfaction` enum('muy_rapido','adecuado','regular','muy_lento') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Satisfacción con tiempo de resolución',
  `comments` text COLLATE utf8mb4_unicode_ci COMMENT 'Comentarios opcionales',
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_satisfactions_ticket_id_user_id_unique` (`ticket_id`,`user_id`),
  KEY `ticket_satisfactions_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_satisfactions_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_satisfactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `creado_por` bigint unsigned NOT NULL,
  `area_id` bigint unsigned DEFAULT NULL,
  `asignado_a` bigint unsigned DEFAULT NULL,
  `asignado_por` bigint unsigned DEFAULT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prioridad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `tiempo_respuesta` time DEFAULT NULL,
  `tiempo_solucion` time DEFAULT NULL,
  `comentario` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fecha_cierre` timestamp NULL DEFAULT NULL,
  `fecha_resolucion` timestamp NULL DEFAULT NULL,
  `comentarios_resolucion` text COLLATE utf8mb4_unicode_ci,
  `escalado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_escalamiento` timestamp NULL DEFAULT NULL,
  `sla_vencido` tinyint(1) NOT NULL DEFAULT '0',
  `dispositivo_id` bigint unsigned DEFAULT NULL,
  `requiere_reemplazo` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tickets_asignado_a_foreign` (`asignado_a`),
  KEY `tickets_asignado_por_foreign` (`asignado_por`),
  KEY `tickets_creado_por_foreign` (`creado_por`),
  KEY `tickets_area_id_foreign` (`area_id`),
  KEY `tickets_estado_prioridad_index` (`estado`,`prioridad`),
  KEY `tickets_escalado_sla_vencido_index` (`escalado`,`sla_vencido`),
  KEY `tickets_created_at_estado_index` (`created_at`,`estado`),
  KEY `tickets_dispositivo_id_index` (`dispositivo_id`),
  KEY `tickets_dispositivo_id_estado_index` (`dispositivo_id`,`estado`),
  KEY `tickets_requiere_reemplazo_index` (`requiere_reemplazo`),
  CONSTRAINT `tickets_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_asignado_a_foreign` FOREIGN KEY (`asignado_a`) REFERENCES `users` (`id`),
  CONSTRAINT `tickets_asignado_por_foreign` FOREIGN KEY (`asignado_por`) REFERENCES `users` (`id`),
  CONSTRAINT `tickets_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_dispositivo_id_foreign` FOREIGN KEY (`dispositivo_id`) REFERENCES `dispositivos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `area_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_area_id_foreign` (`area_id`),
  CONSTRAINT `users_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_100000_create_password_reset_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_03_19_220326_create_categorias_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_03_19_224413_create_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_03_19_232503_create_table_create_categoria_ticket',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_03_20_224642_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_05_14_155121_agregar_columna_atachment_en_la_tabla_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_06_12_172319_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_06_21_225719_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_06_25_120046_agregar_campo_creador_por',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_06_25_121711_agregar_null_campo_asignado_por',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_06_27_004749_create_commentions_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_06_27_004750_create_commentions_reactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_06_28_112924_create_areas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_06_29_134847_create_locales_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_06_29_224945_create_categoria_dispositivos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_06_29_225043_create_dispositivos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_07_01_095338_create_slas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_07_01_104251_agregar_campos_solucion_y_respuesta_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_07_01_115704_agregar_campos_fecha_cierre_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_07_05_105551_create_dispositivo_asignacions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_07_09_000002_add_sla_fields_to_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_07_09_000003_make_assignment_fields_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_07_09_000003_update_sla_fields_to_integer',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_07_09_000005_make_asignado_a_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_07_09_101055_add_escalamiento_fields_to_slas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_07_09_150143_add_unique_area_id_to_slas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_07_10_125048_add_cancelado_fields_to_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_07_11_100834_create_solicitud_dispositivos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_07_11_100848_create_solicitud_transferencias_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_07_11_100955_add_tracking_fields_to_dispositivo_asignacions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_07_11_101027_add_dispositivo_id_to_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_07_11_111843_add_documento_field_to_solicitud_dispositivos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_07_11_114401_add_asignado_por_to_dispositivo_asignacions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_07_11_114602_add_fecha_aprobacion_rechazo_to_solicitud_dispositivos',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_07_11_120539_fix_estado_enum_solicitud_dispositivos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_07_11_122319_add_imagen_to_dispositivos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_07_11_132952_remove_tipo_ticket_from_slas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_07_11_133759_add_tipo_to_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_07_11_171335_add_ticket_id_to_solicitud_dispositivos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_07_11_200507_add_itil_fields_to_categorias_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_07_14_195528_add_simplified_fields_to_dispositivos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2025_07_21_193329_add_critica_to_prioridad_enum_in_solicitud_dispositivos',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_08_29_110635_create_sla_prioridad_factores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_08_29_110645_create_sla_tipo_factores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2025_09_03_225920_remove_sla_horas_from_categorias_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_09_04_133341_create_ticket_satisfactions_table',1);
