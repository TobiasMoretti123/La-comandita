<?php
require_once './models/ProductoPedido.php';
require_once './models/Pedido.php';

class ProductoPedidoController extends ProductoPedido
{
  public static function CargarUno($codigoPedido, $perfil, $idProducto, $cantidad, $estado)
  {        
    $productoPedido = new ProductoPedido();
    $productoPedido->codigoPedido = $codigoPedido;
    $productoPedido->perfil = $perfil;
    $productoPedido->idProducto = $idProducto;      
    $productoPedido->cantidad = $cantidad;      
    $productoPedido->estado = $estado;        
    
    $productoPedido->AltaProductoPedido();
  }

  public function TraerUno($request, $response, $args)
  {
    $codigoPedido = $args['codigoPedido'];
    $pedido = Pedido::obtenerPedidoPorCodigo($codigoPedido);

    if($pedido)
    {
      $payload = json_encode($pedido);
      $response->getBody()->write($payload);
      $response = $response->withStatus(200);
      return $response->withHeader('Content-Type', 'application/json');           
    }
    else
    {
      $payload = json_encode(array("mensaje" => "Pedido inválido. Verifique los datos ingresados."));
      $response = $response->withStatus(400);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::LeerPedidos();
    if($lista)
    {
      $payload = json_encode(array("Lista de productos por pedidos" => $lista));
      $response->getBody()->write($payload);
      $response = $response->withStatus(200);
      return $response->withHeader('Content-Type', 'application/json');
    }
    else
    {
      $payload = json_encode(array("mensaje" => "No hay pedidos registrados."));
      $response->getBody()->write($payload);
      $response = $response->withStatus(400);
      return $response->withHeader('Content-Type', 'application/json');
    }        
  }
  
  public function ModificarUno($request, $response, $args)
  {
    
  }

  public function BorrarUno($request, $response, $args)
  {   
    
  }
}
