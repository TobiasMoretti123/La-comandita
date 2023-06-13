<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->post('/alta', \UsuarioController::class . ':CargarUno');
    $group->get('/mostrar', \UsuarioController::class . ':TraerUno');
    $group->get('/mostrarTodos', \UsuarioController::class . ':TraerTodos');
  });

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->post('/alta', \ProductoController::class . ':CargarUno');
    $group->get('/mostrar', \ProductoController::class . ':TraerUno');
    $group->get('/mostrarTodos', \ProductoController::class . ':TraerTodos');
  });

$app->group('/mesa', function (RouteCollectorProxy $group) {
    $group->post('/alta', \MesaController::class . ':CargarUno');
    $group->get('/mostrar', \MesaController::class . ':TraerUno');
    $group->get('/mostrarTodos', \MesaController::class . ':TraerTodos');
  });

  $app->group('/pedido', function (RouteCollectorProxy $group) {
    $group->post('/alta', \PedidoController::class . ':CargarUno');
    $group->get('/mostrar', \PedidoController::class . ':TraerUno');
    $group->get('/mostrarTodos', \PedidoController::class . ':TraerTodos');
  });
$app->run();
