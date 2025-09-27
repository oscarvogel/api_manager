<?php
/**
 * Driver para conexión con MySQL usando PDO.
 */
class MySQLDriver implements DatabaseInterface {
    public $pdo; // Cambiado a público para pruebas
    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function connect() {
        $dsn = "mysql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['database']}";
        $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->pdo;
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($table, $data) {
        $keys = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($keys) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function select($table, $fields = '*', $where = []) {
        $sql = "SELECT $fields FROM $table";
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', array_map(function($k) { return "$k = :$k"; }, array_keys($where)));
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($where);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($table, $data, $where) {
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
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($table, $where) {
        $whereFields = [];
        $params = [];
        foreach ($where as $key => $value) {
            $whereFields[] = "$key = :$key";
            $params[$key] = $value;
        }

        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereFields);
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}