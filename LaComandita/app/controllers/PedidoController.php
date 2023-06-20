<?php
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/ProductoPedidoController.php';
require_once './controllers/LogController.php';
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
      LogController::CargarUno($request, "Alta de un pedido");  
  
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
      $datos = json_decode(file_get_contents("php://input"), true);
      $pedido = new Pedido();
      $pedido->id=$datos["id"]; 
      $pedido->idMesa=$datos["idMesa"]; 
      $pedido->codigoPedido=$datos["codigoPedido"]; 
      $pedido->idMozo=$datos["idMozo"]; 
      $pedido->nombreCliente=$datos["nombreCliente"];
      $pedido->fotoMesa=$datos["fotoMesa"]; 
      $pedido->horarioPautado=$datos["horarioPautado"];
      $pedido->estado=$datos["estado"]; 
  
      if(Pedido::modificarPedido($pedido))
      {
        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        return $response->withHeader('Content-Type', 'application/json');
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se pudo modificar el pedido. Verifique los datos ingresados."));  
        $response->getBody()->write($payload);
        $response = $response->withStatus(400);
        return $response->withHeader('Content-Type', 'application/json');
      }
    }

    public function BorrarUno($request, $response, $args)
    {   
      $id =  $args["id"];
      $pedidoABorrar=Pedido::obtenerPedidoPorId($id);
      if(Pedido::borrarPedido($id))
      {
        ProductoPedido::borrarProductoPedidoPorCodigo($pedidoABorrar->codigoPedido);
        $payload = json_encode(array("mensaje" => "Pedido cancelado con exito"));
        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        return $response->withHeader('Content-Type', 'application/json');
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se pudo cancelar el pedido. Verifique los datos ingresados."));
        $response->getBody()->write($payload);
        $response = $response->withStatus(400);
        return $response->withHeader('Content-Type', 'application/json');
      }
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
          LogController::CargarUno($request, "Asignar una foto al pedido");
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
    
    public static function calcularTiempoDelPedido()
    {
      $listaDePedidos = Pedido::obtenerTodos();
      if($listaDePedidos)
      {
        foreach($listaDePedidos as $pedido)
        {
          $seccionesComanda = ProductoPedidoController::obtenerSeccionPorCodigoPedido($pedido->codigoPedido);
          $maximoTiempoPedido = 0;
          $todosTiemposDeterminados=true;
          foreach($seccionesComanda as $seccion)
          {
            if($seccion->horarioPautado !=null )
            {
              if($seccion->horarioPautado > $maximoTiempoPedido)
              {
                $maximoTiempoPedido = $seccion->horarioPautado;
              }
            }
            else
            {
              $todosTiemposDeterminados=false;
              break;
            }
          }

          if($todosTiemposDeterminados && $pedido->estado == "pendiente")
          {
            $pedido->estado = "en preparacion";
            $pedido->horarioPautado = $maximoTiempoPedido;
            Pedido::modificarPedido($pedido);
          }
        }
      }
    }

    public static function EmitirInformeDePedidosYDemoras($request, $response, $args)
    {
      $pedidos = Pedido::obtenerTodos();
      $cantidadDePedidos = count($pedidos);
      $listadoDePedidosConEstado = array();

      if($cantidadDePedidos>0)
      {    
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $horarioActual = new DateTime("now");

        foreach($pedidos as $pedido)
        {
          switch($pedido->estado)
          {
            case "pendiente":
              $mensajeDeDemora = "El pedido aún no ha comenzado a prepararse";
              break;
            case "en preparacion":
              $horarioPedido = datetime::createfromformat('Y-m-d H:i:s', $pedido->horarioPautado);
              $diferenciaEnMinutos = $horarioActual->diff($horarioPedido);
              $minutosDeDemora = $diferenciaEnMinutos->days * 24 * 60;
              $minutosDeDemora += $diferenciaEnMinutos->h * 60;
              $minutosDeDemora += $diferenciaEnMinutos->i;   
              if($horarioPedido > $horarioActual)
              {
                $mensajeDeDemora = "0(aún quedan " . $minutosDeDemora ." minutos para terminar el pedido)";
              }
              else
              {
                $mensajeDeDemora = $minutosDeDemora ." minutos";
              }
              break;
            case "listo para servir":
              $mensajeDeDemora = 0;
              break;
            case "entregado":
              $mensajeDeDemora = 0;
              break;
          }  
          
          $mensaje = "Pedido: " . $pedido->codigoPedido. " Estado: " . $pedido->estado. " Tiempo de demora: " . $mensajeDeDemora;
          array_push($listadoDePedidosConEstado, $mensaje);
          $response = $response->withStatus(200);
          LogController::CargarUno($request, "Emitir informe de pedidos y tiempo de demora");  
        }
      }
      else
      {
        $listadoDePedidosConEstado = array("Mensaje" => "No hay pedidos pendientes.");
        $response = $response->withStatus(400); 
      }
  
      $payload = json_encode($listadoDePedidosConEstado);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    } 
}
