<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class aliados extends ConexionCrud{

    private $table= "web_aliados";
    private $id_banner ="";
    private $nombre ="";
    private $nombre2 ="";
    private $ruta_url ="";
    private $estado ="";
    private $token ="";

    public function obteneraliadosActivos(){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE estado = '1'";
        return parent::listar($query);

        }

       
    }


}
?>