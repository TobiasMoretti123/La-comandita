<?php
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/ProductoPedidoController.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      
      $idMesa = $parametros['idMesa'];
      $codigoPedido = $parametros['codigoPedido'];
      $idMozo = $parametros['idMozo'];
      $nombreCliente = $parametros['nombreCliente'];
      $productos = $parametros['productos'];  
      $estado = $parametros['estado'];     
      
      $pedido = new Pedido();   
      $pedido->idMesa = $idMesa; 
      $pedido->codigoPedido = $codigoPedido; 
      $pedido->idMozo = $idMozo; 
      $pedido->nombreCliente = $nombreCliente;   
      if(file_exists($_FILES["fotoMesa"]["tmp_name"]))
      {
        $pedido->fotoMesa = $this->tomarFoto();
      } 
      else 
      {
        $pedido->fotoMesa = null;
      }
      $pedido->estado = $estado;  

      $productos = json_decode($productos);
      foreach($productos as $producto)
      {  
        $productoComprado = Producto::ObtenerProductoPorNombre($producto->producto);
        if($productoComprado)
        {
          MesaController::ActualizarEstadoMesa("con cliente esperando pedido", $idMesa);
          ProductoPedidoController::CargarUno($codigoPedido,$productoComprado->sector, $productoComprado->id, $producto->cantidad, "Pendiente");
        }
      }
      $pedido->AltaPedido();  
  
      $payload = json_encode(array("mensaje" => "Pedido creado con exito. El código de su pedido es: " 
      . $pedido->codigoPedido . ". Con él podrá verificar el estado de su pedido"));  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      $id = $args['id'];
      $pedido = Pedido::ObtenerPedidoPorId($id);

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
        $response->getBody()->write($payload);
        $response = $response->withStatus(400);
        return $response->withHeader('Content-Type', 'application/json');
      }
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Pedido::LeerPedidos();
      if($lista)
      {
        $payload = json_encode(array("Lista de pedidos" => $lista));
        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        return $response->withHeader('Content-Type', 'application/json');
      } 
      else 
      {
        $payload = json_encode(array("mensaje" => "No hay pedidos."));
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

    public static function tomarFoto()
    {
      $carpetaFotos = ".".DIRECTORY_SEPARATOR."fotosMesas".DIRECTORY_SEPARATOR;
      if(!file_exists($carpetaFotos))
      {
          mkdir($carpetaFotos, 0777, true);
      }
      $nuevoNombre = $carpetaFotos.$_FILES["fotoMesa"]["name"];
      rename($_FILES["fotoMesa"]["tmp_name"], $nuevoNombre);

      return $nuevoNombre;
    }

    public static function tomarFotoPosterior($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      
      $idPedido= $parametros["idPedido"];
      $pedidoAModificar=Pedido::ObtenerPedidoPorId($idPedido);
      if($pedidoAModificar->fotoMesa == null)
      {
        $pedidoAModificar->fotoMesa = PedidoController::tomarFoto();
        if (Pedido::AsignarFotoPosterior($pedidoAModificar)) 
        {
          $payload = json_encode(array("mensaje" => "Foto asignada al pedido con exito"));
          $response = $response->withStatus(200);
        } 
        else 
        {
          $payload = json_encode(array("mensaje" => "No se pudo asignar una foto al pedido"));
          $response = $response->withStatus(400);
        }
      }
      else
      {
        $payload = json_encode(array("mensaje" => "El pedido ya posee una foto asignada."));  
        $response = $response->withStatus(400);
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    } 
}
