<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario_id = $parametros['usuarioId'];
        $productoId = $parametros['productoId'];
        $estado = "PENDIENTE";
        $mesa_id = $parametros['mesaId'];
        $codigo = $parametros['codigo'];
        $tiempo_estimado = $parametros['tiempo_estimado'];
        $nombre_cliente = $parametros['nombre_cliente'];


        // Creamos el Pedido
        $pedido = new Pedido();
        $pedido->usuario_id = $usuario_id;
        $pedido->productoId = $productoId;
        $pedido->estado = $estado;
        $pedido->mesa_id = $mesa_id;
        $pedido->codigo = $codigo;
        $pedido->tiempo_estimado = $tiempo_estimado;
        $pedido->nombre_cliente = $nombre_cliente;

        $pedido->crearPedido();
        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
       /* // Buscamos Pedido por nombre
        $usr = $args['Pedido'];
        $Pedido = Pedido::obtenerPedido($usr);
        $payload = json_encode($Pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');*/
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        return null;
    }

    public function ModificarEstadoPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $args['id'];
        $estado = $parametros['estado'];
        Pedido::modificarEstado($id,$estado);

        $payload = json_encode(array("mensaje" => "Estado cambiado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $args['id'];
        Pedido::borrarPedido($id);

        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function AgregarProducto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $pedido_id = $args['pedidoId'];
        $producto_id = $args['productoId'];

        $pedido = Pedido::obtenerPedidoPorId($pedido_id);
        $product = Producto::obtenerProductoPorId($producto_id);

        if(!is_null($pedido) && !is_null($product)) {
            $pedido = Pedido::crearPedido($pedido->table_id, $pedido->user_id, $product->id, $pedido->status, $pedido->pedidoNumber, $pedido->picture);
            
            //HistoricAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, "Agregando el producto " . $product->productName . " al pedido " . $order->orderNumber);
            $payload = json_encode(array("mensaje" => "Producto agregado al pedido con exito"));
            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json')
              ->withStatus(201);
        } else{
            $payload = json_encode(array("mensaje" => "Ocurrio un error al agregar el producto al pedido"));
        }
    }

    public function TraerPorEstado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $estado = $args['estado'];
        $lista = Pedido::obtenerPedidosPorEstado($estado);

        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
