<?php

require_once './models/Producto.php';

require_once './interfaces/IApiUsable.php';

class ProductoController implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
       $jwtHeader = $request->getHeaderLine('Authorization');

        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $tipo = $parametros['tipo'];
        $precio = $parametros['precio'];

        $nuevoProducto = new Producto();
        $nuevoProducto->nombre = $nombre;
        $nuevoProducto->tipo = $tipo;
        $nuevoProducto->precio = $precio;

        $productId = $nuevoProducto->CrearProducto();

        //HistoricAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, "Creando el producto con id: " . $productId);
        
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
        $producto = Producto::GetProductById($id);
        
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
        $jwtHeader = $request->getHeaderLine('Authorization');

        $parametros = $request->getParsedBody();
        $id = $args['id'];
        $precio = $parametros['precio'];

        $productId = Product::UpdateProduct($id, $precio);

        //HistoricAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, "Listando todos los productos");


        $payload = json_encode(array("mensaje" => "Producto ". $id ." modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }

    public function BorrarUno($request, $response, $args)
    {
        $jwtHeader = $request->getHeaderLine('Authorization');
        $id = $args['id'];
        Product::DeleteProduct($id);

        $payload = json_encode(array("mensaje" => "Producto ".$id." borrado con exito"));

        //HistoricAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, "Borrando el producto con id: " . $id);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }

}