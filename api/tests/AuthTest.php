<?php

use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
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
        $this->createApiKeysTable();
    }

    protected function tearDown(): void
    {
        // Eliminar tabla temporal al finalizar
        $this->dropApiKeysTable();
    }

    private function createApiKeysTable(): void
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS api_keys (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            api_key VARCHAR(255) UNIQUE NOT NULL,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NULL
        );
        ";
        $this->pdo->exec($sql);
    }

    private function dropApiKeysTable(): void
    {
        $sql = "DROP TABLE IF EXISTS api_keys;";
        $this->pdo->exec($sql);
    }

    public function testAutenticacionCorrecta(): void
    {
        // Insertar clave válida
        $stmt = $this->pdo->prepare("INSERT INTO api_keys (api_key) VALUES (?)");
        $stmt->execute(['clave_de_prueba_123456789']);

        $_SERVER['HTTP_X_API_KEY'] = 'clave_de_prueba_123456789';

        // Crear instancia de MySQLDriver y conectar
        $dbConfig = $this->config['database']['connections']['mysql'];
        $db = new MySQLDriver($dbConfig);
        $db->connect();

        $auth = new AuthMiddleware($db);

        // No debe lanzar error ni devolver mensaje de error
        $this->expectOutputString('');
        $auth->authenticate();

        // Verificar que no haya respuesta de error
        $this->assertTrue(true); // Si llega aquí, pasó la autenticación
    }

    public function testAutenticacionIncorrecta(): void
    {
        $_SERVER['HTTP_X_API_KEY'] = 'clave_invalida';

        // Crear instancia de MySQLDriver y conectar
        $dbConfig = $this->config['database']['connections']['mysql'];
        $db = new MySQLDriver($dbConfig);
        $db->connect();

        $auth = new AuthMiddleware($db);

        // Debe devolver error
        $this->expectOutputRegex('/"error":"Clave de API inválida"/');
        $auth->authenticate();
    }
}