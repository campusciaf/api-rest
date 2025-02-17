<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/categorias_reglamentos.class.php';
header("Access-Control-Allow-Origin: *");// quita el bloqueo cros 
// header('Access-Control-Allow-Origin: https://ciaf.edu.co/');
// header('Access-Control-Allow-Origin: http://localhost:4200');
header("Access-Control-Allow-Headers: Origin,Autorizacion");
header('Content-Type: application/json');


$_respuestas =new respuestas;
$_reglamento =new categorias_reglamentos;

if($_SERVER["REQUEST_METHOD"] == "GET"){

        $datoreglamentos = $_reglamento->obtenerCategoriaReglamentos();
        header('Content-Type: application/json');
        echo json_encode($datoreglamentos);
        http_response_code(200);
    
    

}else if($_SERVER["REQUEST_METHOD"] == "POST"){
   

}else if($_SERVER["REQUEST_METHOD"] == "PUT"){

    

}

else if($_SERVER["REQUEST_METHOD"] == "DELETE"){

}

else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);

}

 ?>