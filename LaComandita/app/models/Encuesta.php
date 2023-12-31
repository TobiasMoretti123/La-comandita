<?php

class Encuesta
{
    public $id;
    public $idMesa;
    public $puntuacionMesa;
    public $idMozo;
    public $puntuacionMozo;
    public $puntuacionRestaurante;
    public $idCocinero;
    public $puntuacionCocinero;
    public $comentarios;
    

    public function crearEncuesta()
    {    
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("INSERT INTO tabla_encuentas (idMesa, puntuacionMesa, idMozo, puntuacionMozo, 
        puntuacionRestaurante, idCocinero, puntuacionCocinero, comentarios ) VALUES (:idMesa, :puntuacionMesa, :idMozo, 
        :puntuacionMozo,:puntuacionRestaurante, :idCocinero, :puntuacionCocinero, :comentarios)");
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionMesa', $this->puntuacionMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionMozo', $this->puntuacionMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionRestaurante', $this->puntuacionRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':idCocinero', $this->idCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionCocinero', $this->puntuacionCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':comentarios', $this->comentarios, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public static function InformarMejoresComentarios()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_encuentas
        WHERE puntuacionMesa >= 7 AND puntuacionMozo >= 7 AND puntuacionRestaurante  >= 7 AND puntuacionCocinero >= 7");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function InformarPeoresComentarios()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_encuentas
        WHERE puntuacionMesa < 7 AND puntuacionMozo < 7 AND puntuacionRestaurante  < 7 AND puntuacionCocinero < 7");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }
}
?>