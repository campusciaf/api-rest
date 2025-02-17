<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class agregarDatos extends ConexionCrud{


    public function login( $json){
        $_respuestas =new respuestas;
        $datos = json_decode($json,true);


        if(!isset($datos['user_identificacion']) || !isset($datos['user_password'])){
            // error con los campos
           
            return $_respuestas->error_400();

        }else{
            // todo esta bien //
            $user_identificacion= $datos["user_identificacion"];
            $user_password = $datos["user_password"];// tiene el id estudiante

            $datos = $this->obtenerDatosUsuario($user_identificacion,$user_password);
            if($datos){
                // si existe el usuario verificar la contraseña
                if($user_password == $datos[0]["id_estudiante"]){
                        //crear el token
                        $verificar = $this->insertarToken($datos[0]["id_estudiante"]);

                        if($verificar){
                            // si se guardo
                            $result = $_respuestas->response;
                            $result["result"]=array(
                                "token" => $verificar,
                                "idnum" => $datos[0]["id_estudiante"]
                            );
                            return  $result;
                        }else{
                            //error
                            return $_respuestas->error_500("error interno, no hemos podido guardar");
                        }
                    
                }else{
                    return $_respuestas->error_200("La contraseña o usuario  incorrecto");
                }
                
            }else{
                // si no existe el usuario
                return $_respuestas->error_200("El usuario $user_identificacion no existe");
            }
        }
    }

    private function obtenerDatosUsuario($user_identificacion,$user_password){
        $query= "SELECT * from on_interesados where id_estudiante='$user_password' AND identificacion='$user_identificacion'";
        $datos = parent::listar($query);
        if(isset($datos[0]["id_estudiante"])){
            return $datos;
        }
            return 0;
    }

    //crear el token
    private function insertarToken($id_user){
        $val= true;
        $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
        $estado ="1"; // activo
        $fecha= date("Y-m-d H:i:s");

        $query1= "SELECT id_estudiante,on_interesados_token,on_state_token,on_date_token from on_interesados_token where id_estudiante='$id_user'";// si ya tiene un token
        $datos = parent::listar($query1);
        if(isset($datos[0]["id_estudiante"])){//actualizar el token que tiene

            $resp = $this->actualizarToken($id_user,$token);
            if($resp >= 1){
                return $token;
            }else{
                return 0;
            }
            
        }else{// si no crear un token
            $query = "INSERT INTO on_interesados_token(id_estudiante,on_interesados_token,on_state_token,on_date_token)values('$id_user','$token','$estado','$fecha')";
            $verificar = parent::nonQuery($query);
            if($verificar){
                return $token;
            }else{
                return 0;
            }
        }
        
        

    }

    private function actualizarToken($id_user,$token){
        $date = date("Y-m-d H:i:s");
        $query ="UPDATE on_interesados_token SET on_date_token = '$date', on_interesados_token= '$token' WHERE id_estudiante='$id_user'";
        $resp = parent::nonQuery($query);// non query devuelve es las filas afectadas, por eso la condicion es si es mayor a 1
        if($resp >= 1){
            return $resp;
        }
        else{
            return 0;
        }
    }
}
?>