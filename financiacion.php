<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/financiacion.class.php';
require_once 'clases/yeminus.class.php';

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
$financiacion = new Financiacion();
$yeminus = new YeminusAPI();

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['action'])) $action = $input['action'];
}

switch ($action) {

    case 'getCurrentInstallments':
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $identificacion = isset($data['user_identificacion']) ? trim($data['user_identificacion']) : '';

        if ($identificacion === '') {
            http_response_code(400);
            echo json_encode($responses->errorResponse('Identificación requerida'));
            exit;
        }

        $creditos_activos = $financiacion->creditosActivos($identificacion);

        if (!$creditos_activos || count($creditos_activos) === 0) {
            echo json_encode($responses->successResponse([
                'message' => 'No hay créditos activos',
                'creditos' => []
            ]));
            exit;
        }

        $resultado = [];
        foreach ($creditos_activos as $credito) {
            $consecutivo = $credito["id"];
            $motivo_financiacion = $credito["motivo_financiacion"];

            $cuotas = $financiacion->traerCuotas($consecutivo);

            if (!$cuotas || count($cuotas) === 0) {
                continue;
            }

            $valor_acumulado = 0;
            $fecha_limite = "";
            $pago_inmediato = false;

            foreach ($cuotas as $cuota) {
                $fecha_pago = $cuota["fecha_pago"];
                $valor_cuota = $cuota["valor_cuota"];
                $valor_pagado = $cuota["valor_pagado"];
                $valor_restante = $valor_cuota - $valor_pagado;

                $valor_acumulado += $valor_restante;

                if (date("Y-m-d") < $fecha_pago && !$pago_inmediato) {
                    $fecha_limite = $financiacion->fechaesp($cuota["fecha_pago"]);
                } else {
                    $fecha_limite = "Inmediato";
                    $pago_inmediato = true;
                }
            }

            $formato_cuota = $financiacion->formatoDinero($valor_acumulado);

            $document_filter = array(
                "filtrar" => true,
                "tiposDocumentos" => array("FV"),
                "prefijos" => array("ELEC", "API"),
                "numero" => $credito["id"]
            );
            $rspta = $yeminus->ConsultarFacturaVenta($document_filter);

            $resultado[] = [
                'id_credito' => $consecutivo,
                'motivo' => $motivo_financiacion,
                'valor_total_pendiente' => $valor_acumulado,
                'valor_formato' => $formato_cuota,
                'fecha_limite_pago' => $fecha_limite,
                'pago_inmediato' => $pago_inmediato,
                'documento_yeminus' => $rspta["documentos"][0]["codigoTercero"],
                'prefijo' => $rspta["documentos"][0]["prefijo"],
                'tipoDocumento' => $rspta["documentos"][0]["tipoDocumento"],
            ];
        }

        echo json_encode($responses->successResponse([
            'message' => 'Créditos activos obtenidos correctamente',
            'creditos' => $resultado
        ]));
        break;

    case 'PagarCuota':
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $tipo_pago = $data["tipo_pago"] ?? "";
        $otro_valor = $data["otro_valor"] ?? "";
        $input_pagar_minimo = $data["input_pagar_minimo"] ?? "";
        $input_pagar_total = $data["input_pagar_total"] ?? "";
        $input_mora = $data["input_mora"] ?? "";
        $consecutivo = $data["consecutivo_pago"] ?? "";
        $documento_yeminus = $data["documento_yeminus"] ?? "";
        $prefijo = $data["prefijo"] ?? "FV";
        $tipoDocumento = $data["tipoDocumento"] ?? "";
        $motivo_financiacion = $data["motivo"] ?? "";
        $numero_documento = $data["documento_yeminus"] ?? "";

        if (!$consecutivo || !$documento_yeminus) {
            echo json_encode(["success" => false, "message" => "Datos obligatorios faltantes"]);
            exit;
        }

        if ($tipo_pago === "pago_minimo") {
            $total_enviar = $input_pagar_minimo;
        } elseif ($tipo_pago === "pago_total") {
            $total_enviar = $input_pagar_total;
        } elseif ($tipo_pago === "pago_parcial") {
            $total_enviar = $otro_valor;
        } else {
            echo json_encode(["success" => false, "message" => "Tipo de pago obligatorio"]);
            exit;
        }

        $rsta = $financiacion->verInfoSolicitante($consecutivo);

        if ($rsta) {
            $id_persona = $rsta[0]['id_persona'];

            extract($rsta);
            /*
            // HTML de ePayco
            $html = '
            <form class="col-6">
                <script src="https://checkout.epayco.co/checkout.js" 
                    data-epayco-key="8b4e82b040c208b31bc5be3f33830392" 
                    class="epayco-button" 
                    data-epayco-amount="' . $total_enviar . '" 
                    data-epayco-tax="0"
                    data-epayco-tax-base="' . $total_enviar . '"
                    data-epayco-name="Pago crédito ' . $motivo_financiacion . '" 
                    data-epayco-description="Pago crédito # ' . $consecutivo . ' CC. ' . $numero_documento . '" 
                    data-epayco-extra1="' . $id_persona . '"
                    data-epayco-extra2="' . $consecutivo . '"
                    data-epayco-extra3="' . $tipo_pago . '"
                    data-epayco-extra4="' . $consecutivo . '"
                    data-epayco-extra5="21"
                    data-epayco-extra6="11100611"
                    data-epayco-extra7="' . $prefijo . '"
                    data-epayco-extra8="' . $tipoDocumento . '"
                    data-epayco-extra9="' . $documento_yeminus . '"
                    data-epayco-extra10="' . $input_mora . '"
                    data-epayco-currency="cop"    
                    data-epayco-country="CO" 
                    data-epayco-test="false" 
                    data-epayco-external="true"
                    data-epayco-response="https://ciaf.digital/vistas/gracias.php"  
                    data-epayco-confirmation="https://ciaf.digital/vistas/pagosagregadorsofi.php" 
                    data-epayco-button="https://ciaf.digital/public/img/pago-efectivo.webp"> 
                </script> 
            </form>
        
            <form class="col-6">
                <script src="https://checkout.epayco.co/checkout.js"
                    data-epayco-key="d4b482f39f386634f5c50ba7076eecff" 
                    class="epayco-button" 
                    data-epayco-amount="' . $total_enviar . '" 
                    data-epayco-tax="0"
                    data-epayco-tax-base="' . $total_enviar . '"
                    data-epayco-name="Pago crédito ' . $motivo_financiacion . '" 
                    data-epayco-description="Pago crédito # ' . $consecutivo . ' CC. ' . $numero_documento . '" 
                    data-epayco-extra1="' . $id_persona . '"
                    data-epayco-extra2="' . $consecutivo . '"
                    data-epayco-extra3="' . $tipo_pago . '"
                    data-epayco-extra4="' . $consecutivo . '"
                    data-epayco-extra5="16"
                    data-epayco-extra6="11100506"
                    data-epayco-extra7="' . $prefijo . '"
                    data-epayco-extra8="' . $tipoDocumento . '"
                    data-epayco-extra9="' . $documento_yeminus . '"
                    data-epayco-extra10="' . $input_mora . '"
                    data-epayco-currency="cop"    
                    data-epayco-country="CO" 
                    data-epayco-test="false" 
                    data-epayco-external="true" 
                    data-epayco-response="https://ciaf.digital/vistas/gracias.php"  
                    data-epayco-confirmation="https://ciaf.digital/vistas/pagosagregadorsofi.php" 
                    data-epayco-button="https://ciaf.digital/public/img/pagos-pse.webp"> 
                </script> 
            </form>
            ';
            */
            
            $efestivo = array(
                "key" => "8b4e82b040c208b31bc5be3f33830392",
                "amount" => $total_enviar,
                "tax" => "0",
                "tax_base" => $total_enviar,
                "name" => "Pago crédito " . $motivo_financiacion,
                "description" => "Pago crédito # " . $consecutivo . " CC. " . $numero_documento,
                "extra1" => $id_persona,
                "extra2" => $consecutivo,
                "extra3" => $tipo_pago,
                "extra4" => $consecutivo,
                "extra5" => "21",
                "extra6" => "11100611",
                "extra7" => $prefijo,
                "extra8" => $tipoDocumento,
                "extra9" => $documento_yeminus,
                "extra10" => $input_mora,
                "currency" => "cop",
                "country" => "CO",
                "test" => "false",
                "external" => "true",
                "response" => "https://ciaf.digital/vistas/gracias.php",
                "confirmation" => "https://ciaf.digital/vistas/pagosagregadorsofi.php"
            );

            $pse = array(
                "key" => "d4b482f39f386634f5c50ba7076eecff",
                "amount" => $total_enviar,
                "tax" => "0",
                "tax_base" => $total_enviar,
                "name" => "Pago crédito " . $motivo_financiacion,
                "description" => "Pago crédito # " . $consecutivo . " CC. " . $numero_documento,
                "extra1" => $id_persona,
                "extra2" => $consecutivo,
                "extra3" => $tipo_pago,
                "extra4" => $consecutivo,
                "extra5" => "16",
                "extra6" => "11100506",
                "extra7" => $prefijo,
                "extra8" => $tipoDocumento,
                "extra9" => $documento_yeminus,
                "extra10" => $input_mora,
                "currency" => "cop",
                "country" => "CO",
                "test" => "false",
                "external" => "true",
                "response" => "https://ciaf.digital/vistas/gracias.php",
                "confirmation" => "https://ciaf.digital/vistas/pagosagregadorsofi.php"
            );

            echo json_encode([
                "success" => true,
                "efectivo" => $efestivo,
                "pse" => $pse
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "No se encontró información del solicitante"]);
        }
        break;


    default:
        http_response_code(400);
        echo json_encode($responses->errorResponse('Acción no valida'));
        break;
}
