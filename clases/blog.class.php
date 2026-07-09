<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class blog extends ConexionCrud{

    private $table= "web_blog";
    private $table_categorias = "web_blog_categorias";

    public function obtenerBlogActivos(){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " b INNER JOIN " . $this->table_categorias . " c ON b.id_web_blog_categorias = c.id_web_blog_categorias WHERE b.estado = '1' ORDER BY b.id_blog DESC";
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