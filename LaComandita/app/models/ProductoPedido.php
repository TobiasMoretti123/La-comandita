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

    public function AltaProductoPedido()
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

    public static function LeerProductosPedidos()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM tabla_productospedidos 
        WHERE estado != 'cancelado'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    } 
}
?>