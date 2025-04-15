<?php

namespace App\Config;

class Database
{
    private $host = "localhost"; // Cambia esto si tu servidor MySQL está en otro lugar
    private $db_name = "gemini_tareas_eventos";
    private $username = "root"; // Usuario por defecto de AppServ
    private $password = "12345678";     // Contraseña por defecto de AppServ
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new \PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
        return $this->conn;
    }
}