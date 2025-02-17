<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/programa_desempenate.class.php';
//header("Access-Control-Allow-Origin: *");// quita el bloqueo cros 
// header('Access-Control-Allow-Origin: https://ciaf.edu.co/');
// header('Access-Control-Allow-Origin: http://localhost:4200');
header("Access-Control-Allow-Headers: Origin,Autorizacion");
header('Content-Type: application/json');


$_respuestas =new respuestas;
$_programa_desempenate =new programa_desempenate;

if($_SERVER["REQUEST_METHOD"] == "GET"){

    if(isset($_GET['id'])){

        $cursoid= $_GET['id'];
        $datocurso = $_programa_desempenate->obtenerprogramaId($cursoid);
        header('Content-Type: application/json');
        echo json_encode($datocurso);
        http_response_code(200);

    }
}

    
    
 ?>