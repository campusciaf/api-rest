<?php
require "Conexion.php";
session_start();
class ConexionCrud
{

    private function convertirUTF8($array){
        array_walk_recursive($array,function(&$item,$key){
            if(!mb_detect_encoding($item,'utf-8',true)){
                $item = utf8_encode($item);
            }
        });
        return $array;

    }

    public function listar($sqlstr){
        global $mbd;
        $sentencia = $mbd->prepare($sqlstr);
        $sentencia->execute();
        $registros = $sentencia->fetchAll(PDO::FETCH_ASSOC);
        return $this->convertirUTF8($registros);
        
    }

    public function nonQuery($sqlstr){
        global $mbd;
        $consulta = $mbd->prepare($sqlstr);
        return $consulta->execute();
    }


    /* insert */
    public function nonQueryId($sqlstr){
        global $mbd;
        $consulta = $mbd->prepare($sqlstr);
        $consulta->execute();
        $filas = $consulta->rowCount();

        if($filas >= 1){
            return $mbd->lastInsertId();
        }else{
            return 0;
        }
    }

    // encryptar
    protected function encriptar($string){
        return md5($string);
    }


}


?>