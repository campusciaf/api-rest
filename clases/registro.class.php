<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

class miregistro extends ConexionCrud{

    private $table= "on_interesados";
    private $table2= "on_periodo_actual";
    private $table3= "on_interesados_datos";

    
    private $id_banner ="";

    private $identificacion="";
    private $identificacion_r="";
    private $nombre ="";
    private $email ="";
    private $celular ="";
    private $fo_programa ="";
 

    
    private $medio="Web";
    private $conocio="Búsqueda Google";
    private $estado="Interesado";
    private $clave="";



    public function registrar($json){
        $_respuestas = new respuestas;

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

            date_default_timezone_set("America/Bogota");		
            $fecha = date('Y-m-d');
            $hora = date('h:i:s');
      
            $datos= json_decode($json,true);

            $query1 = "SELECT * FROM " . $this->table2 ;
            $resultado=parent::listar($query1);
            $periodo_actual=$resultado[0]["periodo_actual"];
            $periodo_campana=$resultado[0]["periodo_campana"];
            

            if(!isset($datos["user_nombre"]) || !isset($datos["user_celular"]) || !isset($datos["user_programa"])){
                    return $_respuestas->error_400();
            }else{
                $this->identificacion=$datos["user_identificacion"];
                $this->identificacion_r=$datos["user_identificacion_r"];
                $this->nombre=$datos["user_nombre"];
                $this->email=$datos["user_email"];
                $this->celular=$datos["user_celular"];
                $this->fo_programa=$datos["user_programa"];

                $this->clave = md5($this->identificacion);

                $query ="INSERT INTO " . $this->table . " (identificacion,fo_programa,nombre,celular,email,clave,periodo_ingreso,fecha_ingreso,hora_ingreso,medio,conocio,estado,periodo_campana) 
                values ('". $this->identificacion."','". $this->fo_programa."','". $this->nombre."','". $this->celular."','". $this->email."','". $this->clave."','". $periodo_actual."','". $fecha."','". $hora."','". $this->medio."','". $this->conocio."','". $this->estado."','". $periodo_campana."') ";
                
                $resp = parent::nonQueryId($query);
                if($resp){

                    $query3 ="INSERT INTO " . $this->table3 . " (id_estudiante) values ('". $resp."') ";
                    parent::nonQueryId($query3);


                    $asuntodir="Vive la experiencia";
                    $mensaje_final ="<h2>Vive la experiencia</h2><br>";
                    $mensaje_final .= $this->nombre;
                    $mensaje_final .= '<br><br>';
                    $mensaje_final .= 'Usuario: ';
                    $mensaje_final .= $this->identificacion;
                    $mensaje_final .= '<br>Clave: ';
                    $mensaje_final .= $resp;
                    $mensaje_final .= '<br> Link de ingreso: ';
                    $mensaje_final .= '<a href="https://ciaf.edu.co/onlogin" target="_blanck">ingresar aquí</a><br>';
                    $mensaje_final .= 'Somos el PARCHE de los universitarios en la era digital';

                    
                    $this->enviar_correo( $this->email, $asuntodir, $mensaje_final);

                    return $resp;
                }
                else{
                    return 0;
                }
            }

        }
    }

    function enviar_correo($destino, $asunto, $mensaje) {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = "mail.ciaf.edu.co";
        $mail->Port = 465;

        $mail->isHTML(true);
        $mail->Username = "contacto@ciaf.edu.co";
        $mail->Password = "soluciones3.0"; // Contrase�a del correo electronico
        $mail->setFrom("contacto@ciaf.edu.co", "CAMPUS");
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;
        $mail->CharSet = 'UTF-8';
    
        $mail->addAddress($destino);
    
        // Envío y verificación de errores
        if (!$mail->send()) {
            echo 'Error al enviar el correo: ' . $mail->ErrorInfo;
            return false;
        } else {
            return true;
        }
    }

    

}
?>