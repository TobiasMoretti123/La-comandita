<?php

class Usuario
{
    public $id;
    public $nombre;
    public $clave;
    public $perfil;
    public $fechaAlta;
    public $fechaBaja;

    public function AltaUsuario()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $fecha = date('y/m/d');
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into tabla_usuarios (nombre,clave,perfil,fechaAlta)  
        VALUES (:nombre, :clave, :perfil,:fechaAlta)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':fechaAlta', $fecha, PDO::PARAM_STR);
        $consulta->execute();

        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function ObtenerUsuarioPorNombre($nombre)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_usuarios WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function ObtenerUsuarioPorId($id)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_usuarios WHERE id = :id and fechaBaja is null");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function LeerUsuarios()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * from tabla_usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }   
}