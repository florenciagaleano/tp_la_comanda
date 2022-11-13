<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $Pedido = $parametros['usuario_id'];
        $clave = $parametros['producto_id'];
        $estado = "PENDIENTE";
        $clave = $parametros['mesa_id'];
        $codigo = $parametros['codigo'];
        $tiempo_estimado = $parametros['tiempo_estimado'];

        $rol = $parametros['rol'];
        $sector = $parametros['sector'];

        // Creamos el Pedido
        $usr = new Pedido();
        $usr->Pedido = $Pedido;
        $usr->clave = $clave;
        $usr->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos Pedido por nombre
        $usr = $args['Pedido'];
        $Pedido = Pedido::obtenerPedido($usr);
        $payload = json_encode($Pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
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
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Pedido::modificarPedido($nombre);

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $PedidoId = $parametros['PedidoId'];
        Pedido::borrarPedido($PedidoId);

        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
