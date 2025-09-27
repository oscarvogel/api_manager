<?php
/**
 * Endpoint para autenticación básica.
 */
class Login {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function handle() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $username = $input['username'] ?? null;
            $password = $input['password'] ?? null;

            if (!$username || !$password) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Usuario y contraseña requeridos']);
                return;
            }

            // Try selecting from `users` table; if it doesn't exist, fallback to `usuarios` table
            try {
                $stmt = $this->db->pdo->prepare("SELECT id, password_hash FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
            } catch (PDOException $innerEx) {
                // Table not found? fallback to `usuarios` which some deployments use
                $msg = $innerEx->getMessage();
                if (stripos($msg, 'doesn\'t exist') !== false || stripos($msg, 'no such table') !== false || $innerEx->getCode() === '42S02') {
                    $stmt = $this->db->pdo->prepare("SELECT id, password_hash FROM usuarios WHERE nombre = ? OR email = ?");
                    $stmt->execute([$username, $username]);
                    $user = $stmt->fetch();
                } else {
                    // rethrow and let outer handler return error
                    throw $innerEx;
                }
            }

            if (!$user || !password_verify($password, $user['password_hash'])) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Credenciales inválidas']);
                return;
            }

            // Generar nueva clave de API para la sesión
            $api_key = bin2hex(random_bytes(32));
            // Insert API key; use is_active column if present in schema
            try {
                $stmt = $this->db->pdo->prepare("INSERT INTO api_keys (user_id, api_key, is_active) VALUES (?, ?, TRUE)");
                $stmt->execute([$user['id'], $api_key]);
            } catch (PDOException $insertEx) {
                // Fallback to `active` column name if schema differs
                $stmt = $this->db->pdo->prepare("INSERT INTO api_keys (user_id, api_key, active) VALUES (?, ?, TRUE)");
                $stmt->execute([$user['id'], $api_key]);
            }

            http_response_code(200);
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