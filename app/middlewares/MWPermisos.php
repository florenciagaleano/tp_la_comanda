<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


class MWPermisos
{
    public static function VerificarPedido(Request $request, RequestHandler $handler)
    {
        $jwtHeader = $request->getHeaderLine('Authorization');
        $parametros = $request->getParsedBody();
        $pedido_id = $parametros['pedido_id'];

        try {
            $user = AutentificadorJWT::ObtenerData($jwtHeader);

            $order = Pedido::obtenerPedidoPorId($pedido_id);

            if ($user->id != $order->userId || $order == null) {
                throw new Exception("A este usuario no le corresponde el pedido ingresado.");
            }

            $response = $handler->handle($request);

            return $response;
        } catch (Exception $e) {

            $response = new Response();

            $payload = json_encode(array('Error: ' => $e->getMessage()));

            $response->getBody()->write($payload);

            return $response
            ->withHeader('Content-Type', 'application/json');;
        }
    }

    public static function VerificarSocio(Request $request, RequestHandler $handler) {
        $jwtHeader = $request->getHeaderLine('Authorization');
        $response = new Response();

        try {
            $user = AutentificadorJWT::ObtenerData($jwtHeader);

            if (strtoupper($user->rol) == 'SOCIO') {                
                $response = $handler->handle($request);
                $response = $response->withStatus( 200 );
            } else {                
                throw new Exception("El usuario no es socio.");
            }
        } catch (Exception $e) {         
            $payload = json_encode(array('Error: ' => $e->getMessage()));
            $response->getBody()->write($payload);
            $response = $response->withStatus( 401 );
        }
        return $response->withHeader('Content-Type', 'application/json');;
    }

    public static function VerificarUsuario(Request $request, RequestHandler $handler) {
        $jwtHeader = $request->getHeaderLine('Authorization');
        $response = new Response();

        try {
            $user = AutentificadorJWT::ObtenerData($jwtHeader);

            if (strtoupper($user->rol) != null) {                
                $response = $handler->handle($request);
                $response = $response->withStatus( 200 );
            } else {                
                throw new Exception("Falla en autentificacion.");
            }
        } catch (Exception $e) {         
            $payload = json_encode(array('Error: ' => $e->getMessage()));
            $response->getBody()->write($payload);
            $response = $response->withStatus( 401 );
        }
        return $response->withHeader('Content-Type', 'application/json');;
    }

    public static function VerificarMozoOSocio(Request $request, RequestHandler $handler) {
        $jwtHeader = $request->getHeaderLine('Authorization');        
        $response = new Response();

        try {
            $user = AutentificadorJWT::ObtenerData($jwtHeader);

            if (strtoupper($user->rol) == 'MOZO' || strtoupper($user->rol) == 'SOCIO') {                
                $response = $handler->handle($request);
                $response = $response->withStatus( 200 );
            } else {                
                throw new Exception("El usuario no es mesero ni socio.");
            }
        } catch (Exception $e) {         
            $payload = json_encode(array('Error: ' => $e->getMessage()));
            $response->getBody()->write($payload);
            $response = $response->withStatus( 401 );
        }
        return $response->withHeader('Content-Type', 'application/json');;
    }

    public static function VerificarChefMozoOBartender(Request $request, RequestHandler $handler) {
        $jwtHeader = $request->getHeaderLine('Authorization');        
        $response = new Response();

        try {
            $user = AutentificadorJWT::ObtenerData($jwtHeader);

            if (strtoupper($user->rol) == 'MOZO' || strtoupper($user->rol) == 'CHEF' || strtoupper($user->rol) == 'BARTENDER') {                
                $response = $handler->handle($request);
                $response = $response->withStatus( 200 );
            } else {                
                throw new Exception("El usuario no es mozo, bartender ni chef.");
            }
        } catch (Exception $e) {         
            $payload = json_encode(array('Error: ' => $e->getMessage()));
            $response->getBody()->write($payload);
            $response = $response->withStatus( 401 );
        }
        return $response->withHeader('Content-Type', 'application/json');;
    }


}
