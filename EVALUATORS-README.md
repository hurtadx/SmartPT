# SmartPT - Guía Rápida para Evaluadores

##  Requisitos Previos
- Docker Desktop instalado y ejecutándose
- Git (para clonar el repositorio)

## Instalación Rápida (3 pasos)

### Opción A: Script Automático (Recomendado)
```bash
1. Abrir PowerShell como Administrador
2. cd SmartPT
3. .\setup-for-evaluators.bat
```

### Opción B: Manual
```bash
1. docker-compose down
2. docker-compose build
3. docker-compose up -d
```

## Acceso al Sistema

- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000

## 👤 Usuarios de Prueba

| Email | Password | Rol |
|-------|----------|-----|
| admin@smartpt.com | password123 | Administrador |
| user@smartpt.com | password123 | Usuario |

## 📱 Flujo de Evaluación

1. **Registro/Login**: Crear cuenta o usar usuarios de prueba
2. **Completar Encuesta**: Responder todas las preguntas
3. **Ver Resultados**: Acceder a la página de resultados
4. **Verificar Seguridad**: Solo una respuesta por usuario

## Solución de Problemas

### Error "vendor/autoload.php not found"
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Puertos ocupados
```bash
# Cambiar puertos en docker-compose.yml si es necesario
# Frontend: 5173 -> 3000
# Backend: 8000 -> 8080
```

### Verificar Estado
```bash
docker-compose ps
docker-compose logs backend
docker-compose logs frontend
```

## Características Implementadas

**Frontend React 19 + Vite**
- Autenticación completa
- Formulario de encuesta con validación
- Resultados y dashboard
- Diseño responsive
- Protección de rutas

**Backend Laravel + Sanctum**
- API REST completa
- Autenticación con tokens
- Validación de datos
- Base de datos PostgreSQL
- Middleware de protección

 **Seguridad**
- Autenticación obligatoria
- Una respuesta por usuario
- Validación en backend y frontend
- Protección CSRF
- Tokens seguros

## Comandos Docker Útiles

```bash
# Ver logs en tiempo real
docker-compose logs -f

# Reiniciar un servicio específico
docker-compose restart backend

# Acceder al contenedor
docker exec -it smartpt_backend bash

# Limpiar todo
docker-compose down
docker system prune -f
```

## Información del Desarrollador

**Andrés Hurtado Molina**
- Proyecto: Sistema de Encuestas SmartPT
- Tecnologías: React 19, Laravel 10, PostgreSQL 15, Docker
- Características: Autenticación, Validación, Seguridad, Responsive Design

---
*Última actualización: Julio 2025*
