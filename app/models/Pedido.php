<?php

// namespace App\Models;
//require_once './Table.php';

// use Exception;
// use Illuminate\Database\Eloquent\Model;


class Pedido
{

    public $id;
    public $mesa_id;
    public $usuario_id;
    //public $producto_id;
    public $estado;
    public $fecha_creacion;
    public $tiempo_estimado;
    public $precio_final;
    public $nro_pedido;
    public $imagen;
    public $fecha_finalizacion;
    public $nombre_cliente;

    public function CrearPedido() {        
        try {
            $imagenGuardar = $this->imagen;
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            if($this->imagen != null) {
                $imagenGuardar = $this->GuardarImagen();            
            }
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido (mesa_id, usuario_id, estado, fecha_creacion, precio_final, nro_pedido, imagen, nombre_cliente) 
            VALUES (:mesa_id, :usuario_id, :estado, NOW(), 0, :nro_pedido, :imagen, :nombre_cliente)");
            $consulta->bindValue(':mesa_id', $this->mesa_id, PDO::PARAM_INT);
            $consulta->bindValue(':usuario_id', $this->usuario_id, PDO::PARAM_INT);
           // $consulta->bindValue(':producto_id', $this->producto_id, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue('nro_pedido',$this->nro_pedido , PDO::PARAM_INT);
            $consulta->bindValue(':imagen', $imagenGuardar, PDO::PARAM_STR);
            $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);

            try{
                $consulta->execute();

            }catch(Exception $e){
                echo $e->getMessage();
            }
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function AgregarProducto($idProducto){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido_producto (id_pedido,id_producto) 
        VALUES (:id_pedido, :id_producto)");
        $consulta->bindValue(':id_pedido', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $idProducto, PDO::PARAM_INT);

        try{
            $consulta->execute();

        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public static function GetPedidos() {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE estado != 'CANCELADO'");
            $consulta->execute();
            $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
            if (is_null($pedidos)) {
                throw new Exception("No existen pedidos");
            }
            return $pedidos;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function ModificarEstado($id,$estado,$tiempo_estimado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        if($estado == 'en preparacion' || $estado == 'listo para servir' || $estado == 'pagado'){
            if($tiempo_estimado != null){
                $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET estado = :estado, tiempo_estimado = :tiempo_estimado WHERE id = :id");
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
                $consulta->bindValue(':tiempo_estimado', (int)$tiempo_estimado, PDO::PARAM_INT);
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->execute();
            }else{
                $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET estado = :estado WHERE id = :id");
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->execute();
            }
        }

        if($estado == 'listo para servir'){
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET fecha_finalizacion = :fecha_finalizacion WHERE id = :id");
            $consulta->bindValue(':fecha_finalizacion', date("Y-m-d H:i:s"), PDO::PARAM_STR);
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
        }
    }

    public static function GetPedidoById($id) {
        $id = intval($id);
        //var_dump($id);

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE id = :id");
        $consulta->bindValue(':id', 1, PDO::PARAM_INT);
        $consulta->execute();
        //var_dump($consulta->fetchObject("Pedido"));
        return $consulta->fetchObject("Pedido");
    }

    public static function GetPedidosByStatus($estado) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE estado = :estado");
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->execute();
            $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
            if (is_null($pedidos)) {
                throw new Exception("No existen pedidos con el estado " . $estado);
            }
            return $pedidos;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetPedidoByTableNumber($nro_pedido, $tableNumber) {
        
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE nro_pedido = :nro_pedido AND mesa_id = :mesa_id");
            $consulta->bindValue(':nro_pedido', $nro_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':mesa_id', $tableNumber, PDO::PARAM_INT);
            $consulta->execute();
            $order = $consulta->fetchObject("Pedido");            
            if (!$order) {                
                throw new Exception("No existe el pedido con la mesa " . $tableNumber);
            } 
            return $order;
        
    }

    public static function GetPedidoByPedidoNumber($nro_pedido) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE nro_pedido = :nro_pedido");
            $consulta->bindValue(':nro_pedido', $nro_pedido, PDO::PARAM_STR);
            $consulta->execute();
            $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
            if (is_null($pedidos)) {
                throw new Exception("No existe el pedido con el numero de pedido " . $nro_pedido);
            }
            return $pedidos;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetTableNumberMoreAndLessPrice($orderBy){
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT mesa_id, SUM(precio_final) AS total 
            FROM pedido WHERE estado != 'CANCELADO' 
            GROUP BY mesa_id 
            ORDER BY total " . $orderBy . " LIMIT 1");    
            $consulta->execute();
            $table = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
            if (is_null($table)) {
                throw new Exception("No existen pedidos");
            }            
            return $table[0]->mesa_id;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetTableNumberMoreFinalPrice() {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT mesa_id FROM pedido ORDER BY precio_final DESC LIMIT 1;");
            $consulta->execute();
            $table = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
            if (is_null($table)) {
                throw new Exception("No existen pedidos");
            }            
            return $table[0]->mesa_id;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetTableNumberLessFinalPrice() {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT mesa_id
            FROM pedido
            WHERE estado != 'CANCELADO' AND precio_final > 0
            ORDER BY precio_final ASC LIMIT 1;");
            $consulta->execute();
            $table = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
            if (is_null($table)) {
                throw new Exception("No existen pedidos");
            }            
            return $table[0]->mesa_id;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetPedidosBetweenDates($startDate, $endDate) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ordpedidoers WHERE fecha_finalizacion BETWEEN :startDate AND :endDate");
            $consulta->bindValue(':startDate', $startDate, PDO::PARAM_STR);
            $consulta->bindValue(':endDate', $endDate, PDO::PARAM_STR);
            $consulta->execute();
            $tables = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
            if (is_null($tables)) {
                throw new Exception("No existen pedidos");
            }
            return $tables;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function UpdateUserAndTable($id, $mesa_id, $usuario_id, $newFileName) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido SET mesa_id = :mesa_id, usuario_id = :usuario_id, imagen = :imagen WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':mesa_id', $mesa_id, PDO::PARAM_INT);
            $consulta->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $consulta->bindValue(':imagen', $newFileName, PDO::PARAM_STR);
            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function UpdatePedidoChef($nro_pedido, $estado, $tiempo_estimado) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido SET estado = :estado, tiempo_estimado = :tiempo_estimado WHERE nro_pedido = :nro_pedido");            
            $consulta->bindValue(':nro_pedido', $nro_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->bindValue(':tiempo_estimado', $tiempo_estimado, PDO::PARAM_STR);
            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function UpdatePedidoWaitress($nro_pedido, $estado) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido SET estado = :estado,
            fecha_finalizacion = :fecha_finalizacion WHERE nro_pedido = :nro_pedido");
            $consulta->bindValue(':nro_pedido', $nro_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_finalizacion', date("Y-m-d H:i:s"), PDO::PARAM_STR);
            $consulta->execute();
    } catch (Exception $e) {
            return $e->getMessage();
        }
    }
 
    public static function SetPrice($nro_pedido, $precio_final) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido SET precio_final = :precio_final WHERE nro_pedido = :nro_pedido");
            $consulta->bindValue(':nro_pedido', $nro_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':precio_final', $precio_final, PDO::PARAM_STR);
            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetTableWithMoreAndLessPedidos($orderBy) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT mesa_id, COUNT(*) AS pedido 
            FROM pedido GROUP BY mesa_id ORDER BY pedido ". $orderBy. " LIMIT 1");
            $consulta->execute();
            $table = $consulta->fetchObject("Table");
            if (is_null($table)) {
                throw new Exception("No existen mesas con pedidos");
            }
            
            return $table;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function GuardarImagen() {
        $mover =  move_uploaded_file($this->imagen["tmp_name"], $this->CrearDestino());

        if ($mover) {
            return true;
        }
        return false;
    }

    private function CrearDestino(){
        mkdir("Pedidos");
        $destino = "Pedidos/" . $this->nro_pedido . ".jpg";
        return $destino;
    }


    public static function FindAndChangePictureName($actualDir, $nro_pedido, $mesa_id) {
        try {
            $newFileName = $nro_pedido . "_" . $mesa_id;
            $newDir = "./images/".$newFileName.".jpg";
            rename($actualDir, $newDir);            
            return $newDir;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}

?>