<?php

require_once './models/Usuario.php';

class Producto 
{
    public $id;
    public $nombre;
    public $tipo;
    public $precio;

    public function CrearProducto() {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO producto (nombre, tipo, precio)
        VALUES (:nombre, :tipo, :precio)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function GetAllProducts() {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM producto");
            $consulta->execute();
            $products = $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
            if (is_null($products)) {
                throw new Exception("No existen productos");
            }
            return $products;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetPendientesByArea($id) {
        try {

            $userAux = Usuario::GetUsuarioById($id);
            if($userAux != null){
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta("SELECT u.area, p.nombre from producto p inner join pedido_producto pp on p.id = pp.id_producto inner join pedido pd on pp.id_pedido = pd.id
                inner join usuario u on u.id = pd.usuario_id where u.area = :area
                and pd.estado = :estado");
                $consulta->bindValue(':area', $userAux->area, PDO::PARAM_STR);
                $consulta->bindValue(':estado', "en preparacion", PDO::PARAM_STR);
                
                $consulta->execute();
                $products = $consulta->fetchAll();
                if (is_null($products)) {
                    throw new Exception("No existen productos");
                }
                return $products;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetProductoById($id) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM producto WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
            $product = $consulta->fetchObject("Producto");
            if (is_null($product)) {
                throw new Exception("No existe el producto con el id " . $id);
            }
            return $product;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function UpdateProduct($id, $precio) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE producto SET precio = :precio WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':precio', $precio, PDO::PARAM_INT);
            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function DeleteProduct($id) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM producto WHERE id =:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }


}

?>