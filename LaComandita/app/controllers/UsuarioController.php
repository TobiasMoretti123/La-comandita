<?php
require_once './models/Usuario.php';
require_once './models/AutentificadorJWT.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $nombre = $parametros['nombre'];
      $clave = $parametros['clave'];
      $perfil = $parametros["perfil"];

      $usr = new Usuario();
      $usr->nombre = $nombre;
      $usr->clave = $clave;
      $usr->perfil= $perfil;
      $usr->AltaUsuario();

      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
      $response->getBody()->write($payload);
      $response = $response->withStatus(200);
      return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerUno($request, $response, $args)
    {
      $id = $args['id'];
        $usuario = Usuario::ObtenerUsuarioPorId($id);

        if($usuario)
        {
          $payload = json_encode($usuario);
          $response->getBody()->write($payload);
          $response = $response->withStatus(200);
          return $response->withHeader('Content-Type', 'application/json');           
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Usuario inválido. Verifique los datos ingresados."));
          $response->getBody()->write($payload);
          $response = $response->withStatus(400);
          return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function ObtenerUsuario($nombre)
    {
      $usuario = Usuario::ObtenerUsuarioPorNombre($nombre);
      if($usuario)
      {
        return $usuario;
      }
    }
    
    public function TraerTodos($request, $response, $args)
    {
      $lista = Usuario::LeerUsuarios();
      $payload = json_encode(array("listaUsuarios" => $lista));
      $response->getBody()->write($payload);
      $response = $response->withStatus(200);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
       
    }

    public function BorrarUno($request, $response, $args)
    {
      
    }
}
?>