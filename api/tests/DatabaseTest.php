<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
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

        // Crear tabla temporal para pruebas
        $this->createTestTables();
    }

    protected function tearDown(): void
    {
        // Eliminar tablas temporales al finalizar
        $this->dropTestTables();
    }

    private function createTestTables(): void
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS test_usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        ";
        $this->pdo->exec($sql);
    }

    private function dropTestTables(): void
    {
        $sql = "DROP TABLE IF EXISTS test_usuarios;";
        $this->pdo->exec($sql);
    }

    public function testMySQLConnection(): void
    {
        $dbConfig = $this->config['database']['connections']['mysql'];
        $driver = new MySQLDriver($dbConfig);

        $pdo = $driver->connect();

        $this->assertInstanceOf(PDO::class, $pdo);
        $this->assertEquals('mysql', $pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
    }

    public function testInsertAndSelect(): void
    {
        $dbConfig = $this->config['database']['connections']['mysql'];
        $driver = new MySQLDriver($dbConfig);
        $driver->connect();

        // Insertar un registro
        $data = ['nombre' => 'Juan', 'email' => 'juan@test.com'];
        $driver->insert('test_usuarios', $data);

        // Seleccionar
        $result = $driver->select('test_usuarios', '*', ['email' => 'juan@test.com']);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Juan', $result[0]['nombre']);
    }

    public function testUpdate(): void
    {
        $dbConfig = $this->config['database']['connections']['mysql'];
        $driver = new MySQLDriver($dbConfig);
        $driver->connect();

        // Insertar
        $data = ['nombre' => 'Pedro', 'email' => 'pedro@test.com'];
        $driver->insert('test_usuarios', $data);

        // Actualizar
        $driver->update('test_usuarios', ['nombre' => 'Carlos'], ['email' => 'pedro@test.com']);

        // Verificar
        $result = $driver->select('test_usuarios', '*', ['email' => 'pedro@test.com']);
        $this->assertEquals('Carlos', $result[0]['nombre']);
    }

    public function testDelete(): void
    {
        $dbConfig = $this->config['database']['connections']['mysql'];
        $driver = new MySQLDriver($dbConfig);
        $driver->connect();

        // Insertar
        $data = ['nombre' => 'Eliminar', 'email' => 'eliminar@test.com'];
        $driver->insert('test_usuarios', $data);

        // Eliminar
        $driver->delete('test_usuarios', ['email' => 'eliminar@test.com']);

        // Verificar
        $result = $driver->select('test_usuarios', '*', ['email' => 'eliminar@test.com']);
        $this->assertCount(0, $result);
    }
}