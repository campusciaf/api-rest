<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

class misreferidos extends ConexionCrud{

    private $table= "on_interesados";
    private $table2= "on_periodo_actual";
    private $table3= "on_interesados_datos";
    private $table4= "referidos";
    
    private $id_banner ="";
   
    private $nombre ="";
    private $celular ="";
    private $fo_programa ="";
    private $jornada_e ="";


    private $nombre_r ="";
    private $celular_r ="";
    private $relacion ="";


    private $token ="";
    private $identificacion="";
    private $medio="Marketing-digital";
    private $conocio="Referido";
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
            

            if(!isset($datos["nombre"]) || !isset($datos["celular"]) || !isset($datos["fo_programa"])){
                    return $_respuestas->error_400();
            }else{
                    $this->nombre_r=$datos["nombre_r"];
                    $this->celular_r=$datos["celular_r"];
                    $this->relacion=$datos["relacion"];
                    
                    $this->nombre=$datos["nombre"];
                    $this->celular=$datos["celular"];
                    $this->fo_programa=$datos["fo_programa"];
                    $this->jornada_e=$datos["jornada_e"];

                $query ="INSERT INTO " . $this->table . " (identificacion,fo_programa,jornada_e,nombre,celular,clave,periodo_ingreso,fecha_ingreso,hora_ingreso,medio,conocio,estado,periodo_campana) 
                values ('". $this->identificacion."','". $this->fo_programa."','". $this->jornada_e."','". $this->nombre."','". $this->celular."','". $this->clave."','". $periodo_actual."','". $fecha."','". $hora."','". $this->medio."','". $this->conocio."','". $this->estado."','". $periodo_campana."') ";
                
                $resp = parent::nonQueryId($query);
                if($resp){

                    $query3 ="INSERT INTO " . $this->table3 . " (id_estudiante) values ('". $resp."') ";
                    parent::nonQueryId($query3);

                    $query4 ="INSERT INTO " . $this->table4 . " (id_estudiante,nombre,celular,relacion,fecha,hora,periodo_campana) values ('". $resp."','". $this->nombre_r."','". $this->celular_r."','". $this->relacion."','". $fecha."','". $hora."','". $periodo_campana."') ";
                    parent::nonQueryId($query4);

                    // $asuntodir="Vive la experiencia";
                    // $mensaje_final ="<h2>Vive la experiencia</h2><br>";
                    // $mensaje_final .= $this->nombre;
                    // $mensaje_final .= '<br><br>';
                    // $mensaje_final .= 'Somos el PARCHE de los universitarios en la era digital';
                    //$this->enviar_correo( $this->correo, $asuntodir, $mensaje_final);

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