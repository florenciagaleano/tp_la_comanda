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
    public $nombre_cliente;
    //public $precioTotal;
    public $numero_pedido; // les seteo el precio final a todos los q tengan este numero de pedidp
    public $precioFinal;
    public $fecha;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido (estado, usuario_id, mesa_id, producto_id, codigo,tiempo_estimado,nombre_cliente,fecha,numero_pedido) VALUES (:estado, :usuario_id, :mesa_id, :producto_id, :codigo,:tiempo_estimado,:nombre_cliente,:fecha,:numero_pedido)");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':usuario_id', $this->usuario_id);
        $consulta->bindValue(':mesa_id', $this->mesa_id);
        $consulta->bindValue(':producto_id', $this->producto_id);
        $consulta->bindValue(':codigo', $this->codigo);
        $consulta->bindValue(':tiempo_estimado', $this->tiempo_estimado);
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente);
        $consulta->bindValue(':fecha', (new DateTime('now'))->format('Y-m-d'));
        $consulta->bindValue(':numero_pedido', rand(1,10000));

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