<?php

namespace App\Models;

use App\Config\Database;

class EventoModel
{
    private $conn;
    private $table = 'eventos';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function listarEventos()
    {
        $query = "SELECT id, titulo, descripcion, fecha_inicio, fecha_fin, ubicacion, usuario_creador_id, fecha_creacion FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function crearEvento($titulo, $descripcion, $fecha_inicio, $fecha_fin, $ubicacion, $usuario_creador_id)
    {
        $query = "INSERT INTO " . $this->table . " (titulo, descripcion, fecha_inicio, fecha_fin, ubicacion, usuario_creador_id) VALUES (:titulo, :descripcion, :fecha_inicio, :fecha_fin, :ubicacion, :usuario_creador_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":fecha_inicio", $fecha_inicio);
        $stmt->bindParam(":fecha_fin", $fecha_fin);
        $stmt->bindParam(":ubicacion", $ubicacion);
        $stmt->bindParam(":usuario_creador_id", $usuario_creador_id);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function obtenerEvento($id)
    {
        $query = "SELECT id, titulo, descripcion, fecha_inicio, fecha_fin, ubicacion, usuario_creador_id, fecha_creacion FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function actualizarEvento($id, $titulo, $descripcion, $fecha_inicio, $fecha_fin, $ubicacion, $usuario_creador_id)
    {
        $query = "UPDATE " . $this->table . " SET titulo = :titulo, descripcion = :descripcion, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, ubicacion = :ubicacion, usuario_creador_id = :usuario_creador_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":fecha_inicio", $fecha_inicio);
        $stmt->bindParam(":fecha_fin", $fecha_fin);
        $stmt->bindParam(":ubicacion", $ubicacion);
        $stmt->bindParam(":usuario_creador_id", $usuario_creador_id);
        return $stmt->execute();
    }

    public function eliminarEvento($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function asignarUsuario($evento_id, $usuario_id)
    {
        $query = "INSERT INTO asignaciones_evento (evento_id, usuario_id) VALUES (:evento_id, :usuario_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->bindParam(":usuario_id", $usuario_id);
        return $stmt->execute();
    }

    public function listarUsuariosAsignados($evento_id)
    {
        $query = "SELECT u.id, u.nombre, u.email, u.rol, u.fecha_creacion
                  FROM usuarios u
                  INNER JOIN asignaciones_evento ae ON u.id = ae.usuario_id
                  WHERE ae.evento_id = :evento_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}