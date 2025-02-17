<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class horarios extends ConexionCrud{

    private $table= "docente_grupos";
    private $table2= "periodo_actual";
    private $table3= "docente";
    private $table4="materias_ciafi";
    private $table5="programa_ac";
    private $table6="escuelas";
    private $id_banner ="";
    private $nombre ="";
    private $nombre2 ="";
    private $ruta_url ="";
    private $estado ="";
    private $token ="";
  

    public function obtenerhorarios(){
        $_respuestas = new respuestas;

        date_default_timezone_set("America/Bogota");	
        $fecha_actual = date('Y-m-d');
        $hora_actual = date('H:i:s');
        $rango=strtotime('01:00:00');

        $dia="";

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{
        
        $day = date("l");
        switch ($day) {
            case "Sunday":
                $dia="Domingo";
            break;
            case "Monday":
                $dia="Lunes";
            break;
            case "Tuesday":
                $dia="Martes";
            break;
            case "Wednesday":
                $dia="Miercoles";
            break;
            case "Thursday":
                $dia="Jueves";
            break;
            case "Friday":
                $dia="Viernes";
            break;
            case "Saturday":
                $dia="Sabado";
            break;
        }



        $query2 = "SELECT * FROM " . $this->table2 . " ";
        $datos=parent::listar($query2);
        $periodo_actual=$datos[0]["periodo_actual"];

        $query = "SELECT 
        dg.id_docente,dg.id_materia,dg.id_programa,dg.dia,dg.hora,dg.hasta,dg.periodo,dg.salon,dg.semestre,
        doc.id_usuario,doc.usuario_nombre,doc.usuario_apellido,doc.usuario_imagen,
        mc.id,mc.nombre AS nombre_asignatura, 
        pac.id_programa,pac.nombre AS nombre_programa,pac.escuela,
        esc.id_escuelas,esc.escuelas AS nombre_escuela
        FROM " . $this->table . " dg INNER JOIN " . $this->table3 . " doc on dg.id_docente=doc.id_usuario 
        INNER JOIN " . $this->table4 . " mc on dg.id_materia=mc.id 
        INNER JOIN " . $this->table5 . " pac on dg.id_programa=pac.id_programa 
        INNER JOIN " . $this->table6 . " esc on pac.escuela=esc.id_escuelas
        WHERE dg.periodo = '$periodo_actual' and dg.dia='$dia' and (dg.hora <='$hora_actual' and dg.hasta >='$hora_actual') order by dg.hora ASC";
        return parent::listar($query);

        
        }

       
    }

    public function obtenerHorarioProximo(){
        $_respuestas = new respuestas;

        date_default_timezone_set("America/Bogota");	
        $fecha_actual = date('Y-m-d');
        $hora_actual = date('H:i:s');
        $rango=strtotime('01:00:00');

        $dia="";

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{
        
        $day = date("l");
        switch ($day) {
            case "Sunday":
                $dia="Domingo";
            break;
            case "Monday":
                $dia="Lunes";
            break;
            case "Tuesday":
                $dia="Martes";
            break;
            case "Wednesday":
                $dia="Miercoles";
            break;
            case "Thursday":
                $dia="Jueves";
            break;
            case "Friday":
                $dia="Viernes";
            break;
            case "Saturday":
                $dia="Sabado";
            break;
        }

        $fechaAuxiliar  = strtotime ( "2 hours" , strtotime ( $hora_actual ) ) ;  
        $nuevafecha   = date ( 'H:i:s' , $fechaAuxiliar );
       

        $query2 = "SELECT * FROM " . $this->table2 . " ";
        $datos=parent::listar($query2);
        $periodo_actual=$datos[0]["periodo_actual"];

        $query = "SELECT 
        dg.id_docente,dg.id_materia,dg.id_programa,dg.dia,dg.hora,dg.hasta,dg.periodo,dg.salon,dg.semestre,
        doc.id_usuario,doc.usuario_nombre,doc.usuario_apellido,doc.usuario_imagen,
        mc.id,mc.nombre AS nombre_asignatura, 
        pac.id_programa,pac.nombre AS nombre_programa,pac.escuela,
        esc.id_escuelas,esc.escuelas AS nombre_escuela
        FROM " . $this->table . " dg INNER JOIN " . $this->table3 . " doc on dg.id_docente=doc.id_usuario 
        INNER JOIN " . $this->table4 . " mc on dg.id_materia=mc.id 
        INNER JOIN " . $this->table5 . " pac on dg.id_programa=pac.id_programa 
        INNER JOIN " . $this->table6 . " esc on pac.escuela=esc.id_escuelas
        WHERE dg.periodo = '$periodo_actual' and dg.dia='$dia' and (dg.hora >'$hora_actual' and dg.hora <='$nuevafecha') order by dg.hora ASC";
        return parent::listar($query);
  


        
        }

       
    }

    function sumar($hora1, $hora2)
    {
        list($h, $m, $s) = explode(':', $hora2); //Separo los elementos de la segunda hora
        $a = new DateTime($hora1); //Creo un DateTime
        $b = new DateInterval(sprintf('PT%sH%sM%sS', $h, $m, $s)); //Creo un DateInterval
        $a->add($b); //SUMO las horas
        return $a->format('H:i:s'); //Retorno la Suma
    }



}
?>