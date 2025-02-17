<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class programa extends ConexionCrud{

    private $table= "web_programas";
    private $id_banner ="";
    private $nombre ="";
    private $nombre2 ="";
    private $ruta_url ="";
    private $estado ="";
    private $token ="";

    public function obtenerprogramaActivos(){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE estado = '1'";
        return parent::listar($query);

        }

       
    }

    public function obtenerprogramaId($id){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " tb1 INNER JOIN web_programa_descripcion tb2 ON tb1.id_programas=tb2.id_programas WHERE tb1.estado = '1' and tb1.id_programas= '$id'";
        return parent::listar($query);

        

        }

       
    }


}
?>