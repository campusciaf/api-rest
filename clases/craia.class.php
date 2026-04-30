<?php
require_once 'conexion/crud.php';
require_once 'ia.class.php';

class craia extends ConexionCrud {

    public function procesarMensaje($mensaje, $session){

        // 🔹 1. DETECTAR PROGRAMA
        $programa_id = $this->detectarPrograma($mensaje);

        error_log("Programa detectado: " . $programa_id);

        // 🔥 2. RECUPERAR CONTEXTO SI NO DETECTA
        if(!$programa_id && isset($_SESSION['programa_id'])){
            $programa_id = $_SESSION['programa_id'];
        }

        // 🔥 3. GUARDAR SI DETECTA NUEVO
        if($programa_id){
            $_SESSION['programa_id'] = $programa_id;
        }

        // 🔹 4. NORMALIZAR MENSAJE
        $texto = strtolower($mensaje);

        // 🔹 5. DETECTAR ETAPA
        $etapa = 'inicio';

        if ($programa_id) {
            $etapa = 'seleccion';
        }
        else if (strpos($texto, 'elegir') !== false || strpos($texto, 'no se') !== false) {
            $etapa = 'orientacion';
        }
        else if (strpos($texto, 'programa') !== false || strpos($texto, 'carrera') !== false) {
            $etapa = 'seleccion';
        }
        else if (strpos($texto, 'precio') !== false || strpos($texto, 'cuesta') !== false) {
            $etapa = 'validacion';
        }
        else if (strpos($texto, 'inscrib') !== false) {
            $etapa = 'cierre';
        }

        // 🔹 6. DETECTAR INTENCIÓN: PRECIO (ANTES DE IA)
        if (
            strpos($texto, 'precio') !== false ||
            strpos($texto, 'cuesta') !== false ||
            strpos($texto, '💰') !== false
        ) {

            if($programa_id){

                $sugerencias = $this->obtenerSugerencias('validacion');

                return [
                    "respuesta" => 'El programa tiene un costo aproximado de $X por semestre 💰',
                    "sugerencias" => $sugerencias,
                    "etapa" => "validacion",
                    "programa_id" => $programa_id
                ];
            }
        }

        // 🔹 7. TRAER SUGERENCIAS NORMALES
        $etapaConsulta = $programa_id ? 'seleccion' : $etapa;

        $sugerencias = $this->obtenerSugerencias($etapaConsulta);

        // 🔹 8. RESPUESTA IA (FALLBACK)
        $ia = new IA();
        $respuesta = $ia->generarRespuesta($mensaje);

        // 🔹 9. RETORNAR TODO
        return [
            "respuesta" => $respuesta,
            "sugerencias" => $sugerencias,
            "etapa" => $etapa,
            "programa_id" => $programa_id
        ];
    }

    private function detectarPrograma($mensaje){

        $texto = $this->limpiarTexto($mensaje);

        $sql = "SELECT item_id, alias FROM whatsapp_items_alias";
        $aliasList = $this->listar($sql);

        foreach($aliasList as $row){

            $alias = $this->limpiarTexto($row['alias']);

            if(strpos($texto, $alias) !== false){
                return $row['item_id'];
            }
        }

        return null;
    }
    private function limpiarTexto($texto){
        $texto = strtolower($texto);

        $texto = str_replace(
            ['á','é','í','ó','ú','ñ'],
            ['a','e','i','o','u','n'],
            $texto
        );

        return $texto;
    }

    private function obtenerSugerencias($etapa){

        $sql = "SELECT texto FROM craia_sugerencias 
                WHERE etapa = '$etapa' 
                ORDER BY orden";

        $data = $this->listar($sql);

        $sugerencias = [];

        foreach($data as $row){
            $sugerencias[] = $row['texto'];
        }

        return $sugerencias;
    }
}