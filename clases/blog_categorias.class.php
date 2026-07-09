<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class blog_categorias extends ConexionCrud{

    private $table= "web_blog_categorias";

    public function obtenerBlogCategorias(){
        $_respuestas = new respuestas;

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();

        }else{

            $query = "SELECT id_web_blog_categorias, nombre_categoria FROM " . $this->table . " ORDER BY id_web_blog_categorias ASC";
            return parent::listar($query);

        }
    }


}
?>