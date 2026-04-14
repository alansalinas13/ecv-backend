# 🏥 Plataforma ECV — Backend API

Sistema backend para gestión de enfermedades cardiovasculares (ECV), construido con Laravel moderno, arquitectura REST y PostgreSQL.

---

# 📌 Descripción

Este sistema permite:

* Autenticación de usuarios (Sanctum)
* Control de roles (admin, doctor, user)
* Gestión de doctores
* Sistema de citas médicas
* Foro (posts y comentarios)
* Autoevaluación cardiovascular
* Gestión de hospitales (para mapa)

---

# 🧱 Tecnologías

* Laravel (última versión estable)
* PHP >= 8.3
* PostgreSQL
* Laravel Sanctum
* API REST

---

# 🚀 Instalación

## 1. Clonar repositorio

```bash
git clone <repo_url>
cd ecv-backend
```

---

## 2. Instalar dependencias

```bash
composer install
```

---

## 3. Configurar entorno

Copiar archivo `.env`:

```bash
cp .env.example .env
```

---

## 4. Generar clave

```bash
php artisan key:generate
```

---

# 🗄️ Base de datos

## Configurar PostgreSQL en `.env`

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ecv_db
DB_USERNAME=postgres
DB_PASSWORD=tu_password
```

---

## Ejecutar migraciones

```bash
php artisan migrate
```

---

# 🔐 Autenticación (Sanctum)

## Instalación

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

---

## Uso

Header obligatorio:

```http
Authorization: Bearer TOKEN
Accept: application/json
```

---

# 🧑‍⚖️ Roles

| ID | Rol     |
| -- | ------- |
| 1  | Admin   |
| 2  | Doctor  |
| 3  | Usuario |

---

# 🔒 Middleware

* `auth:sanctum`
* `role:X`

Ejemplo:

```php
Route::middleware(['auth:sanctum', 'role:1'])->group(...)
```

---

# 📡 API ENDPOINTS

---

# 🔐 AUTH

| Método | Endpoint           |
| ------ | ------------------ |
| POST   | /api/auth/register |
| POST   | /api/auth/login    |
| POST   | /api/auth/logout   |
| GET    | /api/auth/me       |

---

# 🧑‍⚕️ DOCTORS

| Método | Endpoint          | Rol    |
| ------ | ----------------- | ------ |
| GET    | /api/doctors      | auth   |
| GET    | /api/doctors/{id} | auth   |
| POST   | /api/doctors      | admin  |
| PUT    | /api/doctors/{id} | doctor |
| DELETE | /api/doctors/{id} | admin  |

---

# 📅 APPOINTMENTS

| Método | Endpoint                      | Rol    |
| ------ | ----------------------------- | ------ |
| GET    | /api/appointments             | auth   |
| GET    | /api/appointments/{id}        | auth   |
| POST   | /api/appointments             | user   |
| PUT    | /api/appointments/{id}/status | doctor |

---

# 💬 FORO

## Posts

| Método | Endpoint        |
| ------ | --------------- |
| GET    | /api/posts      |
| GET    | /api/posts/{id} |
| POST   | /api/posts      |
| PUT    | /api/posts/{id} |
| DELETE | /api/posts/{id} |

---

## Comments

| Método | Endpoint                     |
| ------ | ---------------------------- |
| POST   | /api/posts/{postId}/comments |
| DELETE | /api/comments/{id}           |

---

# 🧠 AUTOEVALUACIÓN

| Método | Endpoint         |
| ------ | ---------------- |
| GET    | /api/evaluations |
| POST   | /api/evaluations |

---

# 🏥 HOSPITALES

| Método | Endpoint            | Rol   |
| ------ | ------------------- | ----- |
| GET    | /api/hospitals      | auth  |
| GET    | /api/hospitals/{id} | auth  |
| POST   | /api/hospitals      | admin |
| PUT    | /api/hospitals/{id} | admin |
| DELETE | /api/hospitals/{id} | admin |

---

# 🧠 Lógica de negocio

## Citas

* Usuario agenda cita
* Doctor gestiona estado
* No se permite duplicar horario

---

## Foro

* Usuario crea posts
* Solo autor puede editar/eliminar
* Comentarios protegidos por autoría

---

## Autoevaluación

* Se almacenan respuestas en JSON
* Se calcula riesgo:

| Score | Nivel  |
| ----- | ------ |
| 0-3   | low    |
| 4-6   | medium |
| 7+    | high   |

---

# 🔐 Seguridad

* Hash de contraseñas
* Tokens con Sanctum
* Middleware por rol
* Validación backend obligatoria
* Control de autoría
* Manejo de errores 404

---

# ⚠️ Manejo de errores

Ejemplo:

```json
{
  "message": "Recurso no encontrado"
}
```

---

# 🧪 Testing recomendado

* Postman / Thunder Client
* Probar todos los endpoints
* Verificar roles y permisos

---

# ▶️ Ejecución

```bash
php artisan serve
```

---

# 📂 Estructura

```
app/
 ├── Models/
 ├── Http/
 │    ├── Controllers/
 │    ├── Middleware/
routes/
 ├── api.php
database/
 ├── migrations/
```

---

# 📌 Estado del proyecto

✔ Autenticación
✔ Roles
✔ Doctores
✔ Citas
✔ Foro
✔ Autoevaluación
✔ Hospitales

---

# 🚀 Próximo paso

Frontend con React:

* Login
* Dashboard
* Consumo API
* Mapa con hospitales

---

# ⚠️ Nota

La autoevaluación es orientativa y no reemplaza diagnóstico médico profesional.

---
