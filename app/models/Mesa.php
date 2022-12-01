<?php

// namespace App\Models;

// use Exception;
// use Illuminate\Database\Eloquent\Model;

class Mesa  {

    public $id;
    public $nro_mesa;
    public $estado;

    public function setEstado($estado) {
    if($estado != "vacia" && $estado != "con cliente esperando pedido" && $estado != "con cliente comiendo" && $estado != "con cliente pagando"
        && $estado != "cerrada") {
        $this->estado = "cerrada";
    } else {
        $this->estado = $estado;
    }
}

    public function CrearMesa() {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesa (nro_mesa, estado) VALUES (:nro_mesa, :estado)");
            $consulta->bindValue(':nro_mesa', $this->nro_mesa, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetAllMesas() {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesa WHERE estado != 'DELETED'");
            $consulta->execute();
            $mesas = $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
            if (is_null($mesas)) {
                throw new Exception("No existen mesas");
            }
            return $mesas;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetMesaById($id) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesa WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
            $mesa = $consulta->fetchObject("Mesa");
            if (is_null($mesa)) {
                throw new Exception("No existe la mesa con el id " . $id);
            }
            return $mesa;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetMesaByMesaNumero($nro_mesa) {
        try {            
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id,nro_mesa,estado FROM mesa WHERE nro_mesa = :nro_mesa");
            $consulta->bindValue(':nro_mesa', $nro_mesa, PDO::PARAM_INT);
            $consulta->execute();
            $mesa = $consulta->fetchObject("Mesa");
            if (is_null($mesa)) {
                throw new Exception("No existe la mesa con el nro_mesa " . $nro_mesa);
            }
            return $mesa;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function ActualizarEstado($nro_mesa, $estado) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesa SET estado = :estado WHERE nro_mesa = :nro_mesa");
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->bindValue(':nro_mesa', $nro_mesa, PDO::PARAM_INT);
            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function DeleteMesa($id) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesa set estado = 'DELETED' WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}

?>