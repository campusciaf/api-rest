<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class categorias_reglamentos extends ConexionCrud{

    private $table= "web_categoria_reglamentos";
    private $id_banner ="";
    private $nombre ="";
    private $nombre2 ="";
    private $ruta_url ="";
    private $estado ="";
    private $token ="";

    public function obtenerCategoriaReglamentos(){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " ";
        return parent::listar($query);

        }

       
    }


}
?>