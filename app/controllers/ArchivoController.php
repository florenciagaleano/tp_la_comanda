<?php
require_once './models/Usuario.php';
require_once './files/manejador_archivos.php';

class ArchivoController
{
    public function VerRegistroLogin($request, $response, $args)
    {
        $lista = ManejadorArchivos::LeerCSV("registro_usuarios.csv");
        $payload = json_encode(array("registro" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
  
}
