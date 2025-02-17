<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class emprendimientos extends ConexionCrud{

    private $table= "web_emprendimientos";
    private $id_banner ="";
    private $nombre ="";
    private $nombre2 ="";
    private $ruta_url ="";
    private $estado ="";
    private $token ="";

    public function obteneremprendimientosActivos(){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE estado_emprendimiento = '1'";
        return parent::listar($query);

        }

       
    }

    public function obteneremprendimientosId($id){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE estado_emprendimiento= '1' and id_web_emprendimientos= '$id'";
        return parent::listar($query);

        }

       
    }


}
?>