<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class continuada extends ConexionCrud{

    private $table= "web_educacion_continuada";
    private $id_banner ="";
    private $nombre ="";
    private $nombre2 ="";
    private $ruta_url ="";
    private $estado ="";
    private $token ="";

    public function obtenercontinuadaActivos(){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE estado_educacion = '1'";
        return parent::listar($query);

        }

       
    }

    public function obtenerContinuadaId($id){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE estado_educacion = '1' and id_curso= '$id'";
        return parent::listar($query);

        }

       
    }


}
?>