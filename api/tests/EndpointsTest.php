<?php

use PHPUnit\Framework\TestCase;

class EndpointsTest extends TestCase
{
    private $config;
    private $pdo;

    protected function setUp(): void
    {
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

        $this->config = include __DIR__ . '/../config/config.php';
        $dbConfig = $this->config['database']['connections']['mysql'];

        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']}";
        $this->pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crear tabla temporal para pruebas
        $this->createUsuariosTable();
    }

    protected function tearDown(): void
    {
        // Eliminar tabla temporal al finalizar
        $this->dropUsuariosTable();
    }

    private function createUsuariosTable(): void
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        ";
        $this->pdo->exec($sql);
    }

    private function dropUsuariosTable(): void
    {
        $sql = "DROP TABLE IF EXISTS usuarios;";
        $this->pdo->exec($sql);
    }

    public function testCrearRegistro(): void
    {
        $input = [
            'table' => 'usuarios',
            'data' => ['nombre' => 'Juan', 'email' => 'juan@ejemplo.com']
        ];

        $dbConfig = $this->config['database']['connections']['mysql'];
        $db = new MySQLDriver($dbConfig);
        $db->connect();

        $handler = new CreateRecord($db);

        $this->expectOutputRegex('/"message":"Registro creado"/');
        $handler->handle($input);
    }

    public function testLeerRegistros(): void
    {
        // Insertar un registro de prueba
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nombre, email) VALUES (?, ?)");
        $stmt->execute(['Test', 'test@example.com']);

        $dbConfig = $this->config['database']['connections']['mysql'];
        $db = new MySQLDriver($dbConfig);
        $db->connect();

        $handler = new ReadRecords($db);

        $this->expectOutputRegex('/"data":/');
        $handler->handle('usuarios', 10, 0);
    }
}