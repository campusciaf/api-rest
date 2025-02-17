<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/noticias.class.php';
header("Access-Control-Allow-Origin: *");// quita el bloqueo cros 
header("Access-Control-Allow-Methods: PUT, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, autorizacion, X-Requested-With, Content-Type, Accept, Access-Control-Request-MethodAccess-Control-Allow-Headers,Authorization");
header('content-type: application/json');

$_respuestas =new respuestas;
$_noticias =new noticias;

if($_SERVER["REQUEST_METHOD"] == "GET"){

    if(@$_GET['id']=="codefc"){
        $datonoticias = $_noticias->obtenerNoticiasActivosPrincipal();
        header('Content-Type: application/json');
        echo json_encode($datonoticias);
        http_response_code(200);
    }

    else if(isset($_GET['id'])){

        $noti_id= $_GET['id'];
        $datonoticias = $_noticias->obtenerNoticiasId($noti_id);
        header('Content-Type: application/json');
        echo json_encode($datonoticias);
        http_response_code(200);

    }else{

        $datonoticias = $_noticias->obtenerNoticiasActivos();
        header('Content-Type: application/json');
        echo json_encode($datonoticias);
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