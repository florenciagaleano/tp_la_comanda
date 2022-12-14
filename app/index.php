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

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/EncuenstaController.php';
require_once './controllers/ArchivoController.php';
require_once './middlewares/MWPermisos.php';


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
    $group->post('/crear', \UsuarioController::class . ':CargarUno')->add(\MWPermisos::class . ':VerificarSocio');
    $group->put('[/{id}]', \UsuarioController::class . ':ModificarUno')->add(\MWPermisos::class . ':VerificarSocio');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno')->add(\MWPermisos::class . ':VerificarSocio');
    $group->post('/login', \UsuarioController::class . ':Login');

});

//Productos
/*Necesita estar registrado para crear un producto*/
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->post('/crear', \ProductoController::class . ':CargarUno')->add(\MWPermisos::class . ':VerificarUsuario');
  $group->get('/productospendientesporarea', \ProductoController::class . ':TraerProductosPorArea');

});

//Mesas
/*Necesita ser mesero o socio para crear, modificar o dar de baja un producto*/
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->post('/crear', \MesaController::class . ':CargarUno')->add(\MWPermisos::class . ':VerificarMozoOSocio');
  $group->post('/cerrar/{id}', \MesaController::class . ':Cerrar')->add(\MWPermisos::class . ':VerificarSocio');
  $group->get('/masusada', \MesaController::class . ':TraerMesaMasUsada');

});


//Pedidos
/*Necesita ser chef mozo o bartender para mdificar pedidos. Para ver pedidos por estado Necesita estar registrado*/
$app->group('/pedidoss', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->post('/crear', \PedidoController::class . ':CargarUno');
  $group->delete('/{id}', \PedidoController::class . ':BorrarUno');
  $group->post('/agregarproducto', \PedidoController::class . ':AgregarProducto');
  $group->post('/estado/{pedidoId}', \PedidoController::class . ':ModificarEstadoPedido')->add(\MWPermisos::class . ':VerificarChefMozoOBartender');
  $group->get('/getbyestado', \PedidoController::class . ':TraerTodosSegunEstado')->add(\MWPermisos::class . ':VerificarUsuario');
  $group->get('/gettiemporestante', \PedidoController::class . ':ConsultarTiempoRestante');
  $group->post('/cobrar', \PedidoController::class . ':Cobrar')->add(\MWPermisos::class . ':VerificarChefMozoOBartender');
  $group->get('/pedidostarde', \PedidoController::class . ':GetPedidosFueraDeTiempo')->add(\MWPermisos::class . ':VerificarSocio');

});

$app->group('/archivos', function (RouteCollectorProxy $group) {
  $group->get('/registro', \ArchivoController::class . ':VerRegistroLogin');

});

$app->group('/encuestas', function (RouteCollectorProxy $group) {
  $group->post('/crear', \EncuestaController::class . ':CrearEncuesta');
  $group->get('/writecsv', \EncuestaController::class . ':EndpointWriteCSV');
  $group->get('/readcsv', \EncuestaController::class . ':EndpointReadCSV');
  $group->get('/pdf', \EncuestaController::class . ':EndpointCrearPDF');
  $group->get('/mejores', \EncuestaController::class . ':GetMejores');

});


$app->group('/jwt', function (RouteCollectorProxy $group) {

  $group->get('/devolverDatos', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $user = AutentificadorJWT::ObtenerData($token);
      $payload = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });
});

$app->run();


?>