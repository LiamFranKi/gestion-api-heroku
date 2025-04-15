<?php

namespace App\Models;

use App\Config\Database;

class UsuarioModel
{
    private $conn;
    private $table = 'usuarios';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function listarUsuarios()
    {
        $query = "SELECT id, nombre, email, rol, fecha_creacion FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function crearUsuario($nombre, $email, $password, $rol = 'usuario')
    {
        $query = "INSERT INTO " . $this->table . " (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password); // ¡RECUERDA! Esto es inseguro
        $stmt->bindParam(":rol", $rol);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function obtenerUsuario($id)
    {
        $query = "SELECT id, nombre, email, rol, fecha_creacion FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Implementa aquí los métodos para actualizar y eliminar usuarios
}