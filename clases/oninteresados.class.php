<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';


class datos extends ConexionCrud{
    private $table= "on_interesados";
    private $table2="on_interesados_token" ;
    private $table3="programa_ac" ;
    private $table4="lista_precio_programa";
    private $table5="web_pagos_pse";
    private $table6= "on_periodo_actual";
    private $table7= "on_seguimiento";
    private $table8= "on_precios_inscripcion";
    private $table9= "on_interesados_datos";
    private $table10= "on_entrevista";

    private $id_user="";
    private $usuario_tema="";
    private $token="";

    public function oninteresados($id,$token){
        $query = "SELECT * FROM " . $this->table . " tab1 INNER JOIN " . $this->table2 . " tab2 on tab1.id_estudiante=tab2.id_estudiante INNER JOIN " . $this->table9 . " tab9 on tab1.id_estudiante=tab9.id_estudiante WHERE tab1.id_estudiante= '$id' and tab2.on_interesados_token='$token'";
        return parent::listar($query);
    }

    public function onSoportes($id_estudiante,$caso){

        switch ($caso) {
            case 1:
                $tabla = "on_soporte_cedula ";
                break;
            case 2:
                $tabla = "on_soporte_diploma ";
                break;
            case 3:
                $tabla = "on_soporte_acta ";
                break;
            case 4:
                $tabla = "on_soporte_salud ";
                break;
            case 5:
                $tabla = "on_soporte_prueba ";
                break;
            case 6:
                $tabla = "on_soporte_compromiso ";
                break;
            case 7:
                $tabla = "on_soporte_proteccion_datos ";
                break;
            default:
                // Manejo de caso por defecto si es necesario
                $tabla = "";
                break;
        }

        $query = "SELECT * FROM " . $tabla . "  WHERE id_estudiante= '$id_estudiante'";
        $result=parent::listar($query);
        if ($result && count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }



    public function datosprograma($json){
        $_respuestas =new respuestas;
        $datos = json_decode($json,true);
  
        if(!isset($datos['programa_ac'])){
            // error con los campos
            return $_respuestas->error_400();
        }else{
            $datosperiodo = $this->onPeriodoActual();
            $periodo_actual=$datosperiodo[0]["periodo_campana"];

            $programa = $datos["programa_ac"];
            $query = "SELECT  
            tab3.id_programa,tab3.carnet,tab3.ciclo, 
            tab4.*,
            tab8.* 
            FROM " . $this->table3 . " tab3 INNER JOIN " . $this->table4 . " tab4 on tab3.id_programa=tab4.id_programa  INNER JOIN " . $this->table8 . " tab8 on tab3.ciclo=tab8.ciclo
            WHERE tab3.nombre= '$programa' AND tab4.periodo='$periodo_actual' AND tab4.semestre=1";
            return parent::listar($query);
        }   
    }

    public function actualizarTema($json){// PUT toma los datos del formulario para editar un usuario
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos["token"])){
            return $_respuestas->error_401();
        }
        else{
            $this->token=$datos["token"];
            $arrayToken = $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['usuario_tema'])){
                    return $_respuestas->error_400();
                }else{
                    if(isset($datos["id"])) { $this->id_user = $datos['id']; }
                    if(isset($datos["usuario_tema"])) { $this->usuario_tema = $datos['usuario_tema']; }

                    $resp = $this->editarTema();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id_user" => $this->id_user
                        );
                        return $respuesta;
                    }else{
                        return $resp;
                    }
                }
            }else{
                return $_respuestas->error_401("El token que envio es invalido o caducado");
            }
        }
    }

    private function editarTema(){// modelo para editar los datos del usuario
        date_default_timezone_set("America/Bogota");		
        $fecha = date('Y-m-d');
        $hora = date('h:i:s');
        $estado=0;

        $query= "UPDATE ".$this->table." SET 
        `usuario_tema`='" .$this->usuario_tema."'
        WHERE `id_user`='" . $this->id_user . "'"; 
  
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }
        else{
            return 0;
        }
    }

    private function buscarToken(){
        $query = "SELECT id_user,token,state_token FROM user_token WHERE token='" .$this->token. "' AND state_token='1'";
        $resp = parent::listar($query);
        if($resp){
            return $resp;
        }
        else{
            return 0;
        }
        
    }

    private function onPeriodoActual(){

        $query = "SELECT * FROM on_periodo_actual ";
        $resp = parent::listar($query);
        if($resp){
            return $resp;
        }
        else{
            return 0;
        }

    }

    public function guardarPago($json){
        $_respuestas = new respuestas;

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{
            $datos= json_decode($json,true);
            
            $query1 = "SELECT * FROM " . $this->table6 ;
            $resultado=parent::listar($query1);
            $periodo_actual=$resultado[0]["periodo_actual"];
            $periodo_campana=$resultado[0]["periodo_campana"];

            $estadodocumentos=$datos["x_extra5"];// 1 si falta validar 0 si estan validados lo documentos aplica igual cuando es inscripcion estao formulario inscripcion

            $yeminus_ok=0;
            $tiempopago="No Aplica";
            $factura_yeminus = "NULL"; // Será insertado como NULL en la BD


            $regpago ="INSERT INTO " . $this->table5 . " (`x_id_factura`, `identificacion_estudiante`, `id_estudiante`,`nombre_completo`, `celular`, `periodo`, `x_description`, `x_amount_base`, `x_currency_code`, `x_bank_name`, `x_respuesta`, `x_fecha_transaccion`, `x_franchise`, `x_customer_doctype`, `x_customer_document`, `x_customer_name`, `x_customer_lastname`, `x_customer_email`, `x_customer_phone`, `x_customer_movil`, `x_customer_ind_pais`, `x_customer_country`, `x_customer_city`, `x_customer_address`, `x_customer_ip`, `yeminus_ok`, `factura_yeminus`, `tiempo_pago`) 
            values ('". $datos["x_id_factura"]."','". $datos["x_extra1"]."','". $datos["x_extra2"]."','". $datos["x_extra3"]."','". $datos["x_extra4"]."','". $periodo_campana."','". $datos["x_description"]."','". $datos["x_amount_base"]."','". $datos["x_currency_code"]."','". $datos["x_bank_name"]."','". $datos["x_respuesta"]."','". $datos["x_fecha_transaccion"]."','". $datos["x_franchise"]."','". $datos["x_customer_doctype"]."','". $datos["x_customer_document"]."','". $datos["x_customer_name"]."','". $datos["x_customer_lastname"]."','". $datos["x_customer_email"]."','". $datos["x_customer_phone"]."','". $datos["x_customer_movil"]."','". $datos["x_customer_ind_pais"]."','". $datos["x_customer_country"]."','". $datos["x_customer_city"]."','". $datos["x_customer_address"]."','". $datos["x_customer_ip"]."','". $yeminus_ok."','". $factura_yeminus."','". $tiempopago."') ";

            $resp = parent::nonQueryId($regpago);

            if($resp){
                if($datos["x_description"]=="matricula"){
                    $resultadoActualizar=$this->actualizarEstadomatricula($datos["x_extra2"],$estadodocumentos); // el // Si `actualizarEstadomatricula` retorna un valor válido, devolverlo
                    if ($resultadoActualizar) {
                        return $resp;
                    }
                }else{// es un pago de inscripcion
                    $resultadoActualizar=$this->actualizarEstadoinscricpion($datos["x_extra2"],$estadodocumentos); // el // Si `actualizarEstadomatricula` retorna un valor válido, devolverlo
                    if ($resultadoActualizar) {
                        return $resp;
                    }
                }
                
            }else{
                return 0;// error 
            }

        }

    }

    public function actualizarFormulario($json){
        $_respuestas = new respuestas;

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{
            $datos= json_decode($json,true);

            $estado_inscripcion=$datos["estado_inscripcion"];
            $id_estudiante=$datos["id_estudiante"];

            $nivel_escolaridad=$datos["nivel_escolaridad"];
            $nombre_colegio=$datos["nombre_colegio"];
            $fecha_graduacion=$datos["fecha_graduacion"];

            $tipo_documento=$datos["tipo_documento"];
            $jornada_e=$datos["jornada"];
            $nombre=$datos["nombre"];
            $nombre_2=$datos["nombre_2"];
            $apellidos=$datos["apellidos"];
            $apellidos_2=$datos["apellidos_2"]; 
            $celular=$datos["celular"];
            $email=$datos["email"];

            $query1= "UPDATE ".$this->table9." SET 
            `nivel_escolaridad`='" .$nivel_escolaridad."', 
            `nombre_colegio`='" .$nombre_colegio."', 
            `fecha_graduacion`='" .$fecha_graduacion."'  
            WHERE `id_estudiante`='" . $id_estudiante . "'";

            $resp = parent::nonQuery($query1);


            if($resp){
                
                $resultadoActualizar=$this->actualizarEstadoformulario($id_estudiante,$estado_inscripcion,$tipo_documento,$jornada_e,$nombre,$nombre_2,$apellidos,$apellidos_2,$celular,$email); // el // Si `actualizarEstadomatricula` retorna un valor válido, devolverlo
                if ($resultadoActualizar) {
                    return $resp;
                }
                
                
            }else{
                return 0;// error 
            }

        }
    }

    public function insertarEntrevista($json){
        $_respuestas = new respuestas;

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{
            $datos= json_decode($json,true);


            $id_estudiante=$datos["id_estudiante"];
            

            $regEntrevista = "INSERT INTO " . $this->table10 . " (
                `id_estudiante`,     
                `salud_fisica`, 
                `condicion_de_salud`, 
                `si_condicion_de_salud`, 
                `condicion_neurologica`, 
                `si_condicion_neurologica`, 
                `niveles_de_estres`, 
                `comodo_salud_mental`, 
                `acceso_salud_mental`, 
                `recibir_apoyo_mental`, 
                `dificultad_emocional`, 
                `referir_persona`, 
                `sostiene`, 
                `labora`, 
                `donde_labora`, 
                `tiempo_laborando`, 
                `nombre_referencia`, 
                `celular_referencia`, 
                `parentesco_referencia`, 
                `solicitado_beca`, 
                `desempeno_academico`, 
                `materia_apoyo`, 
                `si_materia_apoyo`, 
                `habilidades_estudio`, 
                `dificultades_exigencias`, 
                `comodo_herramientas`
            ) VALUES (
                '". $id_estudiante ."',
                '". $datos["salud_fisica"] ."',
                '". $datos["condicion_de_salud"] ."',
                '". $datos["si_condicion_de_salud"] ."',
                '". $datos["condicion_neurologica"] ."',
                '". $datos["si_condicion_neurologica"] ."',
                '". $datos["niveles_de_estres"] ."',
                '". $datos["comodo_salud_mental"] ."',
                '". $datos["acceso_salud_mental"] ."',
                '". $datos["recibir_apoyo_mental"] ."',
                '". $datos["dificultad_emocional"] ."',
                '". $datos["referir_persona"] ."',
                '". $datos["sostiene"] ."',
                '". $datos["labora"] ."',
                '". $datos["donde_labora"] ."',
                '". $datos["tiempo_laborando"] ."',
                '". $datos["nombre_referencia"] ."',
                '". $datos["celular_referencia"] ."',
                '". $datos["parentesco_referencia"] ."',
                '". $datos["solicitado_beca"] ."',
                '". $datos["desempeno_academico"] ."',
                '". $datos["materia_apoyo"] ."',
                '". $datos["si_materia_apoyo"] ."',
                '". $datos["habilidades_estudio"] ."',
                '". $datos["dificultades_exigencias"] ."',
                '". $datos["comodo_herramientas"] ."'
            )";
            

            $resp = parent::nonQuery($regEntrevista);


            if($resp){
                
                $resultadoActualizar=$this->actualizarEstadoEntrevista($id_estudiante); // el // Si `actualizarEstadoentrevista` retorna un valor válido, devolverlo
                if ($resultadoActualizar) {
                    return $resp;
                }
                
                
            }else{
                return 0;// error 
            }

        }
    }

    private function actualizarEstadomatricula($id_estudiante,$estadodocumentos){// actualzia el estado de lka matricula en on interesados de 1 a 0
        date_default_timezone_set("America/Bogota");		
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $estadomatricula=0;
        $estado="Admitido";

        if($estadodocumentos==1){
            $query= "UPDATE ".$this->table." SET 
            `matricula`='" .$estadomatricula."'
            WHERE `id_estudiante`='" . $id_estudiante . "'"; 
        }else{
            $query= "UPDATE ".$this->table." SET 
            `matricula`='" .$estadomatricula."', 
            `estado`='" .$estado."' 
            WHERE `id_estudiante`='" . $id_estudiante . "'";
        }

        $resp2 = parent::nonQuery($query);
        if($resp2 >= 1){

            $comentario ="INSERT INTO " . $this->table7 . " (id_usuario, id_estudiante, motivo_seguimiento, mensaje_Seguimiento, fecha_seguimiento, hora_seguimiento, asesor) values ('1','". $id_estudiante."','seguimiento','Pago Matricula','". $fecha."','". $hora."','1') ";
            parent::nonQueryId($comentario);

            return $resp2;
        }
        else{
            return 0;//error
        }
    }

    private function actualizarEstadoinscricpion($id_estudiante,$estadoformulario){// actualzia el estado de del formulario de inscripcion en on interesados de 1 a 0
        date_default_timezone_set("America/Bogota");		
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $estadoinscripcion=0;
        $estado="Inscrito";

        if($estadoformulario==1){
            $query= "UPDATE ".$this->table." SET 
            `inscripcion`='" .$estadoinscripcion."'
            WHERE `id_estudiante`='" . $id_estudiante . "'"; 
        }else{
            $query= "UPDATE ".$this->table." SET 
            `inscripcion`='" .$estadoinscripcion."', 
            `estado`='" .$estado."' 
            WHERE `id_estudiante`='" . $id_estudiante . "'";
        }

        $resp2 = parent::nonQuery($query);
        if($resp2 >= 1){

            $comentario ="INSERT INTO " . $this->table7 . " (id_usuario, id_estudiante, motivo_seguimiento, mensaje_Seguimiento, fecha_seguimiento, hora_seguimiento, asesor) values ('1','". $id_estudiante."','seguimiento','Pago Inscripción','". $fecha."','". $hora."','1') ";
            parent::nonQueryId($comentario);

            return $resp2;
        }
        else{
            return 0;//error
        }
    }

    private function actualizarEstadoformulario($id_estudiante,$estado_inscripcion,$tipo_documento,$jornada_e,$nombre,$nombre_2,$apellidos,$apellidos_2,$celular,$email){// actualzia el estado de del formulario de inscripcion en on interesados de 1 a 0
        date_default_timezone_set("America/Bogota");		
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $estadoformulario=0;
        $estado="Inscrito";

        if($estado_inscripcion==1){
            $query= "UPDATE ".$this->table." SET 
            `formulario`='" .$estadoformulario."', 
            `tipo_documento`='" .$tipo_documento."', 
            `jornada_e`='" .$jornada_e."', 
            `nombre`='" .$nombre."', 
            `nombre_2`='" .$nombre_2."', 
            `apellidos`='" .$apellidos."', 
            `apellidos_2`='" .$apellidos_2."', 
            `celular`='" .$celular."', 
            `email`='" .$email."'
            WHERE `id_estudiante`='" . $id_estudiante . "'"; 
        }else{
            $query= "UPDATE ".$this->table." SET 
            `formulario`='" .$estadoformulario."', 
            `tipo_documento`='" .$tipo_documento."', 
            `jornada_e`='" .$jornada_e."', 
            `nombre`='" .$nombre."', 
            `nombre_2`='" .$nombre_2."', 
            `apellidos`='" .$apellidos."', 
            `apellidos_2`='" .$apellidos_2."', 
            `celular`='" .$celular."', 
            `email`='" .$email."',  
            `estado`='" .$estado."' 
            WHERE `id_estudiante`='" . $id_estudiante . "'";
        }

        $resp2 = parent::nonQuery($query);
        if($resp2 >= 1){

            $comentario ="INSERT INTO " . $this->table7 . " (id_usuario, id_estudiante, motivo_seguimiento, mensaje_Seguimiento, fecha_seguimiento, hora_seguimiento, asesor) values ('1','". $id_estudiante."','seguimiento','Formulario Inscripción','". $fecha."','". $hora."','1') ";
            parent::nonQueryId($comentario);

            return $resp2;
        }
        else{
            return 0;//error
        }
    }

    private function actualizarEstadoEntrevista($id_estudiante){// actualzia el estado de lka matricula en on interesados de 1 a 0
        date_default_timezone_set("America/Bogota");		
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $estadoentrevista=0;


        $query= "UPDATE ".$this->table." SET 
        `entrevista`='" .$estadoentrevista."'
        WHERE `id_estudiante`='" . $id_estudiante . "'"; 
      

        $resp2 = parent::nonQuery($query);
        if($resp2 >= 1){

            $comentario ="INSERT INTO " . $this->table7 . " (id_usuario, id_estudiante, motivo_seguimiento, mensaje_Seguimiento, fecha_seguimiento, hora_seguimiento, asesor) values ('1','". $id_estudiante."','seguimiento','Entrevista','". $fecha."','". $hora."','1') ";
            parent::nonQueryId($comentario);

            return $resp2;
        }
        else{
            return 0;//error
        }
    }




}

?>