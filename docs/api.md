# 🌐 Documentación de la API REST

Esta documentación detalla todas las rutas y endpoints disponibles en el sistema de gestión de incidencias, incluyendo autenticación, métodos de acceso y ejemplos de uso.

## 📋 Índice

1. [Información General](#información-general)
2. [Autenticación](#autenticación)
3. [Endpoints de Tickets](#endpoints-de-tickets)
4. [Endpoints de Usuarios](#endpoints-de-usuarios)
5. [Endpoints de Áreas](#endpoints-de-áreas)
6. [Endpoints de Reportes](#endpoints-de-reportes)
7. [Códigos de Respuesta](#códigos-de-respuesta)
8. [Ejemplos de Uso](#ejemplos-de-uso)

---

## ℹ️ Información General

### Base URL
```
https://tu-dominio.com/api
```

### Versión
```
v1.0
```

### Formato de Respuesta
Todas las respuestas de la API están en formato JSON.

### Headers Requeridos
```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
```

---

## 🔐 Autenticación

### Obtener Token de Acceso

**Endpoint:** `POST /auth/login`

**Descripción:** Autentica un usuario y devuelve un token de acceso.

**Parámetros:**
```json
{
    "email": "usuario@ejemplo.com",
    "password": "contraseña"
}
```

**Respuesta Exitosa:**
```json
{
    "status": "success",
    "message": "Login exitoso",
    "data": {
        "user": {
            "id": 1,
            "name": "Nombre Usuario",
            "email": "usuario@ejemplo.com",
            "area": {
                "id": 1,
                "nombre": "Sistemas"
            },
            "roles": [
                {
                    "id": 1,
                    "name": "Admin"
                }
            ]
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "expires_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

### Cerrar Sesión

**Endpoint:** `POST /auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Respuesta:**
```json
{
    "status": "success",
    "message": "Logout exitoso"
}
```

### Renovar Token

**Endpoint:** `POST /auth/refresh`

**Headers:** `Authorization: Bearer {token}`

**Respuesta:**
```json
{
    "status": "success",
    "data": {
        "token": "nuevo_token_jwt",
        "expires_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

---

## 🎫 Endpoints de Tickets

### Listar Tickets

**Endpoint:** `GET /tickets`

**Parámetros de Query:**
- `page` (int): Número de página (por defecto: 1)
- `per_page` (int): Elementos por página (por defecto: 15)
- `estado` (string): Filtrar por estado (Abierto, En Progreso, Cerrado, etc.)
- `prioridad` (string): Filtrar por prioridad (Critica, Alta, Media, Baja)
- `area_id` (int): Filtrar por área
- `asignado_a` (int): Filtrar por usuario asignado
- `escalado` (boolean): Filtrar por tickets escalados
- `search` (string): Búsqueda en título y descripción

**Ejemplo:**
```
GET /tickets?estado=Abierto&prioridad=Alta&page=1&per_page=10
```

**Respuesta:**
```json
{
    "status": "success",
    "data": {
        "tickets": [
            {
                "id": 1,
                "titulo": "Error en sistema de facturación",
                "descripcion": "El sistema no permite generar facturas",
                "estado": "Abierto",
                "prioridad": "Alta",
                "escalado": false,
                "sla_vencido": false,
                "area": {
                    "id": 1,
                    "nombre": "Facturación"
                },
                "creado_por": {
                    "id": 2,
                    "name": "Juan Pérez"
                },
                "asignado_a": {
                    "id": 3,
                    "name": "María González"
                },
                "tiempo_restante_sla": "120",
                "estado_sla": "ok",
                "attachment": "storage/tickets/documento.pdf",
                "created_at": "2024-01-10T08:30:00.000000Z",
                "updated_at": "2024-01-10T09:15:00.000000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 15,
            "total": 67,
            "from": 1,
            "to": 15
        }
    }
}
```

### Obtener Ticket Específico

**Endpoint:** `GET /tickets/{id}`

**Respuesta:**
```json
{
    "status": "success",
    "data": {
        "ticket": {
            "id": 1,
            "titulo": "Error en sistema de facturación",
            "descripcion": "El sistema no permite generar facturas",
            "comentario": null,
            "estado": "Abierto",
            "prioridad": "Alta",
            "escalado": false,
            "fecha_escalamiento": null,
            "sla_vencido": false,
            "area_id": 1,
            "creado_por": 2,
            "asignado_a": 3,
            "attachment": "storage/tickets/documento.pdf",
            "area": {
                "id": 1,
                "nombre": "Facturación",
                "sla": {
                    "tiempo_respuesta_alto": 60,
                    "tiempo_resolucion_alto": 240
                }
            },
            "creado_por_usuario": {
                "id": 2,
                "name": "Juan Pérez",
                "email": "juan@ejemplo.com"
            },
            "asignado_a_usuario": {
                "id": 3,
                "name": "María González",
                "email": "maria@ejemplo.com"
            },
            "comentarios": [
                {
                    "id": 1,
                    "comment": "Revisando el problema",
                    "created_at": "2024-01-10T09:00:00.000000Z",
                    "commentator": {
                        "id": 3,
                        "name": "María González"
                    }
                }
            ],
            "created_at": "2024-01-10T08:30:00.000000Z",
            "updated_at": "2024-01-10T09:15:00.000000Z"
        }
    }
}
```

### Crear Ticket

**Endpoint:** `POST /tickets`

**Parámetros:**
```json
{
    "titulo": "Título del ticket",
    "descripcion": "Descripción detallada del problema",
    "prioridad": "Alta",
    "attachment": "archivo_opcional"
}
```

**Respuesta:**
```json
{
    "status": "success",
    "message": "Ticket creado exitosamente",
    "data": {
        "ticket": {
            "id": 15,
            "titulo": "Título del ticket",
            "descripcion": "Descripción detallada del problema",
            "estado": "Abierto",
            "prioridad": "Alta",
            "area_id": 1,
            "creado_por": 2,
            "created_at": "2024-01-10T10:30:00.000000Z"
        }
    }
}
```

### Actualizar Ticket

**Endpoint:** `PUT /tickets/{id}`

**Parámetros:**
```json
{
    "titulo": "Nuevo título",
    "descripcion": "Nueva descripción",
    "estado": "En Progreso",
    "prioridad": "Media",
    "asignado_a": 4,
    "comentario": "Comentario de actualización"
}
```

### Escalar Ticket

**Endpoint:** `POST /tickets/{id}/escalar`

**Parámetros:**
```json
{
    "motivo": "SLA vencido - escalamiento automático"
}
```

**Respuesta:**
```json
{
    "status": "success",
    "message": "Ticket escalado exitosamente",
    "data": {
        "ticket": {
            "id": 1,
            "escalado": true,
            "fecha_escalamiento": "2024-01-10T11:00:00.000000Z"
        }
    }
}
```

### Cerrar Ticket

**Endpoint:** `POST /tickets/{id}/cerrar`

**Parámetros:**
```json
{
    "comentario": "Problema resuelto. Se reinició el servicio."
}
```

### Verificar SLA de Ticket

**Endpoint:** `POST /tickets/{id}/verificar-sla`

**Respuesta:**
```json
{
    "status": "success",
    "data": {
        "ticket_id": 1,
        "estado_sla": "advertencia",
        "tiempo_restante_respuesta": 30,
        "tiempo_restante_resolucion": 180,
        "escalado": false
    }
}
```

### Exportar Tickets

**Endpoint:** `GET /tickets/export`

**Parámetros de Query:**
- `format` (string): excel|pdf
- `estado` (string): Filtro opcional
- `prioridad` (string): Filtro opcional
- `area_id` (int): Filtro opcional
- `fecha_inicio` (date): Fecha de inicio (YYYY-MM-DD)
- `fecha_fin` (date): Fecha de fin (YYYY-MM-DD)

**Ejemplo:**
```
GET /tickets/export?format=excel&estado=Abierto&fecha_inicio=2024-01-01
```

**Respuesta:** Archivo descargable (Excel o PDF)

---

## 👥 Endpoints de Usuarios

### Listar Usuarios

**Endpoint:** `GET /users`

**Parámetros de Query:**
- `page` (int): Número de página
- `per_page` (int): Elementos por página
- `role` (string): Filtrar por rol
- `area_id` (int): Filtrar por área
- `activo` (boolean): Filtrar por estado

**Respuesta:**
```json
{
    "status": "success",
    "data": {
        "users": [
            {
                "id": 1,
                "name": "Admin Principal",
                "email": "admin@ejemplo.com",
                "activo": true,
                "area": {
                    "id": 1,
                    "nombre": "Sistemas"
                },
                "roles": [
                    {
                        "id": 1,
                        "name": "Super Admin"
                    }
                ],
                "tickets_asignados": 5,
                "tickets_creados": 12,
                "created_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total": 25
        }
    }
}
```

### Obtener Usuario Específico

**Endpoint:** `GET /users/{id}`

### Crear Usuario

**Endpoint:** `POST /users`

**Parámetros:**
```json
{
    "name": "Nuevo Usuario",
    "email": "usuario@ejemplo.com",
    "password": "contraseña123",
    "area_id": 1,
    "roles": [2, 3],
    "activo": true
}
```

### Actualizar Usuario

**Endpoint:** `PUT /users/{id}`

### Desactivar Usuario

**Endpoint:** `POST /users/{id}/desactivar`

---

## 🏢 Endpoints de Áreas

### Listar Áreas

**Endpoint:** `GET /areas`

**Respuesta:**
```json
{
    "status": "success",
    "data": {
        "areas": [
            {
                "id": 1,
                "nombre": "Sistemas",
                "descripcion": "Área de tecnología e informática",
                "activo": true,
                "usuarios_count": 5,
                "tickets_count": 23,
                "sla": {
                    "id": 1,
                    "nombre": "SLA Sistemas",
                    "tiempo_respuesta_critico": 15,
                    "tiempo_respuesta_alto": 60
                },
                "created_at": "2024-01-01T00:00:00.000000Z"
            }
        ]
    }
}
```

### Obtener Área Específica

**Endpoint:** `GET /areas/{id}`

### Crear Área

**Endpoint:** `POST /areas`

**Parámetros:**
```json
{
    "nombre": "Nueva Área",
    "descripcion": "Descripción del área",
    "activo": true
}
```

### Estadísticas de Área

**Endpoint:** `GET /areas/{id}/estadisticas`

**Respuesta:**
```json
{
    "status": "success",
    "data": {
        "area": {
            "id": 1,
            "nombre": "Sistemas"
        },
        "estadisticas": {
            "total_tickets": 45,
            "tickets_abiertos": 12,
            "tickets_cerrados": 30,
            "tickets_escalados": 3,
            "promedio_resolucion_horas": 4.5,
            "sla_cumplimiento": 85.5,
            "distribucion_prioridad": {
                "Critica": 2,
                "Alta": 8,
                "Media": 25,
                "Baja": 10
            }
        }
    }
}
```

---

## 📊 Endpoints de Reportes

### Dashboard General

**Endpoint:** `GET /dashboard`

**Respuesta:**
```json
{
    "status": "success",
    "data": {
        "resumen": {
            "total_tickets": 234,
            "tickets_abiertos": 45,
            "tickets_en_progreso": 23,
            "tickets_cerrados": 166,
            "tickets_escalados": 8,
            "sla_vencidos": 12
        },
        "tickets_por_area": [
            {
                "area": "Sistemas",
                "total": 89,
                "abiertos": 15
            }
        ],
        "tickets_por_prioridad": {
            "Critica": 5,
            "Alta": 18,
            "Media": 45,
            "Baja": 166
        },
        "tendencia_semanal": [
            {
                "fecha": "2024-01-08",
                "creados": 12,
                "cerrados": 8
            }
        ]
    }
}
```

### Reporte de SLA

**Endpoint:** `GET /reportes/sla`

**Parámetros de Query:**
- `fecha_inicio` (date): Fecha de inicio
- `fecha_fin` (date): Fecha de fin
- `area_id` (int): Filtrar por área

**Respuesta:**
```json
{
    "status": "success",
    "data": {
        "periodo": {
            "inicio": "2024-01-01",
            "fin": "2024-01-31"
        },
        "resumen_general": {
            "tickets_analizados": 156,
            "sla_cumplido": 142,
            "sla_incumplido": 14,
            "porcentaje_cumplimiento": 91.02
        },
        "por_area": [
            {
                "area": "Sistemas",
                "tickets": 45,
                "cumplidos": 40,
                "incumplidos": 5,
                "porcentaje": 88.89
            }
        ],
        "por_prioridad": {
            "Critica": {
                "cumplidos": 8,
                "incumplidos": 2,
                "porcentaje": 80.0
            }
        }
    }
}
```

### Reporte de Productividad

**Endpoint:** `GET /reportes/productividad`

**Parámetros de Query:**
- `usuario_id` (int): Filtrar por usuario
- `area_id` (int): Filtrar por área
- `fecha_inicio` (date): Fecha de inicio
- `fecha_fin` (date): Fecha de fin

---

## 📋 Códigos de Respuesta

### Códigos de Éxito
- `200 OK` - Solicitud exitosa
- `201 Created` - Recurso creado exitosamente
- `204 No Content` - Solicitud exitosa sin contenido

### Códigos de Error
- `400 Bad Request` - Solicitud malformada o parámetros inválidos
- `401 Unauthorized` - Token inválido o expirado
- `403 Forbidden` - Sin permisos suficientes
- `404 Not Found` - Recurso no encontrado
- `422 Unprocessable Entity` - Errores de validación
- `429 Too Many Requests` - Límite de solicitudes excedido
- `500 Internal Server Error` - Error del servidor

### Formato de Error
```json
{
    "status": "error",
    "message": "Descripción del error",
    "errors": {
        "campo": [
            "Error específico del campo"
        ]
    },
    "code": "VALIDATION_ERROR"
}
```

---

## 🔧 Ejemplos de Uso

### Crear un Ticket (JavaScript)

```javascript
const crearTicket = async () => {
    const response = await fetch('/api/tickets', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            titulo: 'Error en sistema de inventario',
            descripcion: 'El sistema no actualiza el stock correctamente',
            prioridad: 'Alta'
        })
    });

    const data = await response.json();
    
    if (response.ok) {
        console.log('Ticket creado:', data.data.ticket);
    } else {
        console.error('Error:', data.message);
    }
};
```

### Listar Tickets con Filtros (PHP)

```php
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => 'https://tu-dominio.com/api/tickets?estado=Abierto&prioridad=Alta',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'Content-Type: application/json'
    ],
]);

$response = curl_exec($curl);
$data = json_decode($response, true);

if ($data['status'] === 'success') {
    foreach ($data['data']['tickets'] as $ticket) {
        echo "Ticket #{$ticket['id']}: {$ticket['titulo']}\n";
    }
}

curl_close($curl);
```

### Actualizar Ticket (Python)

```python
import requests

url = "https://tu-dominio.com/api/tickets/1"
headers = {
    "Authorization": f"Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json"
}

data = {
    "estado": "En Progreso",
    "comentario": "Iniciando revisión del problema"
}

response = requests.put(url, json=data, headers=headers)

if response.status_code == 200:
    ticket = response.json()['data']['ticket']
    print(f"Ticket actualizado: {ticket['titulo']}")
else:
    print(f"Error: {response.json()['message']}")
```

---

## 🔒 Seguridad y Limitaciones

### Rate Limiting
- **Autenticación:** 5 intentos por minuto por IP
- **API General:** 60 solicitudes por minuto por usuario autenticado
- **Exportaciones:** 10 solicitudes por hora por usuario

### Validaciones
- Todos los campos requeridos son validados
- Los archivos adjuntos tienen límite de 1MB
- Formatos permitidos: PDF, JPG, PNG, DOC, DOCX

### Permisos por Rol
- **Super Admin:** Acceso completo a todos los endpoints
- **Admin:** Gestión dentro de su área asignada
- **Técnico:** Solo tickets asignados y comentarios
- **Usuario:** Solo creación y visualización de tickets propios

---

Esta documentación cubre todos los endpoints disponibles en la API REST del sistema de gestión de incidencias. Para mayor información sobre autenticación o casos de uso específicos, consulta con el equipo de desarrollo.
