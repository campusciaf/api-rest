<?php

require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

define('FACEBOOK_APP_ID', 'YOUR-APP-ID');
define('FACEBOOK_APP_SECRET', 'YOUR-APP-SECRED');
define('FACEBOOK_REDIRECT_URI', 'YOUR-REDIRECT-URI');
define('ENDPOINT_BASE', 'https://graph.facebook.com/v14.0/');

// id de la pagina
$pageId = '';

class instagram extends ConexionCrud{

// estructura de punto final de instagram
private $endpointFormat = 'https://graph.facebook.com/v14.0/{ig-user-id}?fields=business_discovery.username({ig-username}){username,website,name,ig_id,id,profile_picture_url,biography,follows_count,followers_count,media_count,media{id,caption,like_count,comments_count,timestamp,username,media_product_type,media_type,owner,permalink,media_url,children{media_url}}}&access_token={access-token}';
private $instagramAccountId = '17841404446954467';
// Punto final de Instagram con ID de cuenta real
private $endpoint =  'https://graph.facebook.com/v14.0/17841404446954467';
private $accessToken = 'EAAQTItqBcocBAK5sfCVFjrzYW2rdNRKdJivYY4fPL2lbhVZAaeKhkZCFJ4lKnVP5jLsO2r2ZC8xfr8YC5KhC01Es7zgxotvUCefn18ig9TlvZAcsd77dSBZCRaeEKXQ3lVPeBah2ZArWCygbm9NrFjOlUdjM1IeVZATpVYzeLtI3YW72JKUbQZAfDBdlVTyOGLGUAFB34ehXSAZDZD'; 

// public $users = array();
// public $users[] = getUserInfoAndPosts();


function getUserInfoAndPosts( ) {

	$instagramAccountId = '17841404446954467';
	$username='comunidadciaf';
	$accessToken = 'EAAQTItqBcocBAK5sfCVFjrzYW2rdNRKdJivYY4fPL2lbhVZAaeKhkZCFJ4lKnVP5jLsO2r2ZC8xfr8YC5KhC01Es7zgxotvUCefn18ig9TlvZAcsd77dSBZCRaeEKXQ3lVPeBah2ZArWCygbm9NrFjOlUdjM1IeVZATpVYzeLtI3YW72JKUbQZAfDBdlVTyOGLGUAFB34ehXSAZDZD'; 
	$endpoint =  'https://graph.facebook.com/v14.0/' . $instagramAccountId;

	// parámetros de punto final
	$igParams = array(
		'fields' => 'business_discovery.username(' . $username . '){username,website,name,ig_id,id,profile_picture_url,biography,follows_count,followers_count,media_count,media{id,caption,like_count,comments_count,timestamp,username,media_product_type,media_type,owner,permalink,media_url,media_thumbnail_url,children{media_url}}}',
		'access_token' => $accessToken
	);

	// agregar parámetros al punto final
	$endpoint .= '?' . http_build_query( $igParams );

	// configuración del curl
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $endpoint );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

	// llamar y obtener respuesta
	$response = curl_exec( $ch );

	// cerrar la llamada del curl
	curl_close( $ch );

	// devolver buena matriz
	return json_decode( $response, true );
}


    public function obtenerinstagramActivos(){
        $_respuestas = new respuestas;

        date_default_timezone_set("America/Bogota");	
        $fecha = date('Y-m-d');

        // if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
        //     return $_respuestas->error_401();
            
        // }else{

        // $query = "SELECT * FROM " . $this->table . " WHERE 	fecha_inicio >= $fecha";
        // return parent::listar($query);

        // }


       
    }

    public function obtenerinstagramId($id){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE id_evento= '$id'";
        return parent::listar($query);

        }

       
    }


}
?>