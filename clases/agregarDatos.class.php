<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

class agregarDatos extends ConexionCrud{

    private $table= "on_interesados";
    private $table2= "on_periodo_actual";
    private $table3= "on_interesados_datos";
    
    private $id_banner ="";
   
    private $nombre ="";
    private $correo ="";
    private $celular ="";
    private $fo_programa ="";
    private $token ="";
    private $identificacion="";
    private $medio="Web";
    private $estado="Interesado";
    private $clave="";


    public function insertaragregarDatos($json){
        $_respuestas = new respuestas;

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

            date_default_timezone_set("America/Bogota");		
            $fecha = date('Y-m-d');
            $hora = date('h:i:s');

           // algoritmo para generar una identificación
           $numero_aleatorio = rand(1111111,999999999);
           /* ******************************* */
      
            $this->identificacion=1 . $numero_aleatorio;
            $this->clave = md5($this->identificacion);

            $datos= json_decode($json,true);

            $query1 = "SELECT * FROM " . $this->table2 ;
            $resultado=parent::listar($query1);
            $periodo_actual=$resultado[0]["periodo_actual"];
            $periodo_campana=$resultado[0]["periodo_campana"];
            

            if(!isset($datos["nombre"]) || !isset($datos["correo"]) || !isset($datos["celular"]) || !isset($datos["fo_programa"])){
                    return $_respuestas->error_400();
            }else{
                   
                    
                    $this->nombre=$datos["nombre"];
                    $this->correo=$datos["correo"];
                    $this->celular=$datos["celular"];
                    $this->fo_programa=$datos["fo_programa"];

                $query ="INSERT INTO " . $this->table . " (identificacion,fo_programa,nombre,celular,email,clave,periodo_ingreso,fecha_ingreso,hora_ingreso,medio,estado,periodo_campana) 
                values ('". $this->identificacion."','". $this->fo_programa."','". $this->nombre."','". $this->celular."','". $this->correo."','". $this->clave."','". $periodo_actual."','". $fecha."','". $hora."','". $this->medio."','". $this->estado."','". $periodo_campana."') ";
                
                $resp = parent::nonQueryId($query);
                if($resp){

                    $query3 ="INSERT INTO " . $this->table3 . " (id_estudiante) values ('". $resp."') ";
                    parent::nonQueryId($query3);

                    $asuntodir="Vive la experiencia";
                    $mensaje_final ="<h2>Vive la experiencia</h2><br>";
                    $mensaje_final .= $this->nombre;
                    $mensaje_final .= '<br><br>';
                    $mensaje_final .= 'Somos el PARCHE de los universitarios en la era digital';
                    $this->enviar_correo( $this->correo, $asuntodir, $mensaje_final);

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