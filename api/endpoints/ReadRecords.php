<?php
/**
 * Endpoint para leer registros de una tabla.
 */
class ReadRecords {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function handle($table = null, $limit = null, $offset = null) {
        $table = $table ?: ($_GET['table'] ?? null);
        $limit = $limit ?: (int)($_GET['limit'] ?? 100);
        $offset = $offset ?: (int)($_GET['offset'] ?? 0);

        if (!$table) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Parámetro "table" requerido']);
            return;
        }

        try {
            $sql = "SELECT * FROM $table LIMIT :limit OFFSET :offset";
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode(['data' => $data]);
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