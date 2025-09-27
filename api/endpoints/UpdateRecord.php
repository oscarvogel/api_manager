<?php
/**
 * Endpoint para actualizar un registro existente.
 */
class UpdateRecord {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function handle() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $table = $input['table'] ?? null;
            $where = $input['where'] ?? null;
            $data = $input['data'] ?? null;

            if (!$table || !$where || !$data || !is_array($data) || !is_array($where)) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Datos insuficientes']);
                return;
            }

            // If updating usuarios and password provided, validate and hash it
            if (strtolower($table) === 'usuarios' && isset($data['password'])) {
                if (!is_string($data['password']) || strlen($data['password']) < 8) {
                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'La contraseña debe tener al menos 8 caracteres']);
                    return;
                }
                $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
                unset($data['password']);
            }

            $setFields = [];
            $params = [];

            foreach ($data as $key => $value) {
                $setFields[] = "$key = :$key";
                $params[$key] = $value;
            }

            $whereFields = [];
            foreach ($where as $key => $value) {
                $whereFields[] = "$key = :where_$key";
                $params["where_$key"] = $value;
            }

            $sql = "UPDATE $table SET " . implode(', ', $setFields) . " WHERE " . implode(' AND ', $whereFields);
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Registro actualizado']);
            } else {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Registro no encontrado']);
            }
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