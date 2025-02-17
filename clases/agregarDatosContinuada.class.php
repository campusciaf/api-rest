<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class agregarDatosContinuada extends ConexionCrud{

    private $table= "educacion_continuada_interesados";
    private $table2= "on_periodo_actual";
    private $table3= "on_interesados_datos";

    private $id_curso ="";
    private $identificacion ="";
    private $nombre ="";
    private $celular ="";
    private $email ="";
    private $periodo_actual="";
    private $periodo_campana="";



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
       

            $datos= json_decode($json,true);

            /*** consulta para traer el period actual ******* */
            $query1 = "SELECT * FROM " . $this->table2 ;
            $resultado=parent::listar($query1);
            $periodo_actual=$resultado[0]["periodo_actual"];
            $periodo_campana=$resultado[0]["periodo_campana"];
            /* ************************************** */

            if(!isset($datos["id_curso"]) || !isset($datos["identificacion"]) || !isset($datos["nombre"]) || !isset($datos["celular"]) || !isset($datos["email"])){
                    return $_respuestas->error_400();
            }else{

                $this->id_curso=$datos["id_curso"];
                $this->identificacion=$datos["identificacion"];
                $this->nombre=$datos["nombre"];
                $this->celular=$datos["celular"];
                $this->email=$datos["email"];
                $medio="web";

                $query = "SELECT * FROM " . $this->table . " WHERE identificacion = $this->identificacion and id_curso= $this->id_curso";
                $result= parent::listar($query);

                if($result){//esta con curso registrado
                    return 0;
                }else{// no esta con surso registrado
         
                    $query ="INSERT INTO " . $this->table . " (identificacion,id_curso,nombre,celular,email,periodo_ingreso,fecha_ingreso,hora_ingreso,medio,periodo_campana) 
                    values ('". $this->identificacion."','". $this->id_curso."','". $this->nombre."','". $this->celular."','". $this->email."','". $periodo_actual."','". $fecha."','". $hora."','". $medio."','". $periodo_campana."') ";
                    $resp = parent::nonQueryId($query);
                    return $resp;
                }


            }

        }


       

    }

    

}
?>