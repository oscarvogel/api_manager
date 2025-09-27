<?php
/**
 * Endpoint para devolver información de diagnóstico del sistema
 */
class SystemInfo {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function handle() {
        try {
            $info = [];

            // Basic env
            $info['debug'] = getenv('DEBUG') ?: $_ENV['DEBUG'] ?? null;
            $info['php_version'] = phpversion();

            // DB connection check
            try {
                $stmt = $this->db->pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $info['db']['tables'] = $tables;

                // Count rows for main tables if exist
                $tablesToCheck = ['usuarios','api_keys'];
                foreach ($tablesToCheck as $t) {
                    if (in_array($t, $tables)) {
                        $cstmt = $this->db->pdo->query("SELECT COUNT(*) as c FROM `" . $t . "`");
                        $cnt = $cstmt->fetch(PDO::FETCH_ASSOC);
                        $info['db']['counts'][$t] = (int)($cnt['c'] ?? 0);
                    }
                }
            } catch (Exception $dbEx) {
                $info['db']['error'] = $dbEx->getMessage();
            }

            header('Content-Type: application/json');
            echo json_encode(['data' => $info]);
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
