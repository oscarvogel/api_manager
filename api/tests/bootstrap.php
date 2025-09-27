<?php

// Cargar variables de entorno de prueba
if (file_exists(__DIR__ . '/../.env.test')) {
    $lines = file(__DIR__ . '/../.env.test', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../db/DatabaseInterface.php';
require_once __DIR__ . '/../db/MySQLDriver.php';
require_once __DIR__ . '/../db/PostgreSQLDriver.php';
require_once __DIR__ . '/../db/SQLServerDriver.php';
require_once __DIR__ . '/../auth/AuthMiddleware.php';
require_once __DIR__ . '/../endpoints/CreateRecord.php';
require_once __DIR__ . '/../endpoints/ReadRecords.php';
require_once __DIR__ . '/../endpoints/UpdateRecord.php';
require_once __DIR__ . '/../endpoints/DeleteRecord.php';
require_once __DIR__ . '/../endpoints/CreateApiKey.php';

$config = include __DIR__ . '/../config/config.php';
$dbConfig = $config['database']['connections'][$config['database']['default']];

// Usar base de datos de prueba si está definida
if (isset($_ENV['TEST_DB_NAME'])) {
    $dbConfig['database'] = $_ENV['TEST_DB_NAME'];
}

$dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']}";
$pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

define('TEST_DB_PDO', $pdo);