<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class cvAdmin extends ConexionCrud{

    private $table= "cvadministrativos";
    private $table2= "dependencias";
    
    private $identificacion="";
    private $nombre ="";
    private $correo ="";
    private $celular ="";
    private $cargo ="";



    
    public function traerCargos(){
        $query = "SELECT * FROM " . $this->table2 . " WHERE vacantes=1";
        return parent::listar($query);
    }

    public function insertarcv($json){
        $_respuestas = new respuestas;
        $datos= json_decode($json,true);
       
        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

            date_default_timezone_set("America/Bogota");		
            $fecha = date('Y-m-d');
           

            if(!isset($_POST["identificacion"]) || !isset($_POST["nombre"]) || !isset($_POST["correo"]) || !isset($_POST["celular"]) || !isset($_POST["cargo"])){
                    return $_respuestas->error_400();
            }else{
                  
                $this->identificacion=$_POST["identificacion"];
                $this->nombre=$_POST["nombre"];
                $this->correo=$_POST["correo"];
                $this->celular=$_POST["celular"];
                $this->cargo=$_POST["cargo"];
               
                // validar que no exista la cedula//

                $verificarcc = $this->verificar($this->identificacion);
                $verificarcorreo = $this->verificarcorreo($this->correo);
                $verificarcelular = $this->verificarcelular($this->celular);

                if($verificarcc == 0 && $verificarcorreo == 0 && $verificarcelular == 0){// si no existe un registro con esso datos

                    // upload.php
                    $uploadDirectory = '../cvadmin/';
                    $uploadFile = $uploadDirectory . basename($_FILES['file']['name']);

                    // Obtener el nombre del archivo
                    $fileName =basename($_FILES['file']['name']);//nombre del documento
                    $fileTmpName = $_FILES['file']['tmp_name'];
                    $nombrefichero=$this->identificacion.'_'.$fileName;

                    // Obtener la extensión del archivo
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    // Generar un nuevo nombre único para el archivo
                    $newFileName = $nombrefichero. '.' . $fileExtension;
                    // Crear la ruta completa del archivo con el nuevo nombre
                    $uploadFile = $uploadDirectory . $newFileName;

                    $file_size = $_FILES['file']['size'];
                    $file_size_kb = $file_size / 1024;       // Tamaño en KB
                    $file_size_mb = $file_size_kb / 1024;    // Tamaño en MB
                    


                    if($fileExtension =='pdf' && $file_size_mb < 5 ){// debe ser pdf
                    
                        // Verificar si el directorio de carga existe, si no, crearlo
                        if (!is_dir($uploadDirectory)) {
                            mkdir($uploadDirectory, 0777, true);
                        }

                        // Mover el archivo al directorio de carga
                        if (move_uploaded_file($fileTmpName, $uploadFile)) {


                            $query ="INSERT INTO " . $this->table . " (cvadministrativos_identificacion,cvadministrativos_nombre,cvadministrativos_correo,cvadministrativos_celular,cvadministrativos_cargo,cvadministrativos_pdf,cvadministrativos_fecha) 
                            values ('". $this->identificacion."','". $this->nombre."','". $this->correo."','".$this->celular."','".$this->cargo."','".$nombrefichero."','". $fecha."') ";
                            
                            $resp = parent::nonQueryId($query);
                            if($resp){
            
                                return $resp;
                            }
                            else{
                                return 0;
                            }


                        }else {
                            return 'Error al subir el archivo.';
                        }
                    }else{
                        return 'pdf';
                    }

                

                }else{
                    return 'Existe';
                }
                /* *************************** */

                
            }

        }

    }

    private function verificar($identificacion){
        $query = "SELECT * FROM " . $this->table . " WHERE cvadministrativos_identificacion='".$identificacion."'";
        $resp = parent::listar($query);
            if($resp){

                return $resp;
            }
            else{
                return 0;
            }
    }

    private function verificarcorreo($correo){
        $query = "SELECT * FROM " . $this->table . " WHERE cvadministrativos_correo='".$correo."'";
        $resp = parent::listar($query);
            if($resp){

                return $resp;
            }
            else{
                return 0;
            }
    }

    private function verificarcelular($celular){
        $query = "SELECT * FROM " . $this->table . " WHERE cvadministrativos_celular='".$celular."'";
        $resp = parent::listar($query);
            if($resp){

                return $resp;
            }
            else{
                return 0;
            }
    }

    

}
?>

