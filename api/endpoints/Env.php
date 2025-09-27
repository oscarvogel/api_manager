<?php
/**
 * Endpoint de diagnóstico para exponer algunas variables de entorno (solo para desarrollo)
 */
class Env {
    public function handle() {
        header('Content-Type: application/json');
        $debugGetenv = getenv('DEBUG');
        $debugEnv = $_ENV['DEBUG'] ?? null;
        $debugServer = $_SERVER['DEBUG'] ?? null;

        echo json_encode([
            'DEBUG_getenv' => $debugGetenv,
            'DEBUG_env' => $debugEnv,
            'DEBUG_server' => $debugServer,
            'all_env' => array_intersect_key($_ENV, array_flip(['DB_DRIVER','DB_HOST','DB_PORT','DB_NAME','DB_USER','DB_PASS','DEBUG']))
        ]);
    }
}
