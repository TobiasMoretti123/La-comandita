<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $nombre = $parametros['nombre'];
      $precio = $parametros['precio'];
      $sector = $parametros["sector"];

      $producto = new Producto();
      $producto->nombre = $nombre;
      $producto->precio = $precio;
      $producto->sector= $sector;
      $producto->AltaProducto();

      $payload = json_encode(array("mensaje" => "Producto creado con exito"));
      $response->getBody()->write($payload);
      $response = $response->withStatus(200);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      $id = $args['id'];
      $producto = Producto::obtenerProductoPorId($id);

      if($producto)
      {
        $payload = json_encode($producto);
        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        return $response->withHeader('Content-Type', 'application/json');       
      }
      else
      {
        $payload = json_encode(array("mensaje" => "Producto inválido. Verifique los datos ingresados."));
        $response->getBody()->write($payload);
        $response = $response->withStatus(400);
        return $response->withHeader('Content-Type', 'application/json');
      }
    }
    
    public function TraerTodos($request, $response, $args)
    {
      $lista = Producto::LeerProductos();
      if($lista)
      {
        $payload = json_encode(array("Lista de Productos" => $lista));
        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        return $response->withHeader('Content-Type', 'application/json');
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No hay productos."));
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
?>