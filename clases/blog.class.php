<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class blog extends ConexionCrud{

    private $table= "web_blog";

    public function obtenerBlogActivos(){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . "  WHERE estado = '1' ORDER BY id_blog DESC";
        return parent::listar($query);

        }

       
    }


    public function obtenerBlogId($id){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

            $query = "SELECT * FROM " . $this->table . " WHERE link_blog='".$id."'";
            return parent::listar($query);

        }

       
    }


}
?>
