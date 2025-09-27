<?php
/**
 * Middleware para validar la clave de API desde la base de datos.
 */
class AuthMiddleware {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function authenticate() {
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;

        if (!$apiKey) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Clave de API no proporcionada']);
            exit;
        }

        // Consultar la clave en la base de datos
        $sql = "SELECT id, user_id, active, expires_at FROM api_keys WHERE api_key = :api_key";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->execute([':api_key' => $apiKey]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Clave de API inválida']);
            exit;
        }

        $keyData = $result[0];

        // Verificar si la clave está activa
        if (!$keyData['active']) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Clave de API inactiva']);
            exit;
        }

        // Verificar si ha expirado
        if ($keyData['expires_at'] && new DateTime() > new DateTime($keyData['expires_at'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Clave de API expirada']);
            exit;
        }

        // Opcional: registrar uso de la clave
        // $this->logApiKeyUsage($keyData['id']);
    }

    private function logApiKeyUsage($keyId) {
        $sql = "INSERT INTO api_logs (api_key_id, accessed_at, ip_address) VALUES (:key_id, NOW(), :ip)";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->execute([
            ':key_id' => $keyId,
            ':ip' => $_SERVER['REMOTE_ADDR']
        ]);
    }
}