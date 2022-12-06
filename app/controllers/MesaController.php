<?php

require_once './models/Registro.php';
// use App\Models\Mesa;

require_once './interfaces/IApiUsable.php';
require_once './models/Mesa.php';

class MesaController extends Mesa implements IApiUsable
{
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
/*
  public function ConsultaMesas($request, $response, $args)
  {
    $consulta = $args['consulta'];
    $payload = "";

    switch ($consulta) {
      case 'MesaMasUsada':
        $idMesaMasUsada = Order::GetMesaWithMoreAndLessOrders("DESC");
        $mesa = Mesa::GetMesaByMesaNumber($idMesaMasUsada->mesa_id);
        $payload = json_encode(array("mesa" => $mesa));
        break;
      case 'MesaMenosUsada':
        $idMesaMasUsada = Order::GetMesaWithMoreAndLessOrders("ASC");
        $mesa = Mesa::GetMesaByMesaNumber($idMesaMasUsada->mesa_id);
        $payload = json_encode(array("mesa" => $mesa));
        break;
      case 'MesaMejoresComentarios':        
        $idMesaBestScore = Survery::GetMesaMejorYPeorComentario("DESC");
        $mesa = Mesa::GetMesaByMesaNumber($idMesaBestScore->id_mesa);
        $payload = json_encode(array("mesa" => $mesa));
        break;
      case 'MesaPeoresComentarios':
        $idMesaBestScore = Survery::GetMesaMejorYPeorComentario("ASC");
        $mesa = Mesa::GetMesaByMesaNumber($idMesaBestScore->id_mesa);
        $payload = json_encode(array("mesa" => $mesa));
        break;
      case 'MasFacturo':
        $mesaMasFacturo = Order::GetMesaNumberMoreAndLessPrice("DESC");
        $mesa = Mesa::GetMesaByMesaNumber($mesaMasFacturo);
        $payload = json_encode(array("mesa" => $mesa));
        break;
      case 'MenosFacturo':
        $mesaMenosFacturo = Order::GetMesaNumberMoreAndLessPrice("ASC");
        $mesa = Mesa::GetMesaByMesaNumber($mesaMenosFacturo);
        $payload = json_encode(array("mesa" => $mesa));
        break;
      case 'MayorImporte':
        $mesaMaxImporte = Order::GetMesaNumberMoreFinalPrice();
        $mesa = Mesa::GetMesaByMesaNumber($mesaMaxImporte);
        $payload = json_encode(array("mesa" => $mesa));
        break;
      case 'MenorImporte':
        $mesaMinImporte = Order::GetMesaNumberLessFinalPrice();
        $mesa = Mesa::GetMesaByMesaNumber($mesaMinImporte);
        $payload = json_encode(array("mesa" => $mesa));
        break;
      default:
        $lista = "Error, ingresar valor valido";
        break;
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(200);
  }


  public function ConsultaMesasFecha($request, $response, $args)
  {
    $mesas = array();

    $fechaInicio = date($args['fechaInicio']);
    $fechaFin = date($args['fechaFin']);
    $mesaImporteEntreDosFechas = Order::GetOrdersBetweenDates($fechaInicio, $fechaFin);    
    
    for ($i=0; $i < count($mesaImporteEntreDosFechas) ; $i++) { 
      $mesa = Mesa::GetMesaByMesaNumber($mesaImporteEntreDosFechas[$i]->mesa_id);
      array_push($mesas, $mesa);
    }

    $payload = json_encode(array("mesas" => $mesas));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(200);
  }*/
}
