<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class cifras extends ConexionCrud{

    private $table= "estudiantes";
    private $table2= "sofi_persona";
    private $table3= "estudiantes_antes_2012";
    private $table4="materias_ciafi";
    private $table5="programa_ac";
    private $table6="escuelas";




    public function egresados() {
        $_respuestas = new respuestas;
    
        if (!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK') {
            return $_respuestas->error_401();
        } else {
            // Primera consulta
            $query1 = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE estado = '2' AND ciclo IN ('1', '2', '3', '7')";
            $resultado1 = parent::listar($query1);
            
            // Segunda consulta
            $query2 = "SELECT COUNT(*) as total FROM " . $this->table3;
            $resultado2 = parent::listar($query2);
    
            // Sumar los totales
            $total1 = $resultado1[0]['total'] ?? 0;
            $total2 = $resultado2[0]['total'] ?? 0;
            $totalFinal = $total1 + $total2;
    
            // Retornar un solo total
            return ['total' => $totalFinal];
        }
    }

    public function creditos(){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT COUNT(*) as total FROM " . $this->table2 . " WHERE estado = 'Aprobado'";
        return parent::listar($query);

        }

       
    }

    public function financiacion(){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

       $query = "
            SELECT SUM(m.valor_financiacion) AS total
            FROM sofi_matricula m
            JOIN sofi_persona p ON m.id_persona = p.id_persona
            WHERE p.estado = 'Aprobado'
        ";
        return parent::listar($query);

        }

       
    }




}
?>