# CI/CD Workflow Documentation

##  Overview

Este repositorio utiliza GitHub Actions para automatizar el proceso de desarrollo, testing y deployment.

##  Workflows Disponibles

### 1. **Development Workflow** (`development.yml`)
- **Trigger:** Push a `develop`, `feature/**`, `hotfix/**`
- **Propósito:** Validación rápida durante desarrollo
- **Acciones:**
  - Verificación de sintaxis básica
  - Validación de estructura de archivos
  - Check de configuración de entorno
  - Verificación de dependencias

### 2. **CI/CD Pipeline** (`ci.yml`)
- **Trigger:** Push a `main`, `develop`, `feature/**` y PRs
- **Propósito:** Testing completo y validación de calidad
- **Acciones:**
  - Análisis de calidad de código
  - Tests del backend (PHP/Laravel)
  - Tests del frontend (React/Node.js)
  - Build de Docker containers
  - Análisis de seguridad

### 3. **Pull Request Validation** (`pr-validation.yml`)
- **Trigger:** PRs a `main` o `develop`
- **Propósito:** Validación específica de Pull Requests
- **Acciones:**
  - Verificación de título de PR (conventional commits)
  - Análisis de archivos modificados
  - Tests condicionales (solo si hay cambios relevantes)
  - Comentario automático con resumen

### 4. **Deploy to Production** (`deploy.yml`)
- **Trigger:** Push a `main`, tags `v*.*.*`, o manual
- **Propósito:** Deployment a producción
- **Acciones:**
  - Validación completa pre-deploy
  - Build de imágenes Docker
  - Deploy a staging y producción
  - Verificación post-deploy

### 5. **Issues Management** (`issues.yml`)
- **Trigger:** Etiquetas en issues
- **Propósito:** Automatización de gestión de issues

### 6. **Pull Requests Management** (`pull-requests.yml`)
- **Trigger:** Apertura de PRs
- **Propósito:** Automatización de gestión de PRs

## Branching Strategy

```
main (production)
├── develop (integration)
│   ├── feature/auth-system
│   ├── feature/survey-form
│   └── feature/results-dashboard
└── hotfix/critical-fix
```

### Flujo de Trabajo:

1. **Desarrollo de Features:**
   ```bash
   git checkout develop
   git checkout -b feature/nueva-funcionalidad
   # Desarrollo...
   git push origin feature/nueva-funcionalidad
   # Crear PR hacia develop
   ```

2. **Release a Producción:**
   ```bash
   git checkout main
   git merge develop
   git tag v1.0.0
   git push origin main --tags
   ```

3. **Hotfixes:**
   ```bash
   git checkout main
   git checkout -b hotfix/fix-critico
   # Fix...
   git checkout main
   git merge hotfix/fix-critico
   git checkout develop
   git merge hotfix/fix-critico
   ```

##  Testing Strategy

### Backend (Laravel)
- **Unit Tests:** Modelos y servicios
- **Feature Tests:** Endpoints de API
- **Integration Tests:** Base de datos y servicios externos

### Frontend (React)
- **Unit Tests:** Componentes individuales
- **Integration Tests:** Flujos de usuario
- **E2E Tests:** Casos de uso completos

##  Deployment Environments

### Development
- **Trigger:** Push a `develop`
- **URL:** `https://dev.smartpt.com`
- **Database:** PostgreSQL (dev)

### Staging  
- **Trigger:** PR a `main`
- **URL:** `https://staging.smartpt.com`
- **Database:** PostgreSQL (staging)

### Production
- **Trigger:** Push a `main` o tags
- **URL:** `https://smartpt.com`
- **Database:** PostgreSQL (production)

##  Security & Quality

- **PHP Syntax Check:** Validación automática de sintaxis
- **ESLint:** Linting de JavaScript/React
- **Security Scan:** Análisis de vulnerabilidades
- **Docker Security:** Escaneo de imágenes
- **Environment Variables:** Verificación de configuración

##  Monitoring & Notifications

- **GitHub Summary:** Resultados en cada workflow
- **PR Comments:** Comentarios automáticos con estado
- **Slack/Email:** Notificaciones en fallos críticos (configurar)

##  Local Development

### Requisitos:
- Docker & Docker Compose
- PHP 8.2+
- Node.js 20+
- Composer
- npm/yarn

### Setup:
```bash
# Clonar repositorio
git clone https://github.com/hurtadx/SmartPT.git
cd SmartPT

# Backend
cd SmartBackend
composer install
cp .env.example .env
php artisan key:generate

# Frontend  
cd ../SmartFrontend
npm install

# Docker
cd ..
docker-compose up -d
```

## 📝 Convenciones

### Commits:
- `feat:` nueva funcionalidad
- `fix:` corrección de bugs
- `docs:` documentación
- `style:` formateo
- `refactor:` refactoring
- `test:` tests
- `chore:` mantenimiento

### PR Titles:
- Deben seguir conventional commits
- Ejemplo: `feat(auth): add JWT authentication`

---

**Desarrollado por André Hurtado - SmartPT © 2025**
