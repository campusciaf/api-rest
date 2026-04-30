<?php 
require_once "global.php";

try{
    $mbd = new PDO(
    'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
    DB_USERNAME,
    DB_PASSWORD
);

$mbd->exec("set names utf8mb4");


}catch (PDOException $e) {
    echo "¡Error!: " . $e->getMessage() . "<br/>";
    die();
}



if (!function_exists('limpiarCadena')){
	
	function limpiarCadena($str){
		return $str;
	}
}




?>