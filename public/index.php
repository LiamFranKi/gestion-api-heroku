<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Habilitar el middleware para procesar JSON en las peticiones
$app->addBodyParsingMiddleware();

// **Rutas para Usuarios**
$app->get('/prueba', function (Request $request, Response $response) {
    $response->getBody()->write("Hola desde /prueba");
    return $response->withHeader('Content-Type', 'text/plain');
});
$app->get('/usuarios', \App\Controllers\UsuarioController::class . ':listarUsuarios');
$app->get('/usuarios/{id}', \App\Controllers\UsuarioController::class . ':obtenerUsuario');
$app->post('/usuarios', \App\Controllers\UsuarioController::class . ':crearUsuario');
$app->put('/usuarios/{id}', \App\Controllers\UsuarioController::class . ':actualizarUsuario');
$app->delete('/usuarios/{id}', \App\Controllers\UsuarioController::class . ':eliminarUsuario');

// **Rutas para Eventos**
$app->get('/eventos', \App\Controllers\EventoController::class . ':listarEventos');
$app->get('/eventos/{id}', \App\Controllers\EventoController::class . ':obtenerEvento');
$app->post('/eventos', \App\Controllers\EventoController::class . ':crearEvento');
$app->put('/eventos/{id}', \App\Controllers\EventoController::class . ':actualizarEvento');
$app->delete('/eventos/{id}', \App\Controllers\EventoController::class . ':eliminarEvento');
$app->post('/eventos/{evento_id}/asignar/{usuario_id}', \App\Controllers\EventoController::class . ':asignarUsuario');
$app->get('/eventos/{evento_id}/usuarios', \App\Controllers\EventoController::class . ':listarUsuariosAsignados');

// **Rutas para Tareas**
$app->get('/tareas', \App\Controllers\TareaController::class . ':listarTareas');
$app->get('/tareas/{id}', \App\Controllers\TareaController::class . ':obtenerTarea');
$app->post('/tareas', \App\Controllers\TareaController::class . ':crearTarea');
$app->put('/tareas/{id}', \App\Controllers\TareaController::class . ':actualizarTarea');
$app->delete('/tareas/{id}', \App\Controllers\TareaController::class . ':eliminarTarea');
$app->post('/tareas/{tarea_id}/asignar/{usuario_id}', \App\Controllers\TareaController::class . ':asignarUsuario');
$app->get('/tareas/{tarea_id}/usuarios', \App\Controllers\TareaController::class . ':listarUsuariosAsignados');



$app->run();