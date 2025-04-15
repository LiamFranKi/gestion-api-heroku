<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\UsuarioModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TareaController
{
    private $tareaModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->tareaModel = new TareaModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function listarTareas(Request $request, Response $response): Response
    {
        $tareas = $this->tareaModel->listarTareas();
        $payload = json_encode($tareas);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function obtenerTarea(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $tarea = $this->tareaModel->obtenerTarea($id);
        if ($tarea) {
            $payload = json_encode($tarea);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            return $response->withStatus(404)->withJson(['error' => 'Tarea no encontrada']);
        }
    }

    public function crearTarea(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $titulo = $data['titulo'] ?? '';
        $descripcion = $data['descripcion'] ?? null;
        $fecha_limite = $data['fecha_limite'] ?? null;
        $usuario_creador_id = $data['usuario_creador_id'] ?? 0;

        // Validar que el usuario creador exista
        if (!$this->usuarioModel->obtenerUsuario($usuario_creador_id)) {
            return $response->withStatus(400)->withJson(['error' => 'El usuario creador no existe']);
        }

        if (!empty($titulo) && is_numeric($usuario_creador_id) && $usuario_creador_id > 0) {
            $tareaId = $this->tareaModel->crearTarea($titulo, $descripcion, $fecha_limite, $usuario_creador_id);
            if ($tareaId) {
                return $response->withStatus(201)->withJson(['id' => $tareaId, 'message' => 'Tarea creada']);
            } else {
                return $response->withStatus(500)->withJson(['error' => 'Error al crear la tarea']);
            }
        } else {
            return $response->withStatus(400)->withJson(['error' => 'Faltan datos para crear la tarea']);
        }
    }

    public function actualizarTarea(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $tareaExistente = $this->tareaModel->obtenerTarea($id);
        if (!$tareaExistente) {
            return $response->withStatus(404)->withJson(['error' => 'Tarea no encontrada']);
        }

        $data = $request->getParsedBody();
        $titulo = $data['titulo'] ?? $tareaExistente['titulo'];
        $descripcion = $data['descripcion'] ?? $tareaExistente['descripcion'];
        $fecha_limite = $data['fecha_limite'] ?? $tareaExistente['fecha_limite'];
        $estado = $data['estado'] ?? $tareaExistente['estado'];
        $usuario_creador_id = $data['usuario_creador_id'] ?? $tareaExistente['usuario_creador_id'];

        // Validar que el usuario creador exista
        if (!$this->usuarioModel->obtenerUsuario($usuario_creador_id)) {
            return $response->withStatus(400)->withJson(['error' => 'El usuario creador no existe']);
        }

        $actualizado = $this->tareaModel->actualizarTarea($id, $titulo, $descripcion, $fecha_limite, $estado, $usuario_creador_id);
        if ($actualizado) {
            return $response->withJson(['message' => 'Tarea actualizada']);
        } else {
            return $response->withStatus(500)->withJson(['error' => 'Error al actualizar la tarea']);
        }
    }

    public function eliminarTarea(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $tareaExistente = $this->tareaModel->obtenerTarea($id);
        if (!$tareaExistente) {
            return $response->withStatus(404)->withJson(['error' => 'Tarea no encontrada']);
        }

        $eliminado = $this->tareaModel->eliminarTarea($id);
        if ($eliminado) {
            return $response->withJson(['message' => 'Tarea eliminada']);
        } else {
            return $response->withStatus(500)->withJson(['error' => 'Error al eliminar la tarea']);
        }
    }

    public function asignarUsuario(Request $request, Response $response, array $args): Response
    {
        $tarea_id = (int)$args['tarea_id'];
        $usuario_id = (int)$args['usuario_id'];

        if (!$this->tareaModel->obtenerTarea($tarea_id)) {
            return $response->withStatus(404)->withJson(['error' => 'Tarea no encontrada']);
        }

        if (!$this->usuarioModel->obtenerUsuario($usuario_id)) {
            return $response->withStatus(404)->withJson(['error' => 'Usuario no encontrado']);
        }

        if ($this->tareaModel->asignarUsuario($tarea_id, $usuario_id)) {
            return $response->withStatus(201)->withJson(['message' => 'Usuario asignado a la tarea']);
        } else {
            return $response->withStatus(500)->withJson(['error' => 'Error al asignar el usuario a la tarea']);
        }
    }

    public function listarUsuariosAsignados(Request $request, Response $response, array $args): Response
    {
        $tarea_id = (int)$args['tarea_id'];
        if (!$this->tareaModel->obtenerTarea($tarea_id)) {
            return $response->withStatus(404)->withJson(['error' => 'Tarea no encontrada']);
        }

        $usuarios = $this->tareaModel->listarUsuariosAsignados($tarea_id);
        $payload = json_encode($usuarios);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}