<?php

class Producto
{
    public $id;
    public $nombre;
    public $precio;

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO producto (nombre, precio) VALUES (:nombre, :precio)");
        $consulta->bindValue(':nombre', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->clave);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT nombre,precio FROM producto");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

}