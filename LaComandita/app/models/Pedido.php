<?php

class Pedido
{
    public $id;
    public $idMesa;
    public $codigoPedido;
    public $idMozo;
    public $nombreCliente;
    public $fotoMesa;
    public $horarioPautado;
    public $horarioEntregado;
    public $totalFacturado;
    public $estado;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("INSERT INTO tabla_pedidos (idMesa, codigoPedido, idMozo, 
        nombreCliente, fotoMesa, estado) VALUES (:idMesa, :codigoPedido, :idMozo, :nombreCliente, :fotoMesa, :estado)");
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':fotoMesa', $this->fotoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_pedidos WHERE estado != 'cancelado'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_pedidos 
        WHERE codigoPedido = :codigoPedido AND estado != 'cancelado'");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("UPDATE tabla_pedidos SET nombreCliente = :nombreCliente, 
        horarioPautado = :horarioPautado, horarioEntregado = :horarioEntregado, 
        totalFacturado = :totalFacturado, estado = :estado WHERE id = :id");
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);
        $consulta->bindValue(':nombreCliente', $pedido->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':horarioPautado', $pedido->horarioPautado, PDO::PARAM_STR);
        $consulta->bindValue(':horarioEntregado', $pedido->horarioEntregado, PDO::PARAM_STR);
        $consulta->bindValue(':totalFacturado', $pedido->totalFacturado, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $pedido->estado, PDO::PARAM_STR);
        
        return $consulta->execute();
    }

    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("UPDATE tabla_pedidos SET 
        estado = 'cancelado' WHERE id = :id AND estado != 'cancelado'");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        return $consulta->execute(); 
    }

    public static function asignarFotoPosterior($pedido)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("UPDATE tabla_pedidos SET 
        fotoMesa = :fotoMesa WHERE id = :id");
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);
        $consulta->bindValue(':fotoMesa', $pedido->fotoMesa, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public static function obtenerPedidoPorId($id)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_pedidos
        WHERE id = :id AND estado != 'cancelado'");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerPedidoPorIdMesaYEntregado($idMesa)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_pedidos
        WHERE idMesa = :idMesa AND totalFacturado is NULL");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }
    public static function obtenerPedidoPorIdMesa($idMesa)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_pedidos
        WHERE idMesa = :idMesa");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }
    public static function obtenerPedidoPorCodigo($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_pedidos
        WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }
    public static function obtenerPedidoPorIdMesaYEstado($idMesa)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_pedidos
        WHERE idMesa = :idMesa AND estado = 'listo para servir'");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function InformarListosParaServirTodos()
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("SELECT * FROM tabla_pedidos 
        WHERE estado = 'listo para servir'");              
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function InformarMesaMasUsada()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT T.idMesa,T.cantidad_de_usos FROM 
        (SELECT idMesa, COUNT(idMesa) AS cantidad_de_usos FROM tabla_pedidos WHERE estado='entregado' 
        GROUP BY idMesa ORDER BY cantidad_de_usos DESC) T LIMIT 1");
        $consulta->execute();

        return $consulta->fetchObject();
    }

    public static function InformarPedidosNoATiempo()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_pedidos 
        WHERE horarioPautado<horarioEntregado AND estado != 'cancelado'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function InformarMesasOrdenadasPorFacturacion()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT p.idMesa,p.codigoPedido, p.totalFacturado, 
        m.codigoMesa FROM tabla_pedidos p LEFT JOIN tabla_mesas m ON p.idMesa = m.id
        WHERE p.estado='entregado' AND m.estado='cerrada' AND p.totalFacturado IS NOT NULL
        ORDER BY p.totalFacturado");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function InformarFacturadoEntreFechasPorMesa($idMesa,$fechaDesde, $fechaHasta)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT p.idMesa, m.codigoMesa, SUM(p.totalFacturado) 
        AS facturacion_total FROM tabla_pedidos p LEFT JOIN tabla_mesas m ON p.idMesa = m.id
        WHERE p.estado ='entregado' AND m.estado='cerrada' AND p.totalFacturado IS NOT NULL
        AND p.idMesa=:idMesa AND p.horarioEntregado BETWEEN :fechaDesde AND :fechaHasta
        GROUP BY p.idMesa");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':fechaDesde', $fechaDesde);
        $consulta->bindValue(':fechaHasta', $fechaHasta);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function InformarPedidosCancelados()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_pedidos 
        WHERE estado = 'cancelado'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function InformarMesaMenosUsada()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT T.idMesa,T.cantidad_de_usos FROM 
        (SELECT idMesa, COUNT(idMesa) AS cantidad_de_usos FROM tabla_pedidos WHERE estado='entregado' 
        GROUP BY idMesa ORDER BY cantidad_de_usos ASC) T LIMIT 1");
        $consulta->execute();

        return $consulta->fetchObject();
    }

    public static function InformarFacturacionAcumuladaMesas($criterio)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT p.idMesa, m.codigoMesa, SUM(p.totalFacturado) 
        AS facturacion_total FROM tabla_pedidos p LEFT JOIN tabla_mesas m ON p.idMesa = m.id
        WHERE p.estado ='entregado' AND m.estado='cerrada' AND p.totalFacturado IS NOT NULL
        GROUP BY p.idMesa
        ORDER BY p.totalFacturado $criterio LIMIT 1");
        $consulta->bindValue(':criterio', $criterio);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }
}
?>