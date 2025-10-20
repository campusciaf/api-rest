<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/usuario.class.php';
require_once 'clases/financiacion.class.php';

header("Access-Control-Allow-Origin: *");
// header('Access-Control-Allow-Origin: https://ciaf.edu.co/');
header('Access-Control-Allow-Origin: http://localhost:4200');
header("Access-Control-Allow-Headers: Origin,Autorizacion");
header("Access-Control-Allow-Headers: Origin, autorizacion, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$responses = new Respuestas();
$usuario = new Usuario();
$financiacion = new Financiacion();

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['action'])) $action = $input['action'];
}

switch ($action) {

    case 'validateStudent':
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $identificacion = isset($data['user_identificacion']) ? trim($data['user_identificacion']) : '';
        $credencial = isset($data['user_credencial']) ? trim($data['user_credencial']) : '';

        if ($identificacion === '' || $credencial === '') {
            http_response_code(400);
            echo json_encode($responses->errorResponse('Faltan campos obligatorios'));
            exit;
        }

        if (!preg_match('/^[0-9]{6,20}$/', $identificacion)) {
            http_response_code(400);
            echo json_encode($responses->errorResponse('Formato de identificación inválido'));
            exit;
        }

        $exists = $usuario->validarEstudiante($identificacion, $credencial);
        if ($exists === true) {
            echo json_encode($responses->successResponse(['message' => 'Estudiante validado']));
        } else {
            http_response_code(404);
            echo json_encode($responses->errorResponse('Estudiante no encontrado o credencial inválida'));
        }
        break;

    default:
        http_response_code(400);
        echo json_encode($responses->errorResponse('Acción no valida'));
        break;
}
