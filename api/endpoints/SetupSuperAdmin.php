<?php
/**
 * Endpoint para crear una clave de superadmin una sola vez.
 */
class SetupSuperAdmin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function handle() {
        try {
            // Verificar si ya se ha completado el setup
            $stmt = $this->db->pdo->prepare("SELECT setup_completed FROM setup_status LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch();

            if ($result && $result['setup_completed']) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Setup ya fue completado']);
                return;
            }

            // Crear usuario superadmin
            $password_hash = password_hash('admin123', PASSWORD_DEFAULT); // Contraseña por defecto
            $stmt = $this->db->pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute(['superadmin', 'superadmin@example.com', $password_hash]);
            $user_id = $this->db->pdo->lastInsertId();

            // Generar clave de API
            $api_key = bin2hex(random_bytes(32));

            // Guardar clave de API
            $stmt = $this->db->pdo->prepare("INSERT INTO api_keys (user_id, api_key) VALUES (?, ?)");
            $stmt->execute([$user_id, $api_key]);

            // Marcar setup como completado
            $stmt = $this->db->pdo->prepare("INSERT INTO setup_status (setup_completed) VALUES (TRUE)");
            $stmt->execute();

            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Superadmin creado', 'api_key' => $api_key]);
        } catch (PDOException $pdoEx) {
            http_response_code(500);
            header('Content-Type: application/json');
            $response = ['error' => 'Error en la base de datos'];
            $debugEnv = getenv('DEBUG') ?: $_ENV['DEBUG'] ?? null;
            $debug = in_array(strtolower((string)$debugEnv), ['1','true','yes'], true);
            if ($debug) $response['detail'] = $pdoEx->getMessage();
            echo json_encode($response);
        } catch (Exception $ex) {
            http_response_code(500);
            header('Content-Type: application/json');
            $response = ['error' => 'Error del servidor'];
            $debugEnv = getenv('DEBUG') ?: $_ENV['DEBUG'] ?? null;
            $debug = in_array(strtolower((string)$debugEnv), ['1','true','yes'], true);
            if ($debug) $response['detail'] = $ex->getMessage();
            echo json_encode($response);
        }
    }
}