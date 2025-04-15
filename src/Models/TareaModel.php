<?php

namespace App\Models;

use App\Config\Database;

class TareaModel
{
    private $conn;
    private $table = 'tareas';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function listarTareas()
    {
        $query = "SELECT id, titulo, descripcion, fecha_creacion, fecha_limite, estado, usuario_creador_id FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function crearTarea($titulo, $descripcion, $fecha_limite, $usuario_creador_id)
    {
        $query = "INSERT INTO " . $this->table . " (titulo, descripcion, fecha_limite, usuario_creador_id) VALUES (:titulo, :descripcion, :fecha_limite, :usuario_creador_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":fecha_limite", $fecha_limite);
        $stmt->bindParam(":usuario_creador_id", $usuario_creador_id);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function obtenerTarea($id)
    {
        $query = "SELECT id, titulo, descripcion, fecha_creacion, fecha_limite, estado, usuario_creador_id FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function actualizarTarea($id, $titulo, $descripcion, $fecha_limite, $estado, $usuario_creador_id)
    {
        $query = "UPDATE " . $this->table . " SET titulo = :titulo, descripcion = :descripcion, fecha_limite = :fecha_limite, estado = :estado, usuario_creador_id = :usuario_creador_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":fecha_limite", $fecha_limite);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":usuario_creador_id", $usuario_creador_id);
        return $stmt->execute();
    }

    public function eliminarTarea($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function asignarUsuario($tarea_id, $usuario_id)
    {
        $query = "INSERT INTO asignaciones_tarea (tarea_id, usuario_id) VALUES (:tarea_id, :usuario_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tarea_id", $tarea_id);
        $stmt->bindParam(":usuario_id", $usuario_id);
        return $stmt->execute();
    }

    public function listarUsuariosAsignados($tarea_id)
    {
        $query = "SELECT u.id, u.nombre, u.email, u.rol, u.fecha_creacion
                  FROM usuarios u
                  INNER JOIN asignaciones_tarea at ON u.id = at.usuario_id
                  WHERE at.tarea_id = :tarea_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tarea_id", $tarea_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}