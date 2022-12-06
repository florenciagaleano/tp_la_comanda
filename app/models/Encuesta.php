<?php

class Encuesta
{
    public $id;
    public $id_mesa;
    public $puntaje_total;
    public $puntaje_mozo;
    public $puntaje_chef;
    public $comentarios;

    /*public function __construct($id_mesa, $puntaje_total, $puntaje_mozo, $puntaje_chef, $comentarios)
    {
        $this->id_mesa = $id_mesa;
        $this->puntaje_total = $puntaje_total;
        $this->puntaje_mozo = $puntaje_mozo;
        $this->puntaje_chef = $puntaje_chef;
        $this->comentarios = $comentarios;
    }*/

    public static function crearEncuesta($id_mesa, $puntaje_total, $puntaje_mozo, $puntaje_chef, $comentarios) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuesta (id_mesa, puntaje_total, puntaje_mozo, puntaje_chef, comentarios)
        VALUES (:id_mesa, :puntaje_total, :puntaje_mozo, :puntaje_chef, :comentarios)");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_total', $puntaje_total, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_mozo', $puntaje_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_chef', $puntaje_chef, PDO::PARAM_INT);
        $consulta->bindValue(':comentarios', $comentarios, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }


    public static function GetEncuestas() {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuesta");
            $consulta->execute();
            $survies = $consulta->fetchAll(PDO::FETCH_CLASS, "Encuesta");
            if (is_null($survies)) {
                throw new Exception("No existen encuestas");
            }

            return $survies;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function GetMesaMejorYPeorComentario($orderBy) {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM surveys ORDER BY puntaje_total " . $orderBy . " LIMIT 1");
            $consulta->execute();
            $survies = $consulta->fetchObject("Survery");
            if (is_null($survies)) {
                throw new Exception("No existen encuestas");
            }            
            return $survies;
        } catch (Exception $e) {
            return $e->getMessage();
        }             
     }
    
}
