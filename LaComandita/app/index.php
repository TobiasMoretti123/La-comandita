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

require_once './middlewares/CheckTokenMiddleware.php';

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

require_once './controllers/AutenticadorController.php';
require_once './controllers/LogController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/ProductoPedidoController.php';
require_once './controllers/UsuarioController.php';

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
    $group->post('/login', \AutenticadorController::class . ':Login');
    $group->get('/mostrar', \UsuarioController::class . ':TraerUno');
    $group->get('/mostrarTodos', \UsuarioController::class . ':TraerTodos');
    $group->put('/modificarUsuario', \UsuarioController::class . ':ModificarUno');
    $group->delete('/borrarUsuario', \UsuarioController::class . ':BorrarUno');
  });

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->post('/alta', \ProductoController::class . ':CargarUno');
    $group->get('/mostrar', \ProductoController::class . ':TraerUno');
    $group->get('/mostrarTodos', \ProductoController::class . ':TraerTodos');
    $group->put('/modificarProducto', \ProductoController::class . ':ModificarUno');
    $group->delete('/borrarProducto/{id}', \ProductoController::class . ':BorrarUno');
  });

$app->group('/mesa', function (RouteCollectorProxy $group) {
    $group->post('/alta', \MesaController::class . ':CargarUno');
    $group->get('/mostrar', \MesaController::class . ':TraerUno');
    $group->get('/mostrarTodos', \MesaController::class . ':TraerTodos');
    $group->put('/modificarMesa', \MesaController::class . ':ModificarUno');
    $group->delete('/borrarMesa/{id}', \MesaController::class . ':BorrarUno');
  });

  $app->group('/pedido', function (RouteCollectorProxy $group) {
    $group->post('/alta', \PedidoController::class . ':CargarUno')->add(new CheckMozoMiddleware());
    $group->post('/tomarFotoPosterior', \PedidoController::class . ':tomarFotoPosterior')->add(new CheckMozoMiddleware());
    $group->get('/mostrarTodos', \PedidoController::class . ':TraerTodos');
    $group->get('/InformeDePedidosYDemoras',  \PedidoController::class . ':EmitirInformeDePedidosYDemoras')->add(new CheckSocioMiddleware());
    $group->put('/modificarPedido/{id}', \PedidoController::class . ':ModificarUno');
    $group->delete('/borrarPedido/{id}', \PedidoController::class . ':BorrarUno');
  });
$app->run();
