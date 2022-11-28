<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $rol;
    public $sector;
    public $estado;

    public function setRol($rol){
        if($rol != "BARTENDER" && $rol != "CERVECERO" &&
            $rol != "COCINERO" && $rol != "MOZO" &&
            $rol != "SOCIO"){
                $this->rol = "MOZO";
            }

        $this->rol = $rol;
    }

    public function setSector($sector){
        if($sector != "ENTRADA" && $sector != "PATIO" &&
            $sector != "COCINA" && $sector != "CANDYBAR" &&
            $sector != "SOCIO"){
                $this->sector = "CANDYBAR";
            }

        $this->sector = $sector;

    }

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuario (usuario, clave, estado, rol, sector) VALUES (:usuario, :clave, :estado, :rol, :sector)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, estado, rol, sector FROM usuario");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuario WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public function modificarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario SET usuario = :usuario, clave = :clave WHERE id = :id");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta =$objAccesoDato->RetornarConsulta("DELETE FROM usuario WHERE id=:id");
        $consulta->bindValue(':id',(int)$id, PDO::PARAM_INT);
        $consulta->execute(); 
        //echo $consulta->rowCount();
        return $consulta->rowCount();
    }
}