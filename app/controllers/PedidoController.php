<?php
// use App\Models\HistoricAccions;
// use App\Models\Mesa;
// use App\Models\Product;

// use App\Models\Pedido;

require_once './interfaces/IApiUsable.php';
require_once './models/Mesa.php';
require_once './models/Producto.php';
require_once './models/Pedido.php';
require_once './models/Usuario.php';
//require_once './models/HistoricAccions.php';

class PedidoController implements IApiUsable
{
    
    public function CargarUno($request, $response, $args)
    {
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
        $parametros = $request->getParsedBody();

        $mesaId = $parametros['nro_mesa'];
        $usuarioId = $parametros['usuario_id'];
        //$productoId = $parametros['producto_id'];
        $nombreCliente = $parametros['nombre_cliente'];
        $estado = 'pendiente';

        $mesa = Mesa::GetMesaByMesaNumero($mesaId);
        $usuario = Usuario::GetUsuarioById($usuarioId);
        //$product = Producto::GetProductoById($productoId);
       // var_dump($_FILES['imagen']);

        var_dump($mesa);
        
        if(!is_null($mesa) && !is_null($usuario) && ($mesa->estado == 'vacia' || $mesa->estado == 'cerrada')){
            $nuevoPedido = new Pedido();
            $nuevoPedido->usuario_id = $usuarioId;
            //$nuevoPedido->producto_id = $productoId;
            $nuevoPedido->mesa_id = $mesa->id;
            $nuevoPedido->estado = "pendiente";
            $nuevoPedido->nro_pedido = rand(1, 100000);
            $nuevoPedido->imagen = $_FILES['imagen'];
            $nuevoPedido->nombre_cliente = $nombreCliente;

            $pedido = $nuevoPedido->CrearPedido();
            $mesa = Mesa::ActualizarEstado($nuevoPedido->mesa_id, 'con cliente esperando pedido');

            Registro::CrearRegistro(AutentificadorJWT::ObtenerData($token)->id, "CREAR PEDIDO");

            $payload = json_encode(array("mensaje" => "Pedido ". $pedido." creado con exito"));
            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json')
              ->withStatus(201);
        } else{
            $payload = json_encode(array("mensaje" => "Id Usuario o Id Producto o Id Mesa no existen o la mesa ya esta ocupada"));
        }
      
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $jwtHeader = $request->getHeaderLine('Authorization');

        $id = $args['id'];
        $pedido = Pedido::GetPedidoById($id);


        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }

    public function TraerTodos($request, $response, $args)
    {      
        $jwtHeader = $request->getHeaderLine('Authorization');

        $lista = Pedido::GetPedidos();

        //cAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, "Listando todos los pedidos");

        $payload = json_encode(array("pedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }

    public function ModificarUno($request, $response, $args) {
        return null;
    }
    
    public function ModificarEstadoPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $args['pedidoId'];
        $estado = $parametros['estado'];
        $tiempo_estimado = $parametros['tiempo_estimado'];

        //var_dump($tiempo_estimado);

        Pedido::ModificarEstado($id,$estado,$tiempo_estimado);

        $payload = json_encode(array("mensaje" => "Estado cambiado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        return null;
    }

    public function AgregarProducto($request, $response, $args) {
        $jwtHeader = $request->getHeaderLine('Authorization');
        $parametros = $request->getParsedBody();

        $pedidoId = $parametros['pedidoId'];
        $productoId = $parametros['productoId'];

        $pedido = Pedido::GetPedidoById($pedidoId);
        $product = Producto::GetProductoById($productoId);
        //var_dump($pedido);
        if(!is_null($pedido) && !is_null($product)) {

          $pedido->AgregarProducto($productoId,$pedidoId);
          //HistoricAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, ("Agregando el producto " . $product->productName . " al pedido " . strval($pedido->pedidoNumber)));
            $payload = json_encode(array("mensaje" => "Producto agregado al pedido con exito"));
            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json')
              ->withStatus(201);
        } else{
            $payload = json_encode(array("mensaje" => "Ocurrio un error al agregar el producto al pedido"));
        }
    }

    public function TraerProductosDeUnPedido($request, $response, $args) {
        $jwtHeader = $request->getHeaderLine('Authorization');

        $pedidoNumber = $args['pedidoNumber'];
        $lista = Pedido::GetPedidoByPedidoNumber($pedidoNumber);

        if(!is_null($lista)) {
          //HistoricAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, "Consultando los productos del pedido " . $pedidoNumber);
          $payload = json_encode(array("products" => $lista));
        } else {
            $payload = json_encode(array("mensaje" => "El pedido con numero de orden: " .$pedidoNumber. " no existe"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }

    public function TraerTodosSegunEstado($request, $response, $args) {
        $jwtHeader = $request->getHeaderLine('Authorization');
        $parametros = $request->getParsedBody();


        $estado = $parametros['estado'];
        $lista = Pedido::GetPedidosByStatus($estado);

        if(!is_null($lista)) {
          //HistoricAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, "Consultando los pedidos con estado " . $estado);
          $payload = json_encode(array("pedidos" => $lista));
        } else {
            $payload = json_encode(array("mensaje" => "El pedido con estado: " .$estado. " no existe"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);        
  }

  public function ConsultarTiempoRestante($request, $response, $args) {
   // $jwtHeader = $request->getHeaderLine('Authorization');
    $parametros = $request->getParsedBody();
    $nro_pedido = $parametros['nro_pedido'];
    $pedido = Pedido::GetPedidoByPedidoNumber($nro_pedido);
    //var_dump($pedido);

    if(is_null($pedido->tiempo_estimado)) {
      throw new Exception("El pedido no esta en preparacion");	
    } else {
      //HistoricAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, "Consultando el tiempo restante del pedido " . $pedidonumber);
      $payload = json_encode(array("Tiempo estimado" => $pedido->tiempo_estimado));
    } 

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(200);
  }


  public static function Cobrar($request, $response, $args) {
    
    $jwtHeader = $request->getHeaderLine('Authorization');
    $parametros = $request->getParsedBody();


    $pedidoId = $parametros['pedido_id'];
    $precio = Pedido::Cobrar($pedidoId);

    $payload = json_encode(array("mensaje" => "Precio final: " .$precio));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(200);        
}

public static function GetPedidosFueraDeTiempo($request, $response, $args) {
    
  $jwtHeader = $request->getHeaderLine('Authorization');
  $parametros = $request->getParsedBody();


  //$pedidoId = $parametros['pedido_id'];
  $pedidos = Pedido::TraerPedidosFueraDeTiempo();

  $payload = json_encode(array("pedidos" => $pedidos));

  $response->getBody()->write($payload);
  return $response
    ->withHeader('Content-Type', 'application/json')
    ->withStatus(200);        
}

}
?>