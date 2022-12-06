<?php

require_once './models/Producto.php';

require_once './interfaces/IApiUsable.php';

class ProductoController implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
    

        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $tipo = $parametros['tipo'];
        $precio = $parametros['precio'];

        $nuevoProducto = new Producto();
        $nuevoProducto->nombre = $nombre;
        $nuevoProducto->tipo = $tipo;
        $nuevoProducto->precio = $precio;

        $productId = $nuevoProducto->CrearProducto();
        //var_dump($header);
        Registro::CrearRegistro(AutentificadorJWT::ObtenerData($token)->id, "CREAR PRODUCTO");
        
        $payload = json_encode(array("mensaje" => "Producto ". $productId ." creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(201);
    }

    public function TraerUno($request, $response, $args)
    {
        $jwtHeader = $request->getHeaderLine('Authorization');

        $id = $args['id'];
        $producto = Producto::GetProductoById($id);
        
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }

    public function TraerTodos($request, $response, $args)
    {
        $jwtHeader = $request->getHeaderLine('Authorization');

        $lista = Producto::GetAllProducts();
        
        $payload = json_encode(array("products" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }
    
    public function ModificarUno($request, $response, $args)
    {
        return null;
    }

    public function BorrarUno($request, $response, $args)
    {
        return null;
    }

    public function TraerProductosPorArea($request, $response, $args)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
    
        $lista = Producto::GetPendientesByArea(AutentificadorJWT::ObtenerData($token)->id);

        $payload = json_encode(array("pendientes" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }

}