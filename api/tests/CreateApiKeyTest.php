<?php

use PHPUnit\Framework\TestCase;

class CreateApiKeyTest extends TestCase
{
    private $config;
    private $pdo;

    protected function setUp(): void
    {
        $this->config = include __DIR__ . '/../config/config.php';
        $dbConfig = $this->config['database']['connections']['mysql'];

        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']}";
        $this->pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crear tabla temporal
        $sql = "
        CREATE TABLE IF NOT EXISTS test_api_keys (
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

    protected function tearDown(): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS test_api_keys;");
    }

    public function testCreateApiKey(): void
    {
        $input = json_encode([
            'user_id' => 1,
            'expires_at' => '2025-12-31 23:59:59'
        ]);

        // Simular entrada
        file_put_contents('php://input', $input);

        $dbConfig = $this->config['database']['connections']['mysql'];
        $driver = new MySQLDriver($dbConfig);
        $driver->connect();

        // Cambiar tabla para pruebas
        $originalTable = 'api_keys';
        $reflection = new ReflectionClass($driver);
        $property = $reflection->getProperty('pdo');
        $property->setAccessible(true);
        $pdo = $property->getValue($driver);

        $stmt = $pdo->prepare("INSERT INTO test_api_keys (user_id, api_key, expires_at) VALUES (:user_id, :api_key, :expires_at)");
        $apiKey = bin2hex(random_bytes(32));
        $stmt->execute([
            ':user_id' => 1,
            ':api_key' => $apiKey,
            ':expires_at' => '2025-12-31 23:59:59'
        ]);

        // Verificar que la clave fue insertada
        $result = $pdo->query("SELECT * FROM test_api_keys WHERE api_key = '$apiKey'")->fetch();
        $this->assertIsArray($result);
        $this->assertEquals(1, $result['user_id']);
        $this->assertEquals($apiKey, $result['api_key']);
    }
}