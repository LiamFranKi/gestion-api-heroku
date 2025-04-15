<?php

namespace App\Controllers;

use App\Models\EventoModel;
use App\Models\UsuarioModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EventoController
{
    private $eventoModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->eventoModel = new EventoModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function listarEventos(Request $request, Response $response): Response
    {
        $eventos = $this->eventoModel->listarEventos();
        $payload = json_encode($eventos);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function obtenerEvento(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $evento = $this->eventoModel->obtenerEvento($id);
        if ($evento) {
            $payload = json_encode($evento);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            return $response->withStatus(404)->withJson(['error' => 'Evento no encontrado']);
        }
    }

    public function crearEvento(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $titulo = $data['titulo'] ?? '';
        $descripcion = $data['descripcion'] ?? null;
        $fecha_inicio = $data['fecha_inicio'] ?? '';
        $fecha_fin = $data['fecha_fin'] ?? null;
        $ubicacion = $data['ubicacion'] ?? null;
        $usuario_creador_id = $data['usuario_creador_id'] ?? 0;

        // Validar que el usuario creador exista
        if (!$this->usuarioModel->obtenerUsuario($usuario_creador_id)) {
            return $response->withStatus(400)->withJson(['error' => 'El usuario creador no existe']);
        }

        if (!empty($titulo) && !empty($fecha_inicio) && is_numeric($usuario_creador_id) && $usuario_creador_id > 0) {
            $eventoId = $this->eventoModel->crearEvento($titulo, $descripcion, $fecha_inicio, $fecha_fin, $ubicacion, $usuario_creador_id);
            if ($eventoId) {
                return $response->withStatus(201)->withJson(['id' => $eventoId, 'message' => 'Evento creado']);
            } else {
                return $response->withStatus(500)->withJson(['error' => 'Error al crear el evento']);
            }
        } else {
            return $response->withStatus(400)->withJson(['error' => 'Faltan datos para crear el evento']);
        }
    }

    public function actualizarEvento(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $eventoExistente = $this->eventoModel->obtenerEvento($id);
        if (!$eventoExistente) {
            return $response->withStatus(404)->withJson(['error' => 'Evento no encontrado']);
        }

        $data = $request->getParsedBody();
        $titulo = $data['titulo'] ?? $eventoExistente['titulo'];
        $descripcion = $data['descripcion'] ?? $eventoExistente['descripcion'];
        $fecha_inicio = $data['fecha_inicio'] ?? $eventoExistente['fecha_inicio'];
        $fecha_fin = $data['fecha_fin'] ?? $eventoExistente['fecha_fin'];
        $ubicacion = $data['ubicacion'] ?? $eventoExistente['ubicacion'];
        $usuario_creador_id = $data['usuario_creador_id'] ?? $eventoExistente['usuario_creador_id'];

        // Validar que el usuario creador exista
        if (!$this->usuarioModel->obtenerUsuario($usuario_creador_id)) {
            return $response->withStatus(400)->withJson(['error' => 'El usuario creador no existe']);
        }

        $actualizado = $this->eventoModel->actualizarEvento($id, $titulo, $descripcion, $fecha_inicio, $fecha_fin, $ubicacion, $usuario_creador_id);
        if ($actualizado) {
            return $response->withJson(['message' => 'Evento actualizado']);
        } else {
            return $response->withStatus(500)->withJson(['error' => 'Error al actualizar el evento']);
        }
    }

    public function eliminarEvento(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $eventoExistente = $this->eventoModel->obtenerEvento($id);
        if (!$eventoExistente) {
            return $response->withStatus(404)->withJson(['error' => 'Evento no encontrado']);
        }

        $eliminado = $this->eventoModel->eliminarEvento($id);
        if ($eliminado) {
            return $response->withJson(['message' => 'Evento eliminado']);
        } else {
            return $response->withStatus(500)->withJson(['error' => 'Error al eliminar el evento']);
        }
    }

    public function asignarUsuario(Request $request, Response $response, array $args): Response
    {
        $evento_id = (int)$args['evento_id'];
        $usuario_id = (int)$args['usuario_id'];

        if (!$this->eventoModel->obtenerEvento($evento_id)) {
            return $response->withStatus(404)->withJson(['error' => 'Evento no encontrado']);
        }

        if (!$this->usuarioModel->obtenerUsuario($usuario_id)) {
            return $response->withStatus(404)->withJson(['error' => 'Usuario no encontrado']);
        }

        if ($this->eventoModel->asignarUsuario($evento_id, $usuario_id)) {
            return $response->withStatus(201)->withJson(['message' => 'Usuario asignado al evento']);
        } else {
            return $response->withStatus(500)->withJson(['error' => 'Error al asignar el usuario al evento']);
        }
    }

    public function listarUsuariosAsignados(Request $request, Response $response, array $args): Response
    {
        $evento_id = (int)$args['evento_id'];
        if (!$this->eventoModel->obtenerEvento($evento_id)) {
            return $response->withStatus(404)->withJson(['error' => 'Evento no encontrado']);
        }

        $usuarios = $this->eventoModel->listarUsuariosAsignados($evento_id);
        $payload = json_encode($usuarios);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}