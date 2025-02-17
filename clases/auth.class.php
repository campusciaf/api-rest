<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class auth extends ConexionCrud{

    public function login( $json){
        $_respuestas =new respuestas;
       
        if(!isset($_POST["usuario"]) || !isset($_POST["password"])){
            // error con los campos
            return $_respuestas->error_400();

        }else{
            // todo esta bien //
            $usuario = $_POST["usuario"];
            $password = $_POST["password"];
            $password=parent::encriptar($password);

            $datos = $this->obtenerDatosUsuario($usuario);
            if($datos){
                // si existe el usuario verificar la contraseña
                if($password == $datos[0]["usuario_clave"]){
                    if($datos[0]["usuario_condicion"]==1){
                        //crear el token
                        $verificar = $this->insertarToken($datos[0]["id_usuario"]);

                        if($verificar){
                            // si se guardo
                            $result = $_respuestas->response;
                            $result["result"]=array(
                                "token" => $verificar
                            );
                            return  $result;
                        }else{
                            //error
                            return $_respuestas->error_500("error interno, no hemos podido guardar");
                        }
                    }else{
                        // usuario inactivo
                        return $_respuestas->error_200("el usuario esta inactivo"); 
                    }
                }else{
                    return $_respuestas->error_200("el paswword es invalido");
                }
                
            }else{
                // si no existe el usuario
                return $_respuestas->error_200("El usuario $usuario no existe");
            }
        }
    }

    private function obtenerDatosUsuario($correo){
        $query= "SELECT id_usuario,usuario_clave,usuario_email,usuario_condicion from usuario where usuario_email='$correo'";
        $datos = parent::listar($query);
        if(isset($datos[0]["id_usuario"])){
            return $datos;
        }
            return 0;
    }

    //crear el token
    private function insertarToken($id_usuario){
        $val= true;
        $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
        $estado ="1"; // activo
        $fecha= date("Y-m-d H:i");
        
        $query = "INSERT INTO usuario_token(id_usuario,token,estado,fecha)values('$id_usuario','$token','$estado','$fecha')";
        $verificar = parent::nonQuery($query);
        if($verificar){
            return $token;
        }else{
            return 0;
        }

    }

}
?>