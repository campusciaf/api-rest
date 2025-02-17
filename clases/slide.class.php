<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class slide extends ConexionCrud{

    private $table= "web_baner";
    private $id_banner ="";
    private $nombre ="";
    private $nombre2 ="";
    private $ruta_url ="";
    private $estado ="";
    private $token ="";

    // public function claveKey(){

    //     return "KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK";
    // }

    public function obtenerSlideActivos(){



    
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

                $query = "SELECT * FROM " . $this->table . " WHERE estado = '1' ORDER BY id_banner DESC";
                return parent::listar($query);
           
        }

        // $query = "SELECT * FROM " . $this->table . " WHERE estado = '1'";
        // return parent::listar($query);



       
    }

    public function listarSlide($pagina = 1){
        $inicio = 0;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad *($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;

        }
        $query = "SELECT * FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::listar($query);
        return ($datos);
    }

    public function obtenerSlide($id){
        $query = "SELECT * FROM " . $this->table . " WHERE id_banner = '$id'";
        return parent::listar($query);
    }

    // public function post($json){
    //     $_respuestas = new respuestas;

    //     if(!isset($_POST["token"])){
    //         return $_respuestas->error_401();
            
    //     }else{
    //         $this->token = $_POST["token"];
    //         $arrayToken= $this->buscartoken();
    //         if($arrayToken){

    //         }else{
    //             return $_respuestas->error_401("El token es invalido o ha caducado");
    //         }
    //     }

    //         if(!isset($_POST["nombre"]) || !isset($_POST["nombre2"]) || !isset($_POST["ruta_url"])){
    //             return $_respuestas->error_400();
    //         }else{
                
    //             $this->nombre = $_POST["nombre"];
    //             $this->nombre2 = $_POST["nombre2"];
    //             $this->ruta_url = $_POST["ruta_url"];
                
    //             if(isset($_POST["estado"])){$this->estado = $_POST["estado"];}

    //             $resp = $this->insertarSlide();
                
    //             if($resp){
    //                 $respuesta= $_respuestas->response;
    //                 $respuesta["result"] = array(
    //                     "id_banner" => $resp
    //                 );
    //                 return $respuesta;
    //             }else{
    //                 return $_respuestas->error_500();
    //             }

    //         }
    // }

    public function insertarSlide(){
        $query ="INSERT INTO " . $this->table . " (nombre,nombre2,ruta_url,estado) 
        values ('". $this->nombre."','". $this->nombre2."','". $this->ruta_url."','". $this->estado."') ";

        $resp = parent::nonQueryId($query);
        if($resp){
            return $resp;
        }
        else{
            return 0;
        }
    }

    // private function buscartoken(){
    //     $query ="SELECT token,id_usuario,estado from usuario_token WHERE token= '" .$this->token. "'AND estado='1'";
    //     $resp = parent::listar($query);
    //     if($resp){
    //         return $resp;
    //     }
    //     else{
    //         return 0;
    //     }
        
    // }


    // private function actualizarToken($id_usuario_token){
    //     $date=date("Y-m-d H:i:s");
    //     $query ="UPDATE usuario_token SET fecha='$date' WHERE id_usuario_token='$id_usuario_token'";
    //     $resp = parent::nonQuery($query);
    //     if($resp >=1){
    //         return $resp;
    //     }
    //     else{
    //         return 0;
    //     }
    // }

    


    

}
?>