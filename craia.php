<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/craia.class.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Origin, autorizacion, Content-Type, Accept");

header('content-type: application/json; charset=utf-8');

$_respuestas = new respuestas;
$_craia = new craia();

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $postBody = json_decode(file_get_contents("php://input"), true);

    if(!isset($postBody['mensaje'])){
        echo json_encode($_respuestas->error_400());
        exit;
    }

    $mensaje = $postBody['mensaje'];
    $session = $postBody['session'] ?? 'web';

    $respuesta = $_craia->procesarMensaje($mensaje, $session);

    echo json_encode($respuesta);

    http_response_code(200);

}else{
    echo json_encode($_respuestas->error_405());
}
?>