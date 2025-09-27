# API Manager

Proyecto API + Frontend para gestión de usuarios y claves API.

## Estructura del repositorio

- `api/` — Backend PHP (endpoints, drivers, tests)
- `frontend/` — Frontend Vue 3 + Vite

## Requisitos

- PHP 7.4+ (recomendado 8.0+)
- Composer
- Node 18+ (npm o pnpm)
- MySQL/MariaDB o el driver que prefieras

## Configuración local rápida

1. Copia el archivo de ejemplo y rellena con tus credenciales:

```powershell
cp api/.env.example api/.env
# Edita api/.env y completa DB_* y DEBUG
```

2. Instala dependencias backend:

```powershell
cd api
composer install
```

3. Instala dependencias frontend e inicia el servidor de desarrollo:

```powershell
cd frontend
npm install
npm run dev
```

4. (Opcional) Importa la base de datos de ejemplo:

```sql
-- Ejecuta el archivo SQL en tu servidor MySQL
SOURCE path/to/api/database_init.sql;
```

## Ejecutar tests

- Backend (PHPUnit):

```powershell
cd api
vendor/bin/phpunit
```

## Seguridad

- No subas `api/.env` al repositorio. Usa `api/.env.example` como plantilla.
- Si una credencial se filtra, róta las claves inmediatamente.

## Cómo contribuir

1. Fork
2. Crear rama `feature/xyz`
3. Abrir Pull Request

## CI/CD (GitHub Actions)

Se incluye un workflow básico en `.github/workflows/ci.yml` que ejecuta tests PHP y construye el frontend en cada push/PR.

---

Si quieres, puedo añadir instrucciones de despliegue (Docker, Render, etc.) o configurar un pipeline de despliegue automático.
