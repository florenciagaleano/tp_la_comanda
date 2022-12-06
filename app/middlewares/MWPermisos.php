<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


class MWPermisos
{

    public static function VerificarSocio(Request $request, RequestHandler $handler) {
        $jwtHeader = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $jwtHeader)[1]);
     //var_dump(AutentificadorJWT::ObtenerData($token));
        $response = new Response();

        try {
            $user = AutentificadorJWT::ObtenerData($token);
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
        $token = trim(explode("Bearer", $jwtHeader)[1]);

        $response = new Response();

        try {
            $user = AutentificadorJWT::ObtenerData($token);

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
        $token = trim(explode("Bearer", $jwtHeader)[1]);
 
        $response = new Response();

        try {
            $user = AutentificadorJWT::ObtenerData($token);

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
        $token = trim(explode("Bearer", $jwtHeader)[1]);
   
        $response = new Response();

        try {
            $user = AutentificadorJWT::ObtenerData($token);

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
