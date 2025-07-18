<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class cifras extends ConexionCrud{

    private $table= "puntos";
    private $table2= "credencial_estudiante";




    public function puntos() {
        $_respuestas = new respuestas;
    
        if (!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK') {
            return $_respuestas->error_401();
        } else {
            // Primera consulta
           $query1 = "
                SELECT 
                    est.id_credencial,
                    est.credencial_nombre,
                    est.credencial_nombre_2,
                    est.credencial_apellido,
                    est.credencial_apellido_2,
                    est.credencial_identificacion,
                    est.credencial_login,
                    est.credencial_usuario,
                    SUM(pts.puntos_cantidad) AS puntos
                FROM 
                    $this->table pts
                INNER JOIN 
                    $this->table2 est ON est.id_credencial = pts.id_credencial
                WHERE 
                    pts.punto_nombre = 'induccion'
                GROUP BY 
                    est.id_credencial
                ORDER BY 
                    puntos DESC
                LIMIT 10
            ";

            $resultado1 = parent::listar($query1);
            return ['total' => $resultado1];

        }
    }






}
?>