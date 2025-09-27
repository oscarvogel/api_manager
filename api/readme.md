# API REST CRUD con Autenticación por Claves

API REST en PHP que permite realizar operaciones CRUD sobre tablas de base de datos. Incluye autenticación por claves de API almacenadas en base de datos.

## Características

- ✅ Operaciones CRUD (Create, Read, Update, Delete) sobre cualquier tabla.
- ✅ Autenticación con claves de API seguras almacenadas en base de datos.
- ✅ Soporte para MySQL, PostgreSQL y SQL Server.
- ✅ Endpoint para generar nuevas claves de API.
- ✅ Segura y lista para integrarse con Vue.js, React, Angular, etc.

## Requisitos

- PHP 7.4+
- MySQL / PostgreSQL / SQL Server
- Extensiones: `pdo`, `pdo_mysql`, `json`, `openssl`

## Instalación

1. Clona o copia los archivos en tu servidor.
2. Crea las tablas necesarias (ver abajo).
3. Configura las credenciales en `config/config.php`.

## Estructura de Tablas

```sql
-- Tabla de usuarios (opcional)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de claves de API
CREATE TABLE IF NOT EXISTS api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    api_key VARCHAR(255) UNIQUE NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabla de logs (opcional)
CREATE TABLE IF NOT EXISTS api_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    api_key_id INT NOT NULL,
    accessed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    endpoint VARCHAR(100),
    FOREIGN KEY (api_key_id) REFERENCES api_keys(id)
);
CREATE TABLE IF NOT EXISTS setup_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setup_completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
## 🧪 Prueba del Nuevo Endpoint

### Crear una clave de API:

```bash
curl -X POST /api/?endpoint=create_api_key \
-H "Content-Type: application/json" \
-H "X-API-Key: clave_admin" \
-d '{"user_id": 1, "expires_at": "2025-12-31"}'
```

⚠️ Nota: La clave clave_admin debe tener permisos para crear claves (esto puedes controlarlo con roles, si implementas en el futuro). 

## Tests Unitarios

Este proyecto incluye tests unitarios para verificar el funcionamiento de la API.

### Requisitos

- PHP 7.4+
- PHPUnit (instalar con Composer o globalmente)

### Instalación de PHPUnit

```bash
composer require --dev phpunit/phpunit
```
---

## 🧪 Comandos útiles

```bash
# Ejecutar todos los tests
phpunit

# Ejecutar un test específico
phpunit tests/AuthTest.php

# Generar reporte de cobertura (opcional)
phpunit --coverage-html coverage/
```

### DatabaseTest.php

Prueba los drivers de base de datos (MySQL, PostgreSQL, SQL Server) para asegurar que se conecten correctamente y ejecuten operaciones CRUD.

#### Ejemplo de Test

```php
public function testMySQLConnection()
{
    $dbConfig = $this->config['database']['connections']['mysql'];
    $driver = new MySQLDriver($dbConfig);

    $pdo = $driver->connect();

    $this->assertInstanceOf(PDO::class, $pdo);
    $this->assertEquals('mysql', $pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
}
```

## ⚠️ Consideraciones

- Este test asume que tienes las tablas necesarias en la base de datos de pruebas.
- Si usas `PostgreSQL` o `SQL Server`, asegúrate de tener sus extensiones instaladas en PHP.
- Puedes usar `markTestSkipped()` para omitir tests si una extensión no está disponible.
- Ejecuta las pruebas con este comando ./vendor/bin/phpunit tests/DatabaseTest.php

## Variables de Entorno

Este proyecto usa variables de entorno para proteger credenciales sensibles.

### Configuración

Copia el archivo `.env.example` a `.env` y actualiza los valores:

```bash
cp .env.example .env
```
