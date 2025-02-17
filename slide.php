<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/slide.class.php';
header("Access-Control-Allow-Origin: *");// quita el bloqueo cros 
header("Access-Control-Allow-Methods: PUT, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, autorizacion, X-Requested-With, Content-Type, Accept, Access-Control-Request-MethodAccess-Control-Allow-Headers,Authorization");
header('content-type: application/json');


$_respuestas =new respuestas;
$_slide =new slide;

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_GET["page"])){
        $pagina=$_GET["page"];
        $listar_slide=$_slide->listarSlide($pagina);
        header('Content-Type: application/json');
        echo json_encode($listar_slide);
        http_response_code(200);
    }else if(isset($_GET['id'])){
        $slideid= $_GET['id'];
        $datoslide = $_slide->obtenerSlide($slideid);
        header('Content-Type: application/json');
        echo json_encode($datoslide);
        http_response_code(200);
    }else{
        $datoslide = $_slide->obtenerSlideActivos();
        header('Content-Type: application/json');
        echo json_encode($datoslide);
        http_response_code(200);
        
    }

}else if($_SERVER["REQUEST_METHOD"] == "POST"){
    // recibimos los datos enviados
    $postBody= file_get_contents("php://input");
    // enviamos esto al manejador
    $datosArray = $_slide->insertarSlide($postBody);
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