<?php

require_once 'clases/respuestas.class.php';
require_once 'clases/oninteresados.class.php';
header("Access-Control-Allow-Origin: *");// quita el bloqueo cros 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, autorizacion, X-Requested-With, Content-Type, Accept, Access-Control-Request-MethodAccess-Control-Allow-Headers,Authorization");
header('content-type: application/json');





$_respuestas = new respuestas;
$_datos = new datos;

if($_SERVER["REQUEST_METHOD"] == "GET"){

    if(isset($_GET['id']) && isset($_GET['token'])){//metodo para obterner un dato por id
        
        $id_user=$_GET["id"];
        $token=$_GET["token"];
        $caso=$_GET["opcion"];

        if($_GET["opcion"]==0){
            $datos = $_datos->oninteresados($id_user,$token);
        }else{
            // $id_user en este caso es el documento 1= a soporter cedula
            $datos = $_datos->onSoportes($id_user,$caso);
        }

        header('Content-Type: application/json');
        echo json_encode($datos);
        http_response_code(200);
    }

    


}else if($_SERVER["REQUEST_METHOD"] == "POST"){

    // recibimos los datos enviados
    // recibir datos
     $postBody= file_get_contents("php://input");
     $data = json_decode($postBody, true);

    if($data['opcion']==1){
        $datosArray=$_datos->datosprograma($postBody);
    } 
    if($data['opcion']==2){
        $datosArray=$_datos->guardarPago($postBody);
    } 
    if($data['opcion']==3){
        $datosArray=$_datos->actualizarFormulario($postBody);
    } 
    if($data['opcion']==4){
        $datosArray=$_datos->insertarEntrevista($postBody);
    } 
    
   
    if(isset($datosArray["result"]["error_id"])){
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    }else{
        http_response_code(200);
    }
    echo json_encode($datosArray);

}else if($_SERVER["REQUEST_METHOD"] == "PUT"){
    $postBody= file_get_contents("php://input");// recibimos los datos enviados del formulario
    $datosArray = $_cuenta->actualizarTema($postBody);// enviamos esto al manejador
    header('Content-Type: application/json');// devolvemos una respuesta

        if(isset($datosArray["result"]["error_id"])){
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
        }else{
            http_response_code(200);
        }
        echo json_encode($datosArray);
}

else if($_SERVER["REQUEST_METHOD"] == "DELETE"){

}

else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);

}




?>