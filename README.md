
# SmartPT

Sistema de gestión de encuestas desarrollado con Laravel y React.

## Requisitos Previos

**Instala solo esto antes de comenzar:**

**Docker Desktop**  
Descarga desde: https://www.docker.com/products/docker-desktop/

**Git**  
Descarga desde: https://git-scm.com/downloads

**Eso es todo.** No necesitas instalar PHP, Node.js, PostgreSQL ni configurar variables de entorno. Docker se encarga de todo automáticamente.

## Instalación Rápida

```bash
git clone https://github.com/hurtadx/SmartPT.git

cd SmartPT

docker-compose up -d --build
```

### Acceso

**Aplicación:** http://localhost:5173



## Inicialización automática del backend (Docker)

Al iniciar el contenedor del backend con Docker Compose:

- Se copian automáticamente las variables de entorno desde `.env.docker` (o `.env.example` si no existe).
- Se genera la clave de aplicación de Laravel si es necesario.
- Se instalan las dependencias de Composer si faltan.
- Se ejecutan las migraciones y seeders,
- Se limpian y optimizan los cachés de Laravel.
- El servidor web se inicia automáticamente.

Solo corre:

```bash
docker compose up -d --build
```
## Cómo ejecutar los tests

Para correr los tests de backend desde Docker:

```bash
docker compose exec backend php artisan test
```

Esto ejecuta todos los tests unitarios y de integración de Laravel.


### Usuarios de Prueba

Se crean automáticamente estos usuarios de prueba:

| Email | Contraseña |
|-------|------------|
| admin@smartpt.com | password123 |
| user@smartpt.com | password123 |
| evaluador@smartpt.com | password123 |

## Descripción

SmartPT es una aplicación web que permite a los usuarios registrarse, autenticarse y completar encuestas de desarrollo. El sistema incluye funcionalidades de autenticación, gestión de encuestas y visualización de resultados.

## Estructura del Proyecto

```
SmartPT/
├── SmartBackend/    # API REST desarrollada en Laravel
└── SmartFrontend/   # Aplicación web desarrollada en React
```

## Tecnologías

**Backend:** Laravel 10, PHP 8.2, PostgreSQL 15  
**Frontend:** React 19, Vite 7  
**Deployment:** Docker

## Funcionalidades

- Sistema de autenticación (login/registro)
- Formulario de encuesta con validación
- Una respuesta por usuario
- Visualización de resultados
- Interfaz responsive

## Si Docker no funciona

**Opción simple:** Instalación local

1. Instalar: PHP 8.2, Composer, Node.js, PostgreSQL
2. Backend: `cd SmartBackend && composer install && php artisan serve`
3. Frontend: `cd SmartFrontend && npm install && npm run dev`

## Instalación Local Detallada

### Backend (Laravel)

1. Navegar al directorio del backend:
```bash
cd SmartBackend
```

2. Instalar dependencias:
```bash
composer install
```

3. Configurar el archivo de entorno:
```bash
cp .env.example .env
```

4. Generar la clave de la aplicación:
```bash
php artisan key:generate
```

5. Configurar la base de datos en `.env`:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smart_pt
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

6. Ejecutar migraciones:
```bash
php artisan migrate
```

7. Iniciar el servidor:
```bash
php artisan serve
```

### Frontend (React)

1. Navegar al directorio del frontend:
```bash
cd SmartFrontend
```

2. Instalar dependencias:
```bash
npm install
```

3. Configurar variables de entorno:
```bash
cp .env.example .env
```

4. Iniciar el servidor de desarrollo:
```bash
npm run dev
```

## API Endpoints

- `POST /api/register` - Registro
- `POST /api/login` - Login  
- `POST /api/survey/submit` - Enviar encuesta
- `GET /api/survey/results` - Ver resultados

---

**Desarrollado por:** Andrés Hurtado Molina
