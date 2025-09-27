<?php
/**
 * Punto de entrada principal de la API REST CRUD.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/db/DatabaseInterface.php';
require_once __DIR__ . '/db/MySQLDriver.php';
require_once __DIR__ . '/auth/AuthMiddleware.php';
require_once __DIR__ . '/endpoints/ReadRecords.php';
require_once __DIR__ . '/endpoints/CreateRecord.php';
require_once __DIR__ . '/endpoints/UpdateRecord.php';
require_once __DIR__ . '/endpoints/DeleteRecord.php';
require_once __DIR__ . '/endpoints/SetupSuperAdmin.php';
require_once __DIR__ . '/endpoints/Login.php';
require_once __DIR__ . '/endpoints/CreateApiKey.php';
require_once __DIR__ . '/endpoints/Env.php';

$config = include __DIR__ . '/config/config.php';

// Seleccionar driver
$driverName = $config['database']['default'];
$dbConfig = $config['database']['connections'][$driverName];

switch ($driverName) {
    case 'mysql':
        $db = new MySQLDriver($dbConfig);
        break;
    // Agregar otros drivers aquí
    default:
        throw new Exception("Driver no soportado: $driverName");
}

$db->connect();

// Obtener endpoint y método
$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? null;

// Endpoints públicos que no requieren autenticación
$publicEndpoints = ['setup_superadmin', 'login', 'env'];

// Solo aplicar autenticación si no es un endpoint público
if (!in_array($endpoint, $publicEndpoints)) {
    $auth = new AuthMiddleware($db);
    $auth->authenticate();
}

// Rutas RESTful
switch ($endpoint) {
    case 'read':
        $handler = new ReadRecords($db);
        $handler->handle();
        break;
    case 'create':
        $handler = new CreateRecord($db);
        $handler->handle();
        break;
    case 'update':
        $handler = new UpdateRecord($db);
        $handler->handle();
        break;
    case 'delete':
        $handler = new DeleteRecord($db);
        $handler->handle();
        break;
    case 'create_api_key':
        $handler = new CreateApiKey($db);
        $handler->handle();
        break;
    case 'env':
        $handler = new Env();
        $handler->handle();
        break;
    case 'setup_superadmin':
        $handler = new SetupSuperAdmin($db);
        $handler->handle();
        break;      
    case 'login':
        $handler = new Login($db);
        $handler->handle();
        break;          
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint no encontrado']);
        break;
}