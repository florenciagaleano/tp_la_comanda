<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $jwtHeader = $request->getHeaderLine('Authorization');

      $parametros = $request->getParsedBody();

      $nombre = $parametros['nombre'];
      $tipo = $parametros['tipo'];
      $precio = $parametros['precio'];

      $productId = Producto::crearProducto($nombre, $tipo, $precio);

      $payload = json_encode(array("mensaje" => "Producto ". $productId ." creado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(201);
  }

    public function TraerUno($request, $response, $args)
    {
        return null;
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $id = $args['id'];
      Producto::modificarProducto($id);

      $payload = json_encode(array("mensaje" => "Producto modificado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

    public function BorrarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $id = $args['id'];
      Producto::borrarProducto($id);

      $payload = json_encode(array("mensaje" => "Producto borrado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }



}
