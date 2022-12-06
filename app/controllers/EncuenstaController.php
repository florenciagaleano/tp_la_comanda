<?php

require_once './models/Encuesta.php';
use Fpdf\Fpdf;

class EncuestaController {

    public function EndpointWriteCSV($request, $response, $args) {
        
        $encuestas = Encuesta::GetEncuestas();

        if( (count($encuestas) > 0) && EncuestaController::WriteCSV($encuestas)) {
            $payload = json_encode(array("encuestas" => $encuestas));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        } else {
            $payload = json_encode(array("ERROR" => "No se pudo guardar el archivo"));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    private static function WriteCSV($fileContent, $nombreArchivo = "./encuestas.csv") {
        $directory = dirname($nombreArchivo, 1);
        $success = false;

        try {
            $file = fopen($nombreArchivo, "w");
            if ($file) {
                foreach ($fileContent as $entity) {
                    var_dump($entity);
                    $line = $entity->id . "," . $entity->id_mesa . "," . $entity->puntaje_total . "," . $entity->puntaje_mozo . "," . $entity->puntaje_chef . "," . $entity->comentarios . PHP_EOL;
                    fwrite($file, $line);
                    $success = true;
                }
            }
        } catch (\Throwable $th) {
            echo "No se pudo guardar el archivo";
        }finally{
            fclose($file);
        }

        return $success;
    }

    public static function EndpointReadCSV($request, $response, $args){
        $arrayFile = EncuestaController::ReadCSV();

        $payload = json_encode(array("encuestas" => $arrayFile));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    private static function ReadCSV($filename="./encuestas.csv"){
        $file = fopen($filename, "r");
        $datos = array();
       
        if (file_exists($filename) && filesize($filename) > 0) {

            $file = fopen($filename, "r");

            while(!feof($file)) {
                $linea = fgets($file);

                if(!empty($linea)){
                 
                    $linea = str_replace(PHP_EOL, "", $linea);
                    $csvData = explode(",", $linea);
                    $e = new Encuesta();
                    $e->id = intval($csvData[0]);
                    $e->puntaje_total = intval($csvData[2]);
                    $e->puntaje_mozo = intval($csvData[3]);
                    $e->puntaje_chef = intval($csvData[4]);
                    $e->id_mesa = intval($csvData[1]);
                    $e->comentarios = $csvData[5];
                    array_push($datos, $e);
                }
            }

            fclose($file);
        }
        return $datos;
    }

    public static function CrearEncuesta($request, $response, $args) {

        $parametros = $request->getParsedBody();
        $pedidoId = $parametros["pedido_id"];
        $pedido = Pedido::GetPedidoById($pedidoId);
        //var_dump($pedido);

        if($pedido == null){
            $payload = json_encode(array("ERROR" => "No se encontro la orden"));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        } else{
            $puntaje_total = $parametros["puntaje_total"];
            $puntaje_mozo = $parametros["puntaje_mozo"];
            $puntaje_chef = $parametros["puntaje_chef"];
            $comentarios = $parametros["comentarios"];

            $encuesta = new Encuesta();
            $encuesta = $encuesta->CrearEncuesta($pedido->mesa_id, $puntaje_total, $puntaje_mozo, $puntaje_chef, $comentarios);

            $payload = json_encode(array("mensaje" => "Encuesta ". $encuesta." creada con exito! Gracias"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    
    }
    


    public static function EndpointCrearPDF ($request, $response, $args) {
        
        $pdf = new FPDF('P', 'mm', 'A3');
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(30,10,'Encuestas');
        $pdf->Ln();
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(30,10,'ID');
        $pdf->Cell(30,10,'ID Mesa');
        $pdf->Cell(30,10,'Puntaje Total');
        $pdf->Cell(30,10,'Puntaje Mozo');
        $pdf->Cell(30,10,'Puntaje Chef');
        $pdf->Cell(30,10,'Comentarios');
        $pdf->Ln();
        $pdf->SetFont('Arial','',12);

        $arrayFile = EncuestaController::ReadCSV();

        foreach ($arrayFile as $entity) {
            $pdf->Cell(30,10,$entity->id);
            $pdf->Cell(30,10,$entity->id_mesa);
            $pdf->Cell(30,10,$entity->puntaje_total);
            $pdf->Cell(30,10,$entity->puntaje_mozo);
            $pdf->Cell(30,10,$entity->puntaje_chef);
            $pdf->Cell(30,10,$entity->comentarios);
            $pdf->Ln();
        }
        $pdf->Output('I', 'encuestas.pdf');

        return $response->withHeader('Content-Type', 'application/pdf')
        ->withStatus(201);
    }

    private static function ValidateScores($score) {
        return $score > 0 && $score < 11;
    }

   

}
