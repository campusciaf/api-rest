<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/agregarDatosExpoU.class.php';
//header("Access-Control-Allow-Origin: *");// quita el bloqueo cros 
// header('Access-Control-Allow-Origin: https://ciaf.edu.co/');
// header('Access-Control-Allow-Origin: http://localhost:4200');
header("Access-Control-Allow-Headers: Origin,Autorizacion");
header("Access-Control-Allow-Headers: Origin, autorizacion, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header('Content-Type: application/json');


$_respuestas =new respuestas;
$_agregarDatos =new agregarDatosExpoU;

if($_SERVER["REQUEST_METHOD"] == "GET"){
    

}else if($_SERVER["REQUEST_METHOD"] == "POST"){
    // recibimos los datos enviados
    $postBody= file_get_contents("php://input");
    // enviamos esto al manejador
    
    $datosArray = $_agregarDatos->insertaragregarDatos($postBody);
    // devolvemos una respuesta
   
       header('Content-Type: application/json');

        if(isset($datosArray["result"]["error_id"])){
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
        }else{
            http_response_code(200);
        }
        echo json_encode($datosArray);

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