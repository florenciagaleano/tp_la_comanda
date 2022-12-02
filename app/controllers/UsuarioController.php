<?php

require_once './models/Usuario.php';
require_once './middlewares/AutentificadorJWT.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {

        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $rol = $parametros['rol'];        
        $area = $parametros['area'];

        $nuevoUsuario = new Usuario();
        $nuevoUsuario->usuario = $usuario;
        $nuevoUsuario->clave = $clave;
        $nuevoUsuario->setRol($rol);
        $nuevoUsuario->setArea($area);

        $userId = $nuevoUsuario->CrearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario ". $userId. " creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(201);
    }

    public function TraerUno($request, $response, $args)
    {
        $jwtHeader = $request->getHeaderLine('Authorization');

        $id = $args['id'];
        $usuario = Usuario::GetUsuarioById($id);

        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }

    public function TraerTodos($request, $response, $args)
    {
        $jwtHeader = $request->getHeaderLine('Authorization');

        $lista = Usuario::TraerTodos();

        $payload = json_encode(array("usuarios" => $lista));

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
        $area = $parametros['area'];
        
        Usuario::UpdateUser($id, $area);
        //HistoricAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, "Modificando el area del usuario con id: " . $id);

        $payload = json_encode(array("mensaje" => "Usuario ".$id." modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }

    public function BorrarUno($request, $response, $args)
    {
        $jwtHeader = $request->getHeaderLine('Authorization');
        
        $id = $args['id'];

        Usuario::LogicalDelete($id);

        //HistoricAccions::CreateRegistry(AutentificadorJWT::GetTokenData($jwtHeader)->id, "Borrando el usuario con id: " . $id);

        $payload = json_encode(array("mensaje" => "Usuario ".$id ." borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }

    public function Login($request, $response, $args) {
      $parametros = $request->getParsedBody();
      $user =  $parametros['usuario'];
      $clave =  $parametros['clave'];
      
      if (isset($user) && isset($clave)) {

        $usuario = Usuario::GetUsuarioByNombre($user);
        //var_dump()
        if (!empty($usuario) && ($user == $usuario->usuario) && ($clave == $usuario->clave)) {

          $jwt = AutentificadorJWT::CrearToken($usuario);

          $message = [
            'Autorizacion' => $jwt,
            'Status' => 'Login success'
          ];

          //HistoricAccions::CreateRegistry($usuario->id, "Login exitoso");
        } else {
          $message = [
            'Autorizacion' => 'Denegate',
            'Status' => 'Login failed'
          ];
        }
      }

      $payload = json_encode($message);

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function ConsultaUsuarios($request, $response, $args) {
    $consulta = $args['consulta'];

    switch($consulta) {
      case 'LogueoUsuarios':
        $lista = HistoricAccions::GetTimeLogin();
        break;
      case 'OperacionXSector':
        $lista = HistoricAccions::GetCantOperacionesPorSector();
        break;
      case 'OperacionXUsuario':
        $lista = HistoricAccions::GetCantOperacionesPorUsuario();
        break;
      case 'OperacionXEmpleado':
        $lista = HistoricAccions::GetCantOperacionesPorEmpleado();
        break;
      default:
        $lista = "Error, ingresar valor valido";
        break;
    }

    $payload = json_encode(array("consulta" => $lista));
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}

?>