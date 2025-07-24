# SmartPT

Sistema de gestión de encuestas desarrollado con Laravel y React.

## Requisitos Previos

**Instala solo esto antes de comenzar:**

1. **Docker Desktop** - Descarga desde: https://www.docker.com/products/docker-desktop/
2. **Git** - Descarga desde: https://git-scm.com/downloads

**Eso es todo.** No necesitas instalar PHP, Node.js, PostgreSQL ni configurar variables de entorno. Docker se encarga de todo automáticamente.

## Instalación Rápida

```bash
git clone https://github.com/hurtadx/SmartPT.git

cd SmartPT

docker-compose up -d --build
```

Luego ejecuta las migraciones y genera la clave de Laravel (esto prepara la base de datos y activa la seguridad):

```bash
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan key:generate
```

### Acceso
- **Aplicación**: http://localhost:5173

---

## Descripción

SmartPT es una aplicación web que permite a los usuarios registrarse, autenticarse y completar encuestas de desarrollo. El sistema incluye funcionalidades de autenticación, gestión de encuestas y visualización de resultados.

## Estructura del Proyecto

```
SmartPT/
├── SmartBackend/    # API REST desarrollada en Laravel
└── SmartFrontend/   # Aplicación web desarrollada en React
```

## Tecnologías

**Backend**: Laravel 10, PHP 8.2, PostgreSQL 15  
**Frontend**: React 19, Vite 7  
**Deployment**: Docker

## Funcionalidades

- Sistema de autenticación (login/registro)
- Formulario de encuesta con validación
- Una respuesta por usuario
- Visualización de resultados
- Interfaz responsive

## Si Docker no funciona

**Opción simple**: Instalación local

1. Instalar: PHP 8.2, Composer, Node.js, PostgreSQL
2. Backend: `cd SmartBackend && composer install && php artisan serve`
3. Frontend: `cd SmartFrontend && npm install && npm run dev`

## Tecnologías

#### Backend (Laravel)

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

#### Frontend (React)

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

**Desarrollado por**: Andrés Hurtado Molina
