<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class egresados extends ConexionCrud{

    private $table= "credencial_estudiante";
    private $table2= "egresados_caracterizacion";


    private $id_credencial= "";
    private $egresados_tiene_hijos= "";
    private $egresados_num_hijos= "";
    private $egresados_trabaja= "";
    private $egresados_tipo_trabajador= "";
    private $egresados_empresa= "";
    private $egresados_sector_empresa= "";
    private $egresados_cargo= "";
    private $egresados_profesion= "";
    private $egresados_salario= "";
    private $egresados_estudio_adicional= "";
    private $egresados_formacion= "";
    private $egresados_tipo_formacion= "";
    private $egresados_informacion= "";
    private $egresados_posgrado= "";
    private $egresados_colaborativa= "";
    private $egresados_actualizacion= "";
    private $egresados_recomendar= "";

    public function obteneregresadosId($id){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

        $query = "SELECT * FROM " . $this->table . " WHERE credencial_identificacion= '$id'";
        return parent::listar($query);

        }

       
    }

    public function obtenerDocVerificar($id_credencial){
        $_respuestas = new respuestas;
       

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ){
            return $_respuestas->error_401();
            
        }else{

            $query = "SELECT * FROM " . $this->table2 . " WHERE id_credencial='$id_credencial'";
            $datos=parent::listar($query);

            if(!empty($datos)){
                return $datos;

            }else{
                date_default_timezone_set("America/Bogota");
                $fecha= date("Y-m-d");

                $query2 = "INSERT INTO " . $this->table2 . " (id_credencial,fechaaceptodata)
                values('$id_credencial','$fecha')";
                $resp = parent::nonQueryId($query2);
                if($resp >= 1){
                    $query3 = "SELECT * FROM " . $this->table2 . " WHERE id_credencial='$id_credencial'";
                    $datos2=parent::listar($query3);
                    return $datos2;
                }
                else{
                    return $_respuestas->error_400();
                }
                
            }

        }

       
    }

    public function formularioEditar($json){// PUT toma los datos del formulario para editar un usuario
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['id_credencial'])){//esto es para actualizar el perfil del cliente
            return $_respuestas->error_400();
        }else{
            if(isset($datos["id_credencial"])) { $this->id_credencial = $datos['id_credencial']; }
            if(isset($datos["egresados_tiene_hijos"])) { $this->egresados_tiene_hijos = $datos['egresados_tiene_hijos']; }
            if(isset($datos["egresados_num_hijos"])) { $this->egresados_num_hijos = $datos['egresados_num_hijos']; }
            if(isset($datos["egresados_trabaja"])) { $this->egresados_trabaja = $datos['egresados_trabaja']; }
            if(isset($datos["egresados_tipo_trabajador"])) { $this->egresados_tipo_trabajador = $datos['egresados_tipo_trabajador']; }
            if(isset($datos["egresados_empresa"])) { $this->egresados_empresa = $datos['egresados_empresa']; }
            if(isset($datos["egresados_sector_empresa"])) { $this->egresados_sector_empresa = $datos['egresados_sector_empresa']; }
            if(isset($datos["egresados_cargo"])) { $this->egresados_cargo = $datos['egresados_cargo']; }
            if(isset($datos["egresados_profesion"])) { $this->egresados_profesion = $datos['egresados_profesion']; }
            if(isset($datos["egresados_salario"])) { $this->egresados_salario = $datos['egresados_salario']; }
            if(isset($datos["egresados_estudio_adicional"])) { $this->egresados_estudio_adicional = $datos['egresados_estudio_adicional']; }
            if(isset($datos["egresados_formacion"])) { $this->egresados_formacion = $datos['egresados_formacion']; }
            if(isset($datos["egresados_tipo_formacion"])) { $this->egresados_tipo_formacion = $datos['egresados_tipo_formacion']; }
            if(isset($datos["egresados_informacion"])) { $this->egresados_informacion = $datos['egresados_informacion']; }
            if(isset($datos["egresados_posgrado"])) { $this->egresados_posgrado = $datos['egresados_posgrado']; }
            if(isset($datos["egresados_colaborativa"])) { $this->egresados_colaborativa = $datos['egresados_colaborativa']; }
            if(isset($datos["egresados_actualizacion"])) { $this->egresados_actualizacion = $datos['egresados_actualizacion']; }
            if(isset($datos["egresados_recomendar"])) { $this->egresados_recomendar = $datos['egresados_recomendar']; }
            if(isset($datos["egresados_recomendar"])) { $this->egresados_recomendar = $datos['egresados_recomendar']; }


            $resp = $this->editarCliente();
            if($resp){
                $respuesta = $_respuestas->response;
                $respuesta["result"] = array(
                    "id_credencial" => $this->id_credencial
                );
                
                return $respuesta;
            }else{
                return $resp;
            }
        }


        
    }

    private function editarCliente(){// modelo para editar los datos del cliente
        date_default_timezone_set("America/Bogota");		
        $fecha = date('Y-m-d');
        $hora = date('h:i:s');

        $query= "UPDATE ".$this->table2." SET 
        `egresados_tiene_hijos`='" .$this->egresados_tiene_hijos."', 
        `egresados_num_hijos`='" .$this->egresados_num_hijos."',
        `egresados_trabaja`='" .$this->egresados_trabaja."',
        `egresados_tipo_trabajador`='" .$this->egresados_tipo_trabajador."',
        `egresados_empresa`='" .$this->egresados_empresa."',
        `egresados_sector_empresa`='" .$this->egresados_sector_empresa."',
        `egresados_cargo`='" .$this->egresados_cargo."',
        `egresados_profesion`='" .$this->egresados_profesion."',
        `egresados_salario`='" .$this->egresados_salario."',
        `egresados_estudio_adicional`='" .$this->egresados_estudio_adicional."',
        `egresados_formacion`='" .$this->egresados_formacion."', 
        `egresados_tipo_formacion`='" .$this->egresados_tipo_formacion."',
        `egresados_informacion`='" .$this->egresados_informacion."',
        `egresados_posgrado`='" .$this->egresados_posgrado."',
        `egresados_colaborativa`='" .$this->egresados_colaborativa."',
        `egresados_actualizacion`='" .$this->egresados_actualizacion."',
        `egresados_recomendar`='" .$this->egresados_recomendar."' 
        WHERE `id_credencial`='" . $this->id_credencial . "'"; 
  
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }
        else{
            return 0;
        }
    }

    
    
    
    
    
    
    
    
    
    
    
    
    





}
?>