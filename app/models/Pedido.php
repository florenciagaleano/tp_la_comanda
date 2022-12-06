<?php

// namespace App\Models;
require_once 'Mesa.php';

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

    public static function AgregarProducto($idProducto, $idPedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido_producto (id_pedido,id_producto) 
        VALUES (:id_pedido, :id_producto)");
        $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_INT);
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
            }else if($estado == 'listo para servir'){
                $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET estado = :estado, fecha_finalizacion = NOW() WHERE id = :id");
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
                //$consulta->bindValue(':fecha', $id, PDO::PARAM_INT);
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->execute();
                Mesa::ActualizarEstado((Pedido::GetPedidoById($id))->mesa_id, "con cliente comiendo");
            }
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

    public static function GetPedidoByMesa($nro_pedido, $tableNumber) {
        
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE nro_pedido = :nro_pedido AND mesa_id = :mesa_id");
            $consulta->bindValue(':nro_pedido', $nro_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':mesa_id', $tableNumber, PDO::PARAM_INT);
            $consulta->execute();
            $pedido = $consulta->fetchObject("Pedido");            
            if (!$pedido) {                
                throw new Exception("No existe el pedido con la mesa " . $tableNumber);
            } 
            return $pedido;
        
    }

    public static function GetPedidoByPedidoNumber($nro_pedido) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE nro_pedido = :nro_pedido");
            $consulta->bindValue(':nro_pedido', $nro_pedido, PDO::PARAM_STR);
            $consulta->execute();
            $pedidos = $consulta->fetchObject("Pedido");
            if (is_null($pedidos)) {
                throw new Exception("No existe el pedido con el numero de pedido " . $nro_pedido);
            }
            return $pedidos;
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

    public static function Cobrar($idPedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT SUM(p.precio) from producto p inner join pedido_producto pp on p.id = pp.id_producto
        inner join pedido pd on pp.id_pedido = pd.id where pd.id = :id");
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_STR);
        $consulta->execute();
        //var_dump($consulta->fetchColumn());
        $precio = intval($consulta->fetchColumn());

        Pedido::setPrecioFinal($precio, $idPedido);
        //var_dump((Pedido::GetPedidoById($idPedido))->mesa_id);
        Mesa::ActualizarEstado((Pedido::GetPedidoById($idPedido))->mesa_id, "con cliente pagando");

        return $precio;
}

    private static function setPrecioFinal($precio,$idPedido){
        //var_dump($idPedido);
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido SET precio_final = :precio_final WHERE id = :id");
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':precio_final', $precio, PDO::PARAM_STR);

        $consulta->execute();
    }

    public static function TraerPedidosFueraDeTiempo() {
        $list = array();
        $pedidos = Pedido::GetPedidos();
        foreach ($pedidos as $pedido) {
          $tiempo_estimado = intval($pedido->tiempo_estimado);
    
          //calculo la diferencia en minutos entre fecha_creacion y finishedAT
          $fecha_creacion = new DateTime($pedido->fecha_creacion);
          $fecha_finalizacion = new DateTime($pedido->fecha_finalizacion);
          $diff = $fecha_creacion->diff($fecha_finalizacion);
          //paso la diferencia de horas a minutos
          $minutes = $diff->h * 60 + $diff->i;
          if($fecha_finalizacion != null && $tiempo_estimado != null &&$minutes > $tiempo_estimado) {
            array_push($list, $pedido);
          }
        }
        return $list;
      }
    
}

?>