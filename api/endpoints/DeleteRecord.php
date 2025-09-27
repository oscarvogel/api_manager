<?php
/**
 * Endpoint para eliminar un registro.
 */
class DeleteRecord {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function handle() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $table = $input['table'] ?? null;
            $where = $input['where'] ?? null;

            if (!$table || !$where || !is_array($where)) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Datos insuficientes']);
                return;
            }

            $whereFields = [];
            $params = [];
            foreach ($where as $key => $value) {
                $whereFields[] = "$key = :$key";
                $params[$key] = $value;
            }

            $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereFields);
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Registro eliminado']);
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