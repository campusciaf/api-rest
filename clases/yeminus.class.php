<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class YeminusAPI extends ConexionCrud {

    private $baseUrl;
    private $userID;
    private $userPassword;
    private $empresa_id;

    public function ConsultarFacturaVenta($document_filter) {

        $this->baseUrl = "http://190.60.105.182/apiyeminus";
        $this->userID = "API";
        $this->userPassword = "CIAF123";
        $this->empresa_id = "02";

        // Obtener el token si aún no lo tienes
        $token = $this->getTokenActual();

        if (!$token) {
            return null;
        }
        // URL de la solicitud
        $url = $this->baseUrl . "/api/inventarios/documentos/obtenercabeceradocumento";
        // Datos del cuerpo de la solicitud
        $raw_body = json_encode(array(
            "filtroDocumento" => $document_filter
        ));
        // Inicializar cURL
        $ch = curl_init();
        // Configurar las opciones de cURL
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $raw_body,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $token",
                "id_empresa: " . $this->empresa_id,
                "usuario: " . $this->userID,
                "Throttle-key: 39a3af33665a439c9cb2a80532465eb1"
            ),
            CURLOPT_RETURNTRANSFER => true
        ));
        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Decodificar el JSON recibido para eliminar espacios innecesarios
        $json_decodificado = json_decode($response);
        // Volver a codificarlo en una sola línea
        $json_una_linea = json_encode($json_decodificado);
        // Archivo donde guardaremos los datos del webhook para revisión
        $archivo_log = 'yeminus_log.txt';
        //almacenar los datos en un archivo de texto para verficar
        file_put_contents($archivo_log, $json_una_linea . "\n", FILE_APPEND);
        // Cerrar cURL
        curl_close($ch);
        $jsonData = json_decode($response, true);
        if ($httpCode == 200 && isset($jsonData['esExitoso']) && $jsonData['esExitoso']) {
            return $jsonData['datos'];
        } else {
            return null;
        }
    }

    public function getTokenActual()
    {
        $_respuestas = new respuestas;

        try {
            $query ="SELECT * FROM `yeminus_token`;";

            $resp = parent::listar($query);

            if (isset($resp[0]['access_token']) && $resp[0]['token_type'] == "bearer") {
                return $resp[0]['access_token'];
            } else {
                return null;
            }
        } catch (\Throwable $th) {
            print_r($th);
            return false;
        }
    }

}