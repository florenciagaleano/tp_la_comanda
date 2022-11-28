<?php
    class ManejadorArchivos{

        public static function LeerCSV($archivo){
            $file = fopen($archivo, "r");
            $devolver = array();
            while (($arAux = fgetcsv($file)) !== FALSE) 
            {
                //$userAux= new Usuario($arAux[0],$arAux[1],$arAux[2]);
                array_push($devolver, $arAux);
            }
    
            fclose($file);
            return $devolver;
        }

    }

?>
