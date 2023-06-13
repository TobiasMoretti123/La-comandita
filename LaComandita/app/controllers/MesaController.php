<?php
require_once './models/Mesa.php';
require_once './models/Pedido.php';
require_once './models/ProductoPedido.php';
require_once './controllers/UsuarioController.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $codigoMesa = $parametros['codigoMesa'];
    $estado = $parametros['estado'];
    
    $mesa = new Mesa();
    $mesa->codigoMesa = $codigoMesa;
    $mesa->estado = $estado;
    $mesa->AltaMesa();

    $payload = json_encode(array("mensaje" => "Mesa creada con exito"));
    $response->getBody()->write($payload);
    $response = $response->withStatus(200);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $id = $args['id'];
    $mesa = Mesa::ObtenerMesaPorId($id);

    if ($mesa) 
    {
      $payload = json_encode($mesa);
      $response->getBody()->write($payload);
      $response = $response->withStatus(200);
      return $response->withHeader('Content-Type', 'application/json');
    } 
    else 
    {
      $payload = json_encode(array("mensaje" => "Mesa inexistente. Verifique los datos ingresados."));
      $response->getBody()->write($payload);
      $response = $response->withStatus(400);
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Mesa::LeerMesas();
    if($lista)
    {
      $payload = json_encode(array("Lista de mesas" => $lista));
      $response->getBody()->write($payload);
      $response = $response->withStatus(200);
      return $response->withHeader('Content-Type', 'application/json');
    }
    else
    {
      $payload = json_encode(array("mensaje" => "No hay mesas registradas."));
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