<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class eventos extends ConexionCrud{

    private $table= "calendario_eventos";
    private $id_banner ="";
    private $nombre ="";
    private $nombre2 ="";
    private $ruta_url ="";
    private $estado ="";
    private $token ="";

    public function obtenereventosActivos(){
        $_respuestas = new respuestas;

        date_default_timezone_set("America/Bogota");	
        $fecha = date('Y-m-d');

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE 	fecha_inicio >= '$fecha'";
        return parent::listar($query);

        }

       
    }

    public function obtenereventosId($id){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE id_evento= '$id'";
        return parent::listar($query);

        }

       
    }


}
?>