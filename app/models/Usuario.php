<?php


class Usuario {

    public $id;
    public $usuario;
    public $clave;
    public $rol;
    public $area;
    public $estado;
    public $fecha_creacion;
    public $fecha_eliminacion;

    public function setRol($rol) {
        if($rol != "SOCIO" && $rol != "MOZO" && $rol != "CHEF" && $rol != "BARTENDER" ) {
            $this->rol = "MOZO";
        } else {
            $this->rol = $rol;
        }
    }

    public function setArea($area) {
        if($area != "BARRA" && $area != "COCINA" && $area != "CANDYBAR") {
            $this->area = "CANDYBAR";
        } else {
            $this->area = $area;
        }
    }

    public function CrearUsuario() {
     
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuario (usuario, clave, rol, area, estado, fecha_creacion) 
        VALUES (:usuario, :clave, :rol, :area, :estado, :fecha_creacion)");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        $consulta->bindValue(':area', $this->area, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'ACTIVO', PDO::PARAM_STR);
        $consulta->bindValue(':fecha_creacion', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();

    }

    public static function TraerTodos() {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id,usuario,rol,area,estado,fecha_creacion FROM usuario WHERE estado = 'ACTIVO'");
            $consulta->execute();
            $list = $consulta->fetchAll();
            //var_dump($list);

            if (count($list) < 1) {
                throw new Exception("No hay ningun usuario registrado");
            }
            
            return $list;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetUsuarioById($id) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuario WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
            $usuario = $consulta->fetchObject("Usuario");
            if (is_null($usuario)) {
                throw new Exception("No existe el usuario con el id " . $id);
            }
            return $usuario;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetUsuarioByNombre($usuario) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuario WHERE usuario = :usuario");
            $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $consulta->execute();
            $usuario = $consulta->fetchObject("Usuario");
            if (is_null($usuario)) {
                throw new Exception("No existe el usuario con el usuario " . $usuario);
            }
            return $usuario;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function UpdateUser($id, $area) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE usuario SET area = :area WHERE id = :id");
            $consulta->bindValue(':area', $area, PDO::PARAM_STR);
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function EliminarUsuario($id) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE usuario SET fecha_eliminacion = NOW(), estado = :estado WHERE id = :id");            
            $consulta->bindValue(':estado', 'INACTIVO', PDO::PARAM_STR);
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);            
            $consulta->execute();
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}

?>