<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class agregarDatosExpoU extends ConexionCrud{

    private $table= "on_interesados";
    private $table2= "on_periodo_actual";
    private $table3= "on_interesados_datos";
    
    private $id_banner ="";
   
    private $nombre ="";
    private $correo ="";
    private $celular ="";
    private $nombre_acudiente ="";
    private $celular_acudiente ="";
    private $fo_programa ="";
    private $token ="";
    private $identificacion="";
    private $medio="Asesor";
    private $conocio="Organico";
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

            /*** consilta para traer el period actual ******* */
            $query1 = "SELECT * FROM " . $this->table2 ;
            $resultado=parent::listar($query1);
            $periodo_actual=$resultado[0]["periodo_actual"];
            $periodo_campana=$resultado[0]["periodo_campana"];
            /* ************************************** */

            if(!isset($datos["nombre"]) || !isset($datos["correo"]) || !isset($datos["celular"]) || !isset($datos["fo_programa"]) || !isset($datos["nombre_acudiente"]) || !isset($datos["celular_acudiente"])){
                    return $_respuestas->error_400();
            }else{

                    $this->nombre=$datos["nombre"];
                    $this->correo=$datos["correo"];
                    $this->celular=$datos["celular"];
                    $this->nombre_acudiente=$datos["nombre_acudiente"];
                    $this->celular_acudiente=$datos["celular_acudiente"];
                    $this->fo_programa=$datos["fo_programa"];

                $query ="INSERT INTO " . $this->table . " (identificacion,fo_programa,nombre,celular,email,clave,periodo_ingreso,fecha_ingreso,hora_ingreso,medio,conocio,estado,periodo_campana,nombre_acudiente,celular_acudiente) 
                values ('". $this->identificacion."','". $this->fo_programa."','". $this->nombre."','". $this->celular."','". $this->correo."','". $this->clave."','". $periodo_actual."','". $fecha."','". $hora."','". $this->medio."','". $this->conocio."','". $this->estado."','". $periodo_campana."','". $this->nombre_acudiente."','". $this->celular_acudiente."') ";
                
                $resp = parent::nonQueryId($query);
                if($resp){

                    $query3 ="INSERT INTO " . $this->table3 . " (id_estudiante) values ('". $resp."') ";
                    parent::nonQueryId($query3);

                    return $resp;
                }
                else{
                    return 0;
                }
            }

        }


       

    }

    

}
?>