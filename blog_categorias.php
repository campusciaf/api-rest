<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/blog_categorias.class.php';
header("Access-Control-Allow-Origin: *");// quita el bloqueo cros 
header("Access-Control-Allow-Headers: Origin,Autorizacion");
header('Content-Type: application/json');

$_respuestas =new respuestas;
$_blog_categorias =new blog_categorias;

if($_SERVER["REQUEST_METHOD"] == "GET"){

        $data_categorias = $_blog_categorias->obtenerBlogCategorias();
        header('Content-Type: application/json');
        echo json_encode($data_categorias);
        http_response_code(200);
    

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