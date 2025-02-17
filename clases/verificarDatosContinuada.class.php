<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class verificarDatosContinuada extends ConexionCrud{

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



    public function verificarAgregarDatos($json){
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

            if(!isset($datos["id_curso_verificar"]) || !isset($datos["identificacion_verificar"]) ){
                    return $_respuestas->error_400();
            }else{

                $this->id_curso=$datos["id_curso_verificar"];
                $this->identificacion=$datos["identificacion_verificar"];

                $medio="web";

                $query = "SELECT * FROM " . $this->table . " WHERE identificacion = $this->identificacion and id_curso= $this->id_curso and estado_interesado='Interesado'";
                $result= parent::listar($query);

                if($result){//esta con curso registrado y puede realizar pago
                    return 0;
                }else{// no esta con curso registrado y no puede realizar pago
                    return 1;
                }


            }

        }


       

    }

    

}
?>