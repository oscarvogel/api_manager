<?php
/**
 * Endpoint para crear una nueva clave de API.
 */
class CreateApiKey {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function handle() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $user_id = $input['user_id'] ?? null; // Opcional
            $expires_at = $input['expires_at'] ?? null; // Opcional

            if ($expires_at && !strtotime($expires_at)) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Formato de fecha inválido']);
                return;
            }

            $api_key = bin2hex(random_bytes(32)); // Genera clave segura

            $sql = "INSERT INTO api_keys (user_id, api_key, expires_at) VALUES (:user_id, :api_key, :expires_at)";
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':api_key' => $api_key,
                ':expires_at' => $expires_at
            ]);

            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode(['api_key' => $api_key]);
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