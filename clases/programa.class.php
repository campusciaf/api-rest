<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class programa extends ConexionCrud{

    private $table= "web_programas";
    private $id_banner ="";
    private $nombre ="";
    private $nombre2 ="";
    private $ruta_url ="";
    private $estado ="";
    private $token ="";

    public function obtenerprogramaActivos(){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE estado = '1'";
        return parent::listar($query);

        }

       
    }

    public function obtenerprogramaId($id)
    {
        $_respuestas = new respuestas;

        $headers = getallheaders();

        if (
            !isset($headers["Autorizacion"]) ||
            $headers["Autorizacion"] !== 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK'
        ) {
            return $_respuestas->error_401();
        }

        // Evita inyección SQL en el ID
        $idPrograma = intval($id);

        /*
        * 1. Información general del programa
        */
        $queryPrograma = "
            SELECT *
            FROM web_programas
            WHERE estado = 1
            AND id_programas = $idPrograma
            LIMIT 1
        ";

        $resultadoPrograma = parent::listar($queryPrograma);

        if (empty($resultadoPrograma)) {
            return [
                "estado" => false,
                "mensaje" => "Programa no encontrado",
                "programa" => null,
                "conoce" => null,
                "perfil_profesional" => [],
                "perfil_ocupacional" => [],
                "campo_accion" => [],
                "experiencias" => [],
                "porque_estudiarlo" => null,
                "porque_estudiarlo_items" => [],
                "valores" => null
            ];
        }

        /*
        * 2. Encabezado de Conoce el programa
        */
        $queryConoce = "
            SELECT
                id_conoce,
                id_programas,
                titulo,
                subtitulo,
                contenido_perfil_profesional,
                contenido_perfil_ocupacional
            FROM web_programas_conoce
            WHERE estado = 1
            AND id_programas = $idPrograma
            LIMIT 1
        ";

        $resultadoConoce = parent::listar($queryConoce);

        $conoce = !empty($resultadoConoce)
            ? $resultadoConoce[0]
            : null;

        /*
        * 3. Perfiles profesional y ocupacional
        */
        $perfilProfesional = [];
        $perfilOcupacional = [];

        if ($conoce !== null) {
            $idConoce = intval($conoce["id_conoce"]);

            $queryItems = "
                SELECT
                    id_item,
                    id_conoce,
                    tipo,
                    titulo,
                    descripcion,
                    icono,
                    orden
                FROM web_programas_conoce_items
                WHERE estado = 1
                AND id_conoce = $idConoce
                ORDER BY tipo ASC, orden ASC, id_item ASC
            ";

            $items = parent::listar($queryItems);

            if (is_array($items)) {
                foreach ($items as $item) {
                    if ($item["tipo"] === "profesional") {
                        $perfilProfesional[] = $item;
                    }

                    if ($item["tipo"] === "ocupacional") {
                        $perfilOcupacional[] = $item;
                    }
                }
            }
        }

        /*
        * 4. Campos de acción
        */
        $queryCampoAccion = "
            SELECT
                id_campo_accion,
                id_programas,
                titulo,
                descripcion,
                orden
            FROM web_programas_campo_accion
            WHERE estado = 1
            AND id_programas = $idPrograma
            ORDER BY orden ASC, id_campo_accion ASC
        ";

        $campoAccion = parent::listar($queryCampoAccion);

        if (!is_array($campoAccion)) {
            $campoAccion = [];
        }

        /*
        * 5. Experiencias en video
        */
        $queryExperiencias = "
            SELECT
                id_experiencia,
                id_programas,
                titulo,
                descripcion,
                plataforma,
                codigo_video,
                url_video,
                imagen_portada,
                orden
            FROM web_programas_experiencias
            WHERE estado = 1
            AND id_programas = $idPrograma
            ORDER BY orden ASC, id_experiencia ASC
        ";

        $experiencias = parent::listar($queryExperiencias);

        if (!is_array($experiencias)) {
            $experiencias = [];
        }

        /*
        * 6. Encabezado de Por qué estudiarlo
        */
        $queryPorqueEstudiarlo = "
            SELECT
                id_porque_estudiarlo,
                id_programas,
                titulo,
                subtitulo
            FROM web_programas_porque_estudiarlo
            WHERE estado = 1
            AND id_programas = $idPrograma
            LIMIT 1
        ";

        $resultadoPorque = parent::listar($queryPorqueEstudiarlo);

        $porqueEstudiarlo = !empty($resultadoPorque)
            ? $resultadoPorque[0]
            : null;

        /*
        * 7. Tarjetas de Por qué estudiarlo
        */
        $porqueEstudiarloItems = [];

        if ($porqueEstudiarlo !== null) {
            $idPorqueEstudiarlo = intval(
                $porqueEstudiarlo["id_porque_estudiarlo"]
            );

            $queryPorqueItems = "
                SELECT
                    id_item,
                    id_porque_estudiarlo,
                    titulo,
                    descripcion,
                    icono,
                    tema,
                    orden
                FROM web_programas_porque_estudiarlo_items
                WHERE estado = 1
                AND id_porque_estudiarlo = $idPorqueEstudiarlo
                ORDER BY orden ASC, id_item ASC
            ";

            $porqueEstudiarloItems = parent::listar(
                $queryPorqueItems
            );

            if (!is_array($porqueEstudiarloItems)) {
                $porqueEstudiarloItems = [];
            }
        }

        /*
        * 8. Valores vigentes del programa
        */
        $queryValores = "
            SELECT
                id_valor,
                id_programas,
                periodo,
                valor_inscripcion,
                valor_seguro,
                valor_inscripcion_total,
                valor_matricula_anterior,
                valor_matricula_actual,
                inscripcion_pago_unico,
                moneda,
                fecha_inicio_vigencia,
                fecha_fin_vigencia
            FROM web_programas_valores
            WHERE estado = 1
            AND id_programas = $idPrograma
            AND (
                fecha_inicio_vigencia IS NULL
                OR fecha_inicio_vigencia <= CURDATE()
            )
            AND (
                fecha_fin_vigencia IS NULL
                OR fecha_fin_vigencia >= CURDATE()
            )
            ORDER BY
                fecha_inicio_vigencia DESC,
                id_valor DESC
            LIMIT 1
        ";

        $resultadoValores = parent::listar($queryValores);

        $valores = !empty($resultadoValores)
            ? $resultadoValores[0]
            : null;

        /*
        * Respuesta completa
        */
        return [
            "estado" => true,
            "programa" => $resultadoPrograma[0],
            "conoce" => $conoce,
            "perfil_profesional" => $perfilProfesional,
            "perfil_ocupacional" => $perfilOcupacional,
            "campo_accion" => $campoAccion,
            "experiencias" => $experiencias,
            "porque_estudiarlo" => $porqueEstudiarlo,
            "porque_estudiarlo_items" => $porqueEstudiarloItems,
            "valores" => $valores
        ];
    }

}
?>