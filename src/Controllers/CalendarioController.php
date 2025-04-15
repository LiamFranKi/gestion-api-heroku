<?php

namespace App\Controllers;

use App\Models\CalendarioModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CalendarioController
{
    private $calendarioModel;

    public function __construct()
    {
        $this->calendarioModel = new CalendarioModel();
    }

    private function formatearFecha($fechaString)
    {
        if ($fechaString) {
            $fecha = new \DateTime($fechaString);
            return $fecha->format('d-m-Y H:i:s'); // Incluye la hora si es relevante
        }
        return null;
    }

    public function obtenerEventosPorRango(Request $request, Response $response): Response
    {
        $fecha_inicio = $request->getQueryParams()['fecha_inicio'] ?? null;
        $fecha_fin = $request->getQueryParams()['fecha_fin'] ?? null;

        if ($fecha_inicio && $fecha_fin) {
            $eventosData = $this->calendarioModel->obtenerEventosPorRango($fecha_inicio, $fecha_fin);
            $eventosFormateados = array_map(function ($evento) {
                $evento['fecha_inicio'] = $this->formatearFecha($evento['fecha_inicio']);
                $evento['fecha_fin'] = $this->formatearFecha($evento['fecha_fin']);
                $evento['fecha_creacion'] = $this->formatearFecha($evento['fecha_creacion']);
                return $evento;
            }, $eventosData);

            $payload = json_encode($eventosFormateados);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            return $response->withStatus(400)->withJson(['error' => 'Se deben proporcionar las fechas de inicio y fin']);
        }
    }

    // Podríamos agregar aquí un método similar para obtener tareas por rango
}