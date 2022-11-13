<?php

class Pedido
{
    public $id;
    public $usuario_id;
    public $mesa_id;
    public $producto_id;
    public $codigo;
    public $estado;
    public $tiempo_estimado;
    public $tiempo_total;
    public $nombreCliente;
    //public $precioTotal;
    public $numeroPedido; // les seteo el precio final a todos los q tengan este numero de pedidp
    public $precioFinal;
    public $fecha;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuario (usuario, clave, estado, rol, sector) VALUES (:usuario, :clave, :estado, :rol, :sector)");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave);
        $consulta->bindValue(':estado', $this->estado);
        $consulta->bindValue(':rol', $this->rol);
        $consulta->bindValue(':sector', $this->sector);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, tiempo_estimado, nombre_cliente, precio, fecha FROM pedido");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }


}