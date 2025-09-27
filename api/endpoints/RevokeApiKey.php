<?php
/**
 * Endpoint para revocar (desactivar) una clave API
 */
class RevokeApiKey {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function handle() {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
            $id = $input['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Se requiere id de la clave a revocar']);
                return;
            }

            $sql = "UPDATE api_keys SET active = 0 WHERE id = :id";
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            header('Content-Type: application/json');
            echo json_encode(['message' => 'Clave revocada']);
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
