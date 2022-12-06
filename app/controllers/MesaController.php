<?php

require_once './models/Registro.php';
// use App\Models\Mesa;

require_once './interfaces/IApiUsable.php';
require_once './models/Mesa.php';

class MesaController extends Mesa implements IApiUsable
{
  public function Cerrar($request, $response, $args)
  {
    $jwtHeader = $request->getHeaderLine('Authorization');

    $id = $args['id'];
    
    if(!Mesa::CerrarMesa($id)){
      $payload = json_encode(array("mensaje" => "Mesa cerrada")); 
    }else{
      $payload = json_encode(array("mensaje" => "El cliente todavia no pago"));

    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(200);

  }



  public function CargarUno($request, $response, $args)
  {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    $parametros = $request->getParsedBody();
    $nroMesa = $parametros['nro_mesa'];

    $nuevaMesa = new Mesa();
    $nuevaMesa->nro_mesa = $nroMesa;
   // echo $nroMesa;
    $nuevaMesa->estado = "vacia";

    $mesaId = $nuevaMesa->CrearMesa();

    Registro::CrearRegistro(AutentificadorJWT::ObtenerData($token)->id, "CREAR MESA");

    $payload = json_encode(array("mensaje" => "Mesa " . $mesaId . " creada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(201);
  }

  public function TraerUno($request, $response, $args)
  {
    $jwtHeader = $request->getHeaderLine('Authorization');

    $id = $args['id'];
    $mesa = Mesa::GetMesaById($id);

    $payload = json_encode($mesa);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(200);
  }

  public function TraerMesaMasUsada($request, $response, $args)
  {
    $jwtHeader = $request->getHeaderLine('Authorization');

    $mesa = Mesa::GetMesaMasUsada();

    $payload = json_encode($mesa);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(200);
  }

  public function TraerTodos($request, $response, $args)
  {
    $jwtHeader = $request->getHeaderLine('Authorization');

    $lista = Mesa::GetAllMesas();

    //HistoricAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, "Listando todas las mesas");

    $payload = json_encode(array("mesas" => $lista));

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

  
}
