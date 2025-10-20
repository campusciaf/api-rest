<?php
require_once 'conexion/crud.php';
require_once 'respuestas.class.php';

class Financiacion extends ConexionCrud {

    private $table = "sofi_matricula";
    private $table2 = "sofi_financiamiento";
    private $table3 = "sofi_persona";

    private $identificacion = "";
    private $consecutivo = "";

    public function CreditosActivos($identificacion)
    {
        $_respuestas = new respuestas;

        if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ) {
            return $_respuestas->error_401();
        }else{
            $this->identificacion = $identificacion;

            $query ="SELECT
                        `sm`.`id`,
                        `sm`.`motivo_financiacion`
                    FROM `" . $this->table . "` `sm`
                    INNER JOIN `sofi_persona` `sp` ON `sm`.`id_persona` = `sp`.`id_persona`
                    WHERE `sp`.`numero_documento`
                        LIKE '%".$this->identificacion."%'
                        AND `sm`.`credito_finalizado` = 0
                    ORDER BY `id` DESC;";

            $resp = parent::listar($query);
            if ($resp) {
                return $resp;
            } else {
                return false;
            }
        }
    }

    public function traerCuotas($consecutivo)
    {
        $_respuestas = new respuestas;

        try {
            if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ) {
                return $_respuestas->error_401();
            }else{
                $this->consecutivo = $consecutivo;
                $hoy = date("Y-m-d");

                $query ="SELECT *
                        FROM `" . $this->table2 . "`
                        WHERE `id_matricula` = ".$this->consecutivo."
                            AND estado != 'Pagado'
                            AND DATE_FORMAT(`fecha_pago`, '%Y%m') <= DATE_FORMAT('".$hoy."', '%Y%m')
                            ORDER BY `fecha_pago` ASC LIMIT 1;";

                $resp = parent::listar($query);

                if ($resp) {
                    return $resp;
                } else {
                    return false;
                }
            }
        } catch (\Throwable $th) {
            print_r($th);
            return false;
        }
    }

    public function verInfoSolicitante($consecutivo)
    {
        $_respuestas = new respuestas;

        try {
            if(!isset(getallheaders()["Autorizacion"]) || getallheaders()["Autorizacion"] != 'KFTDQFYvqbPLXkHTuXQJR4Qy3vUryK' ) {
                return $_respuestas->error_401();
            }else{
                $this->consecutivo = $consecutivo;

                $query ="SELECT *
                        FROM (`" . $this->table3 . "`
                        INNER JOIN `" . $this->table . "` ON `" . $this->table . "`.id_persona = `" . $this->table3 . "`.`id_persona`)
                        WHERE `" . $this->table . "`.`id` = ".$this->consecutivo.";";

                $resp = parent::listar($query);

                if ($resp) {
                    return $resp;
                } else {
                    return false;
                }
            }
        } catch (\Throwable $th) {
            print_r($th);
            return false;
        }
    }

    public function fechaesp($date)
    {
        $dia     = explode("-", $date, 3);
        $year     = $dia[0];
        $month     = (string)(int)$dia[1];
        $day     = (string)(int)$dia[2];

        $dias         = array("domingo", "lunes", "martes", "mi&eacute;rcoles", "jueves", "viernes", "s&aacute;bado");
        $tomadia     = $dias[intval((date("w", mktime(0, 0, 0, $month, $day, $year))))];

        $meses = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");

        return $tomadia . ", " . $day . " de " . $meses[$month] . " de " . $year;
    }

    public function formatoDinero($valor)
    {
        $moneda = array(2, ',', '.'); // Peso colombiano 
        return number_format($valor, $moneda[0], $moneda[1], $moneda[2]);
    }
}