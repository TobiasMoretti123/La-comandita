<?php

class ProductoPedido
{
    public $id;
    public $codigoPedido;
    public $idEmpleado;
    public $perfil;
    public $idProducto;
    public $cantidad;
    public $horarioPautado;
    public $estado;

    public function crearProductoPedido()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objAccesoDatos->RetornarConsulta("INSERT INTO tabla_productospedidos
        (codigoPedido, perfil, idProducto, cantidad, estado)
        VALUES (:codigoPedido, :perfil, :idProducto, :cantidad, :estado)");
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT); 
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_productospedidos 
        WHERE estado != 'cancelado'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }

    public static function modificarProductoPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("UPDATE tabla_productospedidos
        SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', $pedido->estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_STR);

        $consulta->execute();
    }

    public static function borrarProductoPedido($id)
    {       
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("UPDATE tabla_productospedidos
        SET estado = 'cancelado' WHERE id = :id AND estado != 'cancelado'");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        
        $consulta->execute();   
    }

    public static function borrarProductoPedidoPorCodigo($codigoPedido)
    {       
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("UPDATE tabla_productospedidos
        SET estado = 'cancelado' WHERE codigoPedido = :codigoPedido AND estado != 'cancelado'");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_INT);
        
        $consulta->execute();   
    }

    public static function InformarPendientesPorPerfil($perfil)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("SELECT * FROM tabla_productospedidos 
        WHERE perfil = :perfil AND estado = 'pendiente' AND idEmpleado = 0");              
        $consulta->bindValue(':perfil', $perfil, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }

    public static function InformarEnPreparacionPorPerfil($perfil)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("SELECT * FROM tabla_productospedidos 
        WHERE perfil = :perfil AND estado = 'en preparacion'");              
        $consulta->bindValue(':perfil', $perfil, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }

    public static function TomarPedidoPorPerfil($productoPedido)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("UPDATE tabla_productospedidos SET idEmpleado = :idEmpleado, 
        horarioPautado= :horarioPautado,estado = :estado WHERE codigoPedido = :codigoPedido AND perfil = :perfil AND estado = 'pendiente'");
        $consulta->bindValue(':codigoPedido', $productoPedido->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $productoPedido->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':perfil', $productoPedido->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':horarioPautado', $productoPedido->horarioPautado);
        $consulta->bindValue(':estado', $productoPedido->estado, PDO::PARAM_STR);

        $consulta->execute();
    }

    public static function InformarListosParaServirPorPerfil($perfil)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("SELECT * FROM tabla_productospedidos 
        WHERE perfil = :perfil AND estado = 'listo para servir'");              
        $consulta->bindValue(':perfil', $perfil, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }

    public static function obtenerSeccionPorCodigoPedido($codigoPedido)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("SELECT * FROM tabla_productospedidos 
        WHERE codigoPedido = :codigoPedido");     
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }

    public static function InformarProdOrdenadoPorCantVenta()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT p.idProducto, r.nombre, SUM(p.cantidad) AS cantidad_vendida 
        FROM tabla_productospedidos p LEFT JOIN tabla_producto r ON p.idProducto = r.id
        WHERE p.estado='entregado'
        GROUP BY p.idProducto
        ORDER BY cantidad_vendida DESC");
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }
}