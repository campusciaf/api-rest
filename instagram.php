<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/instagram.class.php';
//header("Access-Control-Allow-Origin: *");// quita el bloqueo cros 
// header('Access-Control-Allow-Origin: https://ciaf.edu.co/');
// header('Access-Control-Allow-Origin: http://localhost:4200');
header("Access-Control-Allow-Headers: Origin,Autorizacion");
header('Content-Type: application/json');


$_respuestas =new respuestas;
$_instagram =new instagram;

if($_SERVER["REQUEST_METHOD"] == "GET"){

    if(isset($_GET['id'])){

        $cursoid= $_GET['id'];
        $datocurso = $_instagram->obtenerinstagramId($cursoid);
        header('Content-Type: application/json');
        echo json_encode($datocurso);
        http_response_code(200);

    }else{
        $datoinstagram = $_instagram->getUserInfoAndPosts();
        header('Content-Type: application/json');
        echo json_encode($datoinstagram);
        http_response_code(200);
    }

    
    
    

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