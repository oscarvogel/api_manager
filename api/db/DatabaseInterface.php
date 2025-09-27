<?php
/**
 * Interfaz que define los métodos que deben implementar los drivers de base de datos.
 */
interface DatabaseInterface {
    public function connect();
    public function query($sql, $params = []);
    public function insert($table, $data);
    public function select($table, $fields = '*', $where = []);
}