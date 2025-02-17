<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class programa_desempenate extends ConexionCrud{

    private $table= "web_programa_desempenate";
    private $id_banner ="";
    private $nombre ="";
    private $nombre2 ="";
    private $ruta_url ="";
    private $estado ="";
    private $token ="";



    public function obtenerprogramaId($id){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE id_programa= '$id' and estado = '1'";
        return parent::listar($query);

        

        }

       
    }




}
?>