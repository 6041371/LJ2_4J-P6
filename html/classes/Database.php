<?php

class Database {
    private $host = 'db'; 
    private $db   = 'afrika_cup';
    private $user = 'root';
    private $pass = 'root';
    protected $conn;

    public function __construct() {
        $this->conn = new PDO(
            "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4",
            $this->user,
            $this->pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public function getConnection() {
        return $this->conn;
    }

   
}
