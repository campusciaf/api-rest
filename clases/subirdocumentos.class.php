<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';


class datos extends ConexionCrud{



    public function cargarDocumento($json){

        date_default_timezone_set("America/Bogota");
        $fecha= date("Y-m-d");
        $hora=date("H:m:s");

        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        // Obtener el valor de usuario desde la variable POST
       $id_estudiante = $_POST['id_estudiante'] ?? '';
       $documento = $_POST['documento'] ?? '';
      
        // upload.php
        $uploadDirectory = '';
        switch ($documento) {
            case 1:
                $uploadDirectory = '../oncenter/img_cedula/';
                break;
            case 2:
                $uploadDirectory = '../oncenter/img_diploma/';
                break;
            case 3:
               $uploadDirectory = '../oncenter/img_acta/';
                break;
            case 4:
                $uploadDirectory = '../oncenter/img_salud/';
                break;
            case 5:
                $uploadDirectory = '../oncenter/img_prueba/';
                break;
            case 6:
                $uploadDirectory = '../oncenter/img_compromiso/';
                break;
            case 7:
                $uploadDirectory = '../oncenter/img_proteccion_datos/';
                break;
            default:
                // Manejo de caso por defecto si es necesario
                $uploadDirectory = '../oncenter/';
            break;
        }

        
        // $uploadFile = $uploadDirectory . basename($_FILES['file']['name']);

        // Obtener el nombre del archivo
        
        $fileName =basename($_FILES['file']['name']);//nombre del documento
        $fileTmpName = $_FILES['file']['tmp_name'];
        $nombreficheroSinExtension = pathinfo($fileName, PATHINFO_FILENAME);
        $nombrefichero=$id_estudiante.'_'.$nombreficheroSinExtension;

        

        // Obtener la extensión del archivo
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        // Generar un nuevo nombre único para el archivo
        $newFileName = $nombreficheroSinExtension . '.' . $fileExtension;
        // Crear la ruta completa del archivo con el nuevo nombre
        //$uploadFile = $uploadDirectory . $newFileName;

        $uploadFile = $id_estudiante . '_' . $nombreficheroSinExtension . '.' . $fileExtension;
        // Crear la ruta completa con el nuevo nombre del archivo
        $targetFilePath = $uploadDirectory . $uploadFile;
 

        if($fileExtension == 'pdf' or $fileExtension == 'jpg' or $fileExtension == 'png' or $fileExtension == 'jpeg'){// debe ser pdf

            // Verificar si el directorio de carga existe, si no, crearlo
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }

            // Mover el archivo al directorio de carga
            if (move_uploaded_file($fileTmpName, $targetFilePath)) {

                $registrar = $this->insertar($id_estudiante,$uploadFile,$documento);
                if($registrar){
                    return 'ok';//Archivo subido con éxito.
                }else{
                    //error
                    return $_respuestas->error_500("error interno, no hemos podido guardar");
                }


            } else {
                return 'Error al subir el archivo.';
            }
        }else{
            return 'nopdf';
        }

    
    }


    private function insertar($id_estudiante,$nombre_archivo,$documento){

        date_default_timezone_set("America/Bogota");
        $fecha= date("Y-m-d");
        $hora=date("H:m:s");
       

        
        switch ($documento) {
            case 1:
                $tabla = "on_soporte_cedula ";
                break;
            case 2:
                $tabla = "on_soporte_diploma ";
                break;
            case 3:
                $tabla = "on_soporte_acta ";
                break;
            case 4:
                $tabla = "on_soporte_salud ";
                break;
            case 5:
                $tabla = "on_soporte_prueba ";
                break;
            case 6:
                $tabla = "on_soporte_compromiso ";
                break;
            case 7:
                $tabla = "on_soporte_proteccion_datos ";
                break;
            default:
                // Manejo de caso por defecto si es necesario
                $tabla = "";
                break;
        }
          

        $query = "INSERT INTO ".$tabla." (`id_estudiante`, `nombre_archivo`, `fecha_subida`, `hora_subida`, `usuario_subida`)
        values('$id_estudiante','$nombre_archivo','$fecha','$hora','1')";
        $resp = parent::nonQueryId($query);
        if($resp){
            return $resp;
        }
        else{
            return 0;
        }

    }


}

?>