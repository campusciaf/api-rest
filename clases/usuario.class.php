<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

class Usuario extends ConexionCrud {

    private $table= "credencial_estudiante";

    private $identificacion = "";
    private $credencial = "";

    public function validarEstudiante($identificacion, $credencial)
    {
        $_respuestas = new respuestas;

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ) {
            return $_respuestas->error_401();
        }else{
            $this->identificacion = $identificacion;
            $this->credencial = $credencial;

            $query ="SELECT
                        id_credencial,
                        credencial_identificacion,
                        credencial_nombre,
                        credencial_nombre_2,
                        credencial_apellido,
                        credencial_apellido_2,
                        credencial_identificacion,
                        credencial_login,
                        credencial_clave,
                        credencial_condicion,
                        status_update
                    FROM " . $this->table . "
                    WHERE credencial_identificacion = '".$this->identificacion."'
                    AND id_credencial = '".$this->credencial."'
                    AND credencial_condicion='1';";

            $resp = parent::listar($query);
            if ($resp) {
                return true;
            } else {
                return false;
            }
        }
    }
}
?>