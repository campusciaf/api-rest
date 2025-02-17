<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/calidad_crecimiento.class.php';
header("Access-Control-Allow-Origin: *");// quita el bloqueo cros 
// header('Access-Control-Allow-Origin: https://ciaf.edu.co/');
// header('Access-Control-Allow-Origin: http://localhost:4200');
// header('Access-Control-Allow-Origin: https://www.google.com/');
header("Access-Control-Allow-Headers: Origin,Autorizacion");
header('Content-Type: application/json');


$_respuestas =new respuestas;
$_calidad_crecimiento =new calidad_crecimiento;

if($_SERVER["REQUEST_METHOD"] == "GET"){

        $datocalidad_crecimiento = $_calidad_crecimiento->obtenercalidad_crecimientoActivos();
        header('Content-Type: application/json');
        echo json_encode($datocalidad_crecimiento);
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