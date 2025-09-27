<?php
/**
 * Endpoint para insertar un nuevo registro en una tabla.
 */
class CreateRecord {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function handle($input = null) {
        $input = $input ?: json_decode(file_get_contents('php://input'), true);
        $table = $input['table'] ?? null;
        $data = $input['data'] ?? null;

        if (!$table || !$data || !is_array($data)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Datos insuficientes']);
            return;
        }

        try {
            // If inserting into usuarios, validate and hash password
            if (strtolower($table) === 'usuarios') {
                if (!isset($data['password']) || strlen($data['password']) < 8) {
                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'La contraseña es obligatoria y debe tener al menos 8 caracteres']);
                    return;
                }
                // Hash password and set password_hash field
                $password = $data['password'];
                $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
                // Remove plain password before inserting
                unset($data['password']);
            }

            $this->db->insert($table, $data);
            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Registro creado']);
        } catch (PDOException $pdoEx) {
            // SQL error (e.g., unknown column) -> return JSON with message but avoid full stack trace
            http_response_code(500);
            header('Content-Type: application/json');
            $response = ['error' => 'Error en la base de datos'];
            // Mostrar detalle sólo si DEBUG está activo
            $debugEnv = getenv('DEBUG') ?: $_ENV['DEBUG'] ?? null;
            $debug = in_array(strtolower((string)$debugEnv), ['1', 'true', 'yes'], true);
            if ($debug) {
                $response['detail'] = $pdoEx->getMessage();
            }
            echo json_encode($response);
        } catch (Exception $ex) {
            http_response_code(500);
            header('Content-Type: application/json');
            $response = ['error' => 'Error del servidor'];
            $debugEnv = getenv('DEBUG') ?: $_ENV['DEBUG'] ?? null;
            $debug = in_array(strtolower((string)$debugEnv), ['1', 'true', 'yes'], true);
            if ($debug) {
                $response['detail'] = $ex->getMessage();
            }
            echo json_encode($response);
        }
    }
}