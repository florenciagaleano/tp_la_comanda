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
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/Logger.php';

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

/*
❖ Dar de alta y listar usuarios(mozo, bartender...)
❖ Dar de alta y listar productos(bebidas y comidas)
❖ Dar de alta y listar mesas
❖ Dar de alta y listar pedidos
 */

//Usuarios
/*Necesita permisos de SOCIO para crear. modificar o dar de baja un usuario*/
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class .  ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(\MWPermisos::class . ':VerificarSocio');
    $group->put('[/{id}]', \UsuarioController::class . ':ModificarUno')->add(\MWPermisos::class . ':VerificarSocio');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno')->add(\MWPermisos::class . ':VerificarSocio');
    $group->post('/login', \UsuarioController::class . ':Login');
});

//Productos
/*Necesita estar registrado para crear, modificar o dar de baja un producto*/
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->post('[/]', \ProductoController::class . ':CargarUno')->add(\MWPermisos::class . ':VerificarUsuario');
  $group->put('[/{id}]', \ProductoController::class . ':ModificarUno')->add(\MWPermisos::class . ':VerificarUsuario');
  $group->delete('/{id}', \ProductoController::class . ':BorrarUno')->add(\MWPermisos::class . ':VerificarUsuario');
});

//Mesas
/*Necesita ser mesero o socio para crear, modificar o dar de baja un producto*/
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(\MWPermisos::class . ':VerificarMozoOSocio');
  $group->put('[/{id}]', \MesaController::class . ':ModificarUno')->add(\MWPermisos::class . ':VerificarMozoOSocio');
  $group->delete('/{id}', \MesaController::class . ':BorrarUno')->add(\MWPermisos::class . ':VerificarMozoOSocio');
});

//Pedidos
/*Necesita ser chef mozo o bartender para mdificar pedidos. Para ver pedidos por estado ecesita estar registrado*/
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->post('/crear', \PedidoController::class . ':CargarUno');
  $group->delete('/{id}', \PedidoController::class . ':BorrarUno');
  $group->post('/{pedidoId}/producto/{productoId}', \PedidoController::class . ':AgregarProducto')->add(\MWPermisos::class . ':VerificarChefMozoOBartender');
  $group->post('/estado/{pedidoId}', \PedidoController::class . ':ModificarEstadoPedido')->add(\MWPermisos::class . ':VerificarChefMozoOBartender');
  $group->get('[/estado]', \PedidoController::class . ':TraerPorEstado')->add(\MWPermisos::class . ':VerificarUsuario');

});

$app->run();


?>