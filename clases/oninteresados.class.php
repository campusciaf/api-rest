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
            date_default_timezone_set("America/Bogota");
            $fecha = date('Y-m-d');
            $datos= json_decode($json,true);
            if (is_array($datos) && isset($datos['version_formulario']) && (string)$datos['version_formulario'] === '2') {
                return $this->guardarEntrevistaNueva($datos);
            }
            $id_estudiante=$datos["id_estudiante"];
            

            $regEntrevista = "INSERT INTO " . $this->table10 . " (
                `id_estudiante`, 
                `fecha_entrevista`,
                `salud_fisica`, 
                `salud_mental`, 
                `condicion_especial`, 
                `nombre_condicion_especial`, 
                `estres_reciente`, 
                `desea_apoyo_mental`, 
                `costea_estudios`, 
                `labora_actualmente`, 
                `donde_labora`, 
                `tiempo_laborando`, 
                `desea_beca`, 
                `responsabilidades_familiares`, 
                `seguridad_carrera`, 
                `penso_abandonar`, 
                `desea_referir`, 
                `rendimiento_prev`, 
                `necesita_apoyo_academico`, 
                `nombre_materia`, 
                `tiene_habilidades_organizativas`, 
                `comodidad_herramientas_digitales`, 
                `acceso_internet`, 
                `acceso_computador`, 
                `estrato`, 
                `municipio_residencia`, 
                `direccion_residencia`, 
                `nombre_referencia_familiar`, 
                `telefono_referencia_familiar`, 
                `parentesco_referencia_familiar`

            ) VALUES (
                '". $id_estudiante ."',
                '". $fecha."',
                '". $datos["salud_fisica"] ."',
                '". $datos["salud_mental"] ."',
                '". $datos["condicion_especial"] ."',
                '". $datos["nombre_condicion_especial"] ."',
                '". $datos["estres_reciente"] ."',
                '". $datos["desea_apoyo_mental"] ."',
                '". $datos["costea_estudios"] ."',
                '". $datos["labora_actualmente"] ."',
                '". $datos["donde_labora"] ."',
                '". $datos["tiempo_laborando"] ."',
                '". $datos["desea_beca"] ."',
                '". $datos["responsabilidades_familiares"] ."',
                '". $datos["seguridad_carrera"] ."',
                '". $datos["penso_abandonar"] ."',
                '". $datos["desea_referir"] ."',
                '". $datos["rendimiento_prev"] ."',
                '". $datos["necesita_apoyo_academico"] ."',
                '". $datos["nombre_materia"] ."',
                '". $datos["tiene_habilidades_organizativas"] ."',
                '". $datos["comodidad_herramientas_digitales"] ."',
                '". $datos["acceso_internet"] ."',
                '". $datos["acceso_computador"] ."',
                '". $datos["estrato"] ."',
                '". $datos["municipio_residencia"] ."',
                '". $datos["direccion_residencia"] ."',
                '". $datos["nombre_referencia_familiar"] ."',
                '". $datos["telefono_referencia_familiar"] ."',
                '". $datos["parentesco_referencia_familiar"] ."'

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

    private function guardarEntrevistaNueva($datos){
        $_respuestas = new respuestas;
        $idnum = isset($datos['idnum']) ? (string)$datos['idnum'] : '';
        $token = isset($datos['token']) ? $datos['token'] : '';

        if (!ctype_digit($idnum) || !is_string($token) || $token === '' || strlen($token) > 256) {
            return $_respuestas->error_401('No autorizado, token invalido');
        }

        $consentimiento = isset($datos['consentimiento_datos']) ? $datos['consentimiento_datos'] : null;
        if ($consentimiento !== 1 && $consentimiento !== '1' && $consentimiento !== true) {
            return $_respuestas->error_400('Debes autorizar el tratamiento de datos.');
        }

        $respuestas = $this->validarEntrevistaNueva($datos);
        if (isset($respuestas['error'])) {
            return $_respuestas->error_400($respuestas['error']);
        }

        global $mbd;
        $mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $mbd->beginTransaction();

            $consulta = $mbd->prepare(
                "SELECT tab1.id_estudiante, tab1.entrevista
                FROM on_interesados tab1
                INNER JOIN on_interesados_token tab2
                    ON tab1.id_estudiante = tab2.id_estudiante
                    AND tab2.on_interesados_token = :token
                INNER JOIN on_interesados_datos tab9
                    ON tab1.id_estudiante = tab9.id_estudiante
                WHERE tab1.id_estudiante = :idnum
                LIMIT 1
                FOR UPDATE"
            );
            $consulta->execute(array(':token' => $token, ':idnum' => $idnum));
            $aspirante = $consulta->fetch(PDO::FETCH_ASSOC);

            if (!$aspirante) {
                $mbd->rollBack();
                return $_respuestas->error_401('No autorizado, token invalido');
            }

            $idEstudiante = (int)$aspirante['id_estudiante'];
            $pendiente = $aspirante['entrevista'] === 1 || $aspirante['entrevista'] === '1';

            if (!$pendiente) {
                $mbd->rollBack();
                return array(
                    'status' => 'ok',
                    'resultado' => 'ya_completada',
                    'id_estudiante' => $idEstudiante
                );
            }

            $existe = $mbd->prepare(
                "SELECT id_entrevista
                FROM on_entrevista
                WHERE id_estudiante = :id_estudiante AND version_formulario = 2
                LIMIT 1"
            );
            $existe->execute(array(':id_estudiante' => $idEstudiante));

            if (!$existe->fetch(PDO::FETCH_ASSOC)) {
                $columnas = array(
                    'id_estudiante', 'fecha_entrevista', 'version_formulario',
                    'consentimiento_datos', 'consentimiento_fecha'
                );
                $marcas = array(':id_estudiante', ':fecha_entrevista', '2', '1', ':consentimiento_fecha');
                $valores = array(
                    ':id_estudiante' => $idEstudiante,
                    ':fecha_entrevista' => date('Y-m-d'),
                    ':consentimiento_fecha' => date('Y-m-d H:i:s')
                );

                foreach ($respuestas as $columna => $valor) {
                    $columnas[] = $columna;
                    $marcas[] = ':' . $columna;
                    $valores[':' . $columna] = $valor;
                }

                $insertar = $mbd->prepare(
                    "INSERT INTO on_entrevista (" . implode(', ', $columnas) . ")
                    VALUES (" . implode(', ', $marcas) . ")"
                );
                foreach ($valores as $marca => $valor) {
                    if ($valor === null) {
                        $insertar->bindValue($marca, null, PDO::PARAM_NULL);
                    } else {
                        $insertar->bindValue($marca, $valor);
                    }
                }
                $insertar->execute();
                if ($insertar->rowCount() < 1) {
                    $mbd->rollBack();
                    return $_respuestas->error_500('No se pudo guardar la entrevista.');
                }
            }

            $actualizar = $mbd->prepare(
                "UPDATE on_interesados
                SET entrevista = 0
                WHERE id_estudiante = :id_estudiante AND entrevista = 1"
            );
            $actualizar->execute(array(':id_estudiante' => $idEstudiante));
            if ($actualizar->rowCount() !== 1) {
                $mbd->rollBack();
                return $_respuestas->error_500('No se pudo guardar la entrevista.');
            }

            $seguimiento = $mbd->prepare(
                "INSERT INTO on_seguimiento (
                    id_usuario, id_estudiante, motivo_seguimiento, mensaje_seguimiento,
                    fecha_seguimiento, hora_seguimiento, asesor
                ) VALUES (1, :id_estudiante, 'seguimiento', 'Entrevista', :fecha, :hora, '1')"
            );
            $seguimiento->execute(array(
                ':id_estudiante' => $idEstudiante,
                ':fecha' => date('Y-m-d'),
                ':hora' => date('H:i:s')
            ));
            if ($seguimiento->rowCount() < 1) {
                $mbd->rollBack();
                return $_respuestas->error_500('No se pudo guardar la entrevista.');
            }

            $mbd->commit();
            return array(
                'status' => 'ok',
                'resultado' => 'guardada',
                'id_estudiante' => $idEstudiante
            );
        } catch (Exception $e) {
            if ($mbd->inTransaction()) {
                $mbd->rollBack();
            }
            return $_respuestas->error_500('No se pudo guardar la entrevista.');
        }
    }

    private function validarEntrevistaNueva($datos){
        $catalogo = $this->catalogoEntrevistaNueva();
        $respuestas = array();

        foreach ($catalogo['unicas'] as $campo => $regla) {
            if (!isset($datos[$campo]) || !is_string($datos[$campo])) {
                return array('error' => 'Falta una respuesta obligatoria.');
            }
            $valor = trim($datos[$campo]);
            if (!in_array($valor, $regla['opciones'], true) || mb_strlen($valor, 'UTF-8') > $regla['max']) {
                return array('error' => 'Hay una opción que no está permitida.');
            }
            $respuestas[$campo] = $valor;
        }

        foreach ($catalogo['multiples'] as $campo => $regla) {
            if (!isset($datos[$campo]) || !is_array($datos[$campo])) {
                return array('error' => 'Falta una respuesta obligatoria.');
            }
            $seleccion = array();
            foreach ($datos[$campo] as $opcion) {
                if (!is_string($opcion)) {
                    return array('error' => 'Hay una opción que no está permitida.');
                }
                $opcion = trim($opcion);
                if (!in_array($opcion, $regla['opciones'], true) || in_array($opcion, $seleccion, true)) {
                    return array('error' => 'Hay una opción que no está permitida.');
                }
                $seleccion[] = $opcion;
            }
            $cantidad = count($seleccion);
            if ($cantidad < 1 || $cantidad > $regla['max']) {
                return array('error' => 'La cantidad de opciones seleccionadas no es válida.');
            }
            $json = json_encode($seleccion, JSON_UNESCAPED_UNICODE);
            if ($json === false || strlen($json) > 60000) {
                return array('error' => 'No se pudieron guardar las opciones seleccionadas.');
            }
            $respuestas[$campo] = $json;
        }

        if (!isset($datos['inspirador_nombre']) || !is_string($datos['inspirador_nombre'])) {
            return array('error' => 'Completa el nombre de tu Inspirador.');
        }
        $nombre = trim($datos['inspirador_nombre']);
        if ($nombre === '' || mb_strlen($nombre, 'UTF-8') > 150) {
            return array('error' => 'Completa el nombre de tu Inspirador.');
        }
        $respuestas['inspirador_nombre'] = $nombre;

        if (!isset($datos['inspirador_celular']) || !is_string($datos['inspirador_celular'])) {
            return array('error' => 'Escribe un celular de 10 dígitos.');
        }
        $celular = preg_replace('/\s+/', '', $datos['inspirador_celular']);
        if (!preg_match('/^\d{10}$/', $celular)) {
            return array('error' => 'Escribe un celular de 10 dígitos.');
        }
        $respuestas['inspirador_celular'] = $celular;

        $correo = isset($datos['inspirador_correo']) ? $datos['inspirador_correo'] : '';
        if (!is_string($correo)) {
            return array('error' => 'Escribe un correo válido o déjalo vacío.');
        }
        $correo = trim($correo);
        if ($correo !== '' && (!filter_var($correo, FILTER_VALIDATE_EMAIL) || mb_strlen($correo, 'UTF-8') > 150)) {
            return array('error' => 'Escribe un correo válido o déjalo vacío.');
        }
        $respuestas['inspirador_correo'] = $correo === '' ? null : $correo;

        if (!isset($datos['municipio_actual']) || !is_string($datos['municipio_actual'])) {
            return array('error' => 'Escribe tu municipio.');
        }
        $municipio = trim($datos['municipio_actual']);
        if ($municipio === '' || mb_strlen($municipio, 'UTF-8') > 120) {
            return array('error' => 'Escribe tu municipio.');
        }
        $respuestas['municipio_actual'] = $municipio;

        $mensaje = isset($datos['mensaje_final']) ? $datos['mensaje_final'] : '';
        if (!is_string($mensaje) || mb_strlen($mensaje, 'UTF-8') > 4000) {
            return array('error' => 'El mensaje final supera la longitud permitida.');
        }
        $mensaje = trim($mensaje);
        $respuestas['mensaje_final'] = $mensaje === '' ? null : $mensaje;

        return $respuestas;
    }

    private function catalogoEntrevistaNueva(){
        return array(
            'unicas' => array(
                'seguridad_carrera_elegida' => array('max' => 120, 'opciones' => array('Muy seguro(a).', 'Seguro(a).', 'Tengo algunas dudas.', 'Aún no estoy completamente convencido(a).')),
                'conocimiento_profesion' => array('max' => 40, 'opciones' => array('Mucho.', 'Bastante.', 'Poco.', 'Muy poco.')),
                'claridad_proyecto_vida' => array('max' => 80, 'opciones' => array('Muy claro.', 'Bastante claro.', 'Tengo algunas ideas.', 'Aún no lo tengo definido.')),
                'apoyo_personas' => array('max' => 120, 'opciones' => array('Sí, completamente.', 'En gran parte.', 'Solo algunas personas.', 'Siento que estoy afrontando este proceso solo(a).')),
                'inspirador_relacion' => array('max' => 80, 'opciones' => array('Madre', 'Padre', 'Pareja', 'Hijo(a)', 'Hermano(a)', 'Otro familiar', 'Amigo(a)', 'Compañero(a) de trabajo', 'Jefe', 'Otra persona')),
                'inspirador_comunicaciones' => array('max' => 10, 'opciones' => array('Sí.', 'No.')),
                'situacion_acompanamiento' => array('max' => 120, 'opciones' => array('No.', 'Sí, me gustaría recibir orientación.', 'Sí, actualmente recibo atención médica o psicológica.')),
                'contacto_equipo' => array('max' => 40, 'opciones' => array('Sí.', 'Tal vez.', 'Por ahora no.')),
                'forma_pedir_ayuda' => array('max' => 120, 'opciones' => array('Busco ayuda rápidamente.', 'Primero intento resolverla y luego pido ayuda.', 'Solo busco ayuda cuando ya no encuentro otra solución.', 'Prefiero resolverla por mi cuenta.')),
                'situacion_actual' => array('max' => 120, 'opciones' => array('Solo estudio.', 'Estudio y trabajo medio tiempo.', 'Estudio y trabajo tiempo completo.', 'Tengo un emprendimiento.', 'Trabajo y además tengo un emprendimiento.')),
                'financiacion_estudios' => array('max' => 80, 'opciones' => array('Recursos propios.', 'Apoyo de mi familia.', 'Crédito educativo.', 'Beca o subsidio.', 'Varias opciones.', 'Otro.')),
                'ingreso_hogar' => array('max' => 80, 'opciones' => array('Menos de $1.000.000', 'Entre $1.000.001 y $2.000.000', 'Entre $2.000.001 y $3.000.000', 'Entre $3.000.001 y $5.000.000', 'Entre $5.000.001 y $8.000.000', 'Más de $8.000.000', 'No lo conozco.', 'Prefiero no responder.')),
                'dependientes_economicos' => array('max' => 40, 'opciones' => array('No.', 'Sí, una.', 'Sí, dos.', 'Sí, tres o más.')),
                'mayor_reto' => array('max' => 80, 'opciones' => array('La situación económica.', 'El trabajo.', 'La organización del tiempo.', 'Las responsabilidades familiares.', 'La salud.', 'Adaptarme a la vida institucional.', 'Mantener la motivación.', 'Otro.')),
                'recurso_computador' => array('max' => 80, 'opciones' => array('Propio.', 'Compartido.', 'Solo celular o tableta.', 'No cuento con computador.')),
                'recurso_internet' => array('max' => 80, 'opciones' => array('Estable.', 'Algunas veces presenta dificultades.', 'No tengo acceso.')),
                'estrato_socioeconomico' => array('max' => 1, 'opciones' => array('1', '2', '3', '4', '5', '6')),
                'con_quien_vive' => array('max' => 80, 'opciones' => array('Con mis padres.', 'Solo con mi madre.', 'Solo con mi padre.', 'Con mi pareja.', 'Con mis hijos.', 'Con otros familiares.', 'Con amigos.', 'Vivo solo(a).', 'Otro.'))
            ),
            'multiples' => array(
                'motivacion_programa' => array('max' => 9, 'opciones' => array('Cumplir un sueño personal.', 'Crecer profesionalmente.', 'Conseguir mejores oportunidades laborales.', 'Mejorar mis ingresos.', 'Crear mi empresa o fortalecer un emprendimiento.', 'Continuar mi proyecto de vida.', 'Fue una recomendación de otra persona.', 'Aún estoy descubriendo cuál es el camino que quiero seguir.', 'Otro.')),
                'influencia_ciaf' => array('max' => 10, 'opciones' => array('El programa académico.', 'La calidad de la institución.', 'Los horarios.', 'La ubicación.', 'Las opciones de financiación.', 'La recomendación de familiares o amigos.', 'El costo.', 'Posicionamiento en la región.', 'Formación por ciclos.', 'Otro.')),
                'acompanamiento_academico' => array('max' => 11, 'opciones' => array('Matemáticas.', 'Inglés.', 'Lectura y comprensión de textos.', 'Escritura y redacción.', 'Herramientas digitales.', 'Métodos de estudio.', 'Organización del tiempo.', 'Hablar en público.', 'Habilidades blandas.', 'Ninguna por el momento.', 'Otras.')),
                'fortalezas_principales' => array('max' => 3, 'opciones' => array('Liderazgo.', 'Trabajo en equipo.', 'Comunicación.', 'Creatividad.', 'Organización.', 'Pensamiento lógico.', 'Tecnología.', 'Emprendimiento.', 'Disciplina.', 'Adaptabilidad.', 'Otra.')),
                'temas_acompanamiento' => array('max' => 12, 'opciones' => array('Bienestar emocional.', 'Empleabilidad.', 'Emprendimiento.', 'Liderazgo.', 'Educación financiera.', 'Deportes.', 'Cultura.', 'Idiomas.', 'Investigación.', 'Innovación.', 'Voluntariado.', 'Otro.'))
            )
        );
    }

}

?>