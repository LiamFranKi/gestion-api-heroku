<?php

namespace App\Models;

use App\Config\Database;

class CalendarioModel
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerEventosPorRango($fecha_inicio, $fecha_fin)
    {
        $query = "SELECT id, titulo, descripcion, fecha_inicio, fecha_fin, ubicacion, usuario_creador_id, fecha_creacion
                  FROM eventos
                  WHERE fecha_inicio >= :fecha_inicio AND (fecha_fin <= :fecha_fin OR fecha_fin IS NULL)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":fecha_inicio", $fecha_inicio);
        $stmt->bindParam(":fecha_fin", $fecha_fin);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Podríamos agregar aquí un método similar para obtener tareas por rango (filtrando por fecha_limite)
}