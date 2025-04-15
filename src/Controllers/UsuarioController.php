<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuarioController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function listarUsuarios(Request $request, Response $response): Response
    {
        $usuarios = $this->usuarioModel->listarUsuarios();
        $payload = json_encode($usuarios);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function obtenerUsuario(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $usuario = $this->usuarioModel->obtenerUsuario($id);
        if ($usuario) {
            $payload = json_encode($usuario);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            return $response->withStatus(404)->withJson(['error' => 'Usuario no encontrado']);
        }
    }

    public function crearUsuario(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $nombre = $data['nombre'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? ''; // ¡PELIGRO! Contraseña en texto plano
        $rol = $data['rol'] ?? 'usuario';

        if (!empty($nombre) && !empty($email) && !empty($password)) {
            $usuarioId = $this->usuarioModel->crearUsuario($nombre, $email, $password, $rol);
            if ($usuarioId) {
                return $response->withStatus(201)->withJson(['id' => $usuarioId, 'message' => 'Usuario creado']);
            } else {
                return $response->withStatus(500)->withJson(['error' => 'Error al crear el usuario']);
            }
        } else {
            return $response->withStatus(400)->withJson(['error' => 'Faltan datos para crear el usuario']);
        }
    }

    public function actualizarUsuario(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $usuarioExistente = $this->usuarioModel->obtenerUsuario($id);
        if (!$usuarioExistente) {
            return $response->withStatus(404)->withJson(['error' => 'Usuario no encontrado']);
        }

        $data = $request->getParsedBody();
        $nombre = $data['nombre'] ?? $usuarioExistente['nombre'];
        $email = $data['email'] ?? $usuarioExistente['email'];
        $password = $data['password'] ?? $usuarioExistente['password']; // ¡PELIGRO! Contraseña en texto plano
        $rol = $data['rol'] ?? $usuarioExistente['rol'];

        $actualizado = $this->usuarioModel->actualizarUsuario($id, $nombre, $email, $password, $rol);
        if ($actualizado) {
            return $response->withJson(['message' => 'Usuario actualizado']);
        } else {
            return $response->withStatus(500)->withJson(['error' => 'Error al actualizar el usuario']);
        }
    }

    public function eliminarUsuario(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $usuarioExistente = $this->usuarioModel->obtenerUsuario($id);
        if (!$usuarioExistente) {
            return $response->withStatus(404)->withJson(['error' => 'Usuario no encontrado']);
        }

        $eliminado = $this->usuarioModel->eliminarUsuario($id);
        if ($eliminado) {
            return $response->withJson(['message' => 'Usuario eliminado']);
        } else {
            return $response->withStatus(500)->withJson(['error' => 'Error al eliminar el usuario']);
        }
    }
}