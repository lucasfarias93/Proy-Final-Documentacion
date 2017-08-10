<?php

/**
 * CONSTANTES
 */
//ESTADOS

//EXCEPCIONES
Load::negocio("excepciones/negocio_excepcion");
Load::negocio("excepciones/permiso_denegado_excepcion");

/**
 * Funciones generales para uso de la aplicacion
 */
class UtilApp {

    /**
     * Se cambian las vocales por _ para que la busqueda sea mas abarcativa
     * @param type $nombre
     * @return type
     */
    public static function normalizar_busqueda($nombre) {
        $vocales = array("ñ" => "Ñ", "á" => "_", "é" => "_", "í" => "_", "ó" => "_", "ú" => "_",
            "a" => "_", "e" => "_", "i" => "_", "o" => "_", "u" => "_",
            "A" => "_", "E" => "_", "I" => "_", "O" => "_", "U" => "_",
            "Á" => "_", "É" => "_", "Í" => "_", "Ó" => "_", "Ú" => "_",
            " " => "%"
        );
        $cad = strtr($nombre, $vocales);
        return trim($cad);
    }

    /**
     * Se cambian las vocales por _ para que la busqueda sea mas abarcativa
     * @param type $nombre
     * @return type
     */
    public static function normalizar_busqueda_acentos($nombre) {
        $vocales = array("ñ" => "Ñ", "á" => "_", "é" => "_", "í" => "_", "ó" => "_", "ú" => "_",
            "Á" => "_", "É" => "_", "Í" => "_", "Ó" => "_", "Ú" => "_",
            " " => "%"
        );
        $cad = strtr($nombre, $vocales);
        return trim($cad);
    }

    /**
     * se juntan el apellido y en nombre de la persona y se quitan espacios y se codifica a utf8
     * @param type $nombre
     * @return type
     */
    public static function normalizar_nombre($apellido, $nombre) {
        $nombre_completo = strtoupper(trim($apellido)) . ", " . trim($nombre);
        return $nombre_completo;
    }

    /**
     *  se quitan espacios y se codifica a utf8
     * @param type $nombre
     * @return type
     */
    public static function normalizar_string($string) {
        $nombre_completo = trim($string);
        return utf8_encode($nombre_completo);
    }

    /**
     * se divide el apellido y en nombre de la persona y se quitan espacios y se codifica a utf8
     * @param type $nombre
     * @return type
     */
    public static function desnormalizar_nombre($apenom) {
        $nombre = explode(",", $apenom);
        if (!$nombre) {
            return NULL;
        }
        $ret[] = utf8_encode(trim($nombre[0])); //apellido
        $ret[] = utf8_encode(trim($nombre[1]));
        return $ret;
    }

    public static function tipo_to_string($tipo_id) {
        $tipo = "";
        if (intval($tipo_id) == 1) {
            $tipo = "actanac";
        } else if (intval($tipo_id) == 2) {
            $tipo = "actadef";
        } else {
            $tipo = "actamat";
        }
        return $tipo;
    }

    public static function formatea_fecha_pantalla_to_bd($fecha) {
        if (!$fecha) {
            return "";
        }
        return date("Y-m-d", strtotime(str_replace("/", "-", $fecha)));
    }

    public static function formatea_fecha_bd_to_pantalla($fecha) {
        if (!$fecha) {
            return "";
        }
        return date("d/m/Y", strtotime($fecha));
    }

    public static function fecha_hora_actual() {
        return date("Y-m-d H:i:s");
    }

    public static function fecha_actual($formato = "Y-m-d") {
        return date($formato);
    }

    public static function buscar_tipo_tramite_segun_tipo_libro($tipo_libro) {
        switch ($tipo_libro) {
            case LIBRO_TIPO_REHABILITACION: return TRAMITE_TIPO_REHABILITACION;
            case LIBRO_TIPO_DEFUNCION: return TRAMITE_TIPO_DEFUNCION;
            case LIBRO_TIPO_INCAPACIDAD: return TRAMITE_TIPO_CAPACIDAD;
            case LIBRO_TIPO_MATRIMONIO: return TRAMITE_TIPO_MATRIMONIO;
            case LIBRO_TIPO_NACIMIENTO: return TRAMITE_TIPO_NACIMIENTO;
            case LIBRO_TIPO_PERDIDA_PATRIA_POTESTAD: return TRAMITE_TIPO_PERDIDA_PATRIA_POTESTAD;
            case LIBRO_TIPO_PRESCRIPCIONES: return TRAMITE_TIPO_PRESCRIPCION;
            default: return NULL;
        }
    }

    public static function buscar_meses($seleccione = "") {
        if ($seleccione) {
            $meses[""] = $seleccione;
        }
        $meses["1"] = "Enero";
        $meses["2"] = "Febrero";
        $meses["3"] = "Marzo";
        $meses["4"] = "Abril";
        $meses["5"] = "Mayo";
        $meses["6"] = "Junio";
        $meses["7"] = "Julio";
        $meses["8"] = "Agosto";
        $meses["9"] = "Septiembre";
        $meses["10"] = "Octubre";
        $meses["11"] = "Noviembre";
        $meses["12"] = "Dicimiembre";
        return $meses;
    }

    public static function calcular_dias_entre_fechas($fecha_desde, $fecha_hasta) {
        $datetime1 = new DateTime($fecha_desde);
        $datetime2 = new DateTime($fecha_hasta);
        $datetime3 = $datetime1->diff($datetime2);
        return $datetime3->format("%a");
    }

    public static function comienza_con_cero($numero) {
        $nro = substr($numero, 0, 1);
        if ($nro == 0) {
            return TRUE;
        }
        return FALSE;
    }

    //devuelve el primer apellido, necesario para apellidos compuestos                            
    public static function primer_apellido($apellido) {
        $ape = strtoupper(trim($apellido));
        $aux = explode(' ', $ape);
        if (count($aux) == 1) {
            return $ape;
        }
        if (count($aux) == 2) {
            if ($aux[0] == 'DE' || $aux[0] == 'DEL' || $aux[0] == 'DELL' || $aux[0] == 'DELLA' || $aux[0] == 'DELLO' || $aux[0] == 'DA' || $aux[0] == 'DAL' || $aux[0] == 'DALL' || $aux[0] == 'DALLA' || $aux[0] == 'DI' || $aux[0] == 'DO' || $aux[0] == 'DOS' || $aux[0] == 'DU' || $aux[0] == 'DÉ' || $aux[0] == 'DÍ') {
                return $ape;
            } else {
                return $aux[0];
            }
        }
        if (count($aux) == 3) {
            $aux1 = $aux[0] . " " . $aux[1];
            if ($aux1 == 'DE LA' || $aux1 == 'DE LAS' || $aux1 == 'DE LOS') {
                return $ape;
            } else {
                return $aux[0];
            }
        }
        if (count($aux) > 3) {
            return $aux[0];
        }
        if (count($aux) == 0) {
            throw new NegocioExcepcion("Error, apellido vacío", '671');
            ;
        }
    }

    public static function calcular_dias_habiles($fecha_inicial, $fecha_final, $oficina_id = NULL) {
        $dias = UtilApp::dias_habiles($fecha_inicial, $fecha_final);
        $cant = UtilApp::evaluar($dias, $oficina_id);
        return $cant;
    }

    //Cuenta la cantidad de dias que hay entre $fecha_inicial y $fecha_final
    private static function dias_habiles($fecha_inicial, $fecha_final) {
        list($dia, $mes, $year) = explode("/", $fecha_inicial);
        $ini = mktime(0, 0, 0, $mes, $dia, $year);
        list($diaf, $mesf, $yearf) = explode("/", $fecha_final);
        $fin = mktime(0, 0, 0, $mesf, $diaf, $yearf);
        $r = 1;
        while ($ini != $fin) {
            $ini = mktime(0, 0, 0, $mes, $dia + $r, $year);
            $newArray[] .= $ini;
            $r++;
        }
        return $newArray;
    }

    // Verifica que el arreglo de fechas entre la ingresadas contenga los feriados nacionales que correspondan, los sábados y domingos.
    private static function evaluar($lista_fechas, $oficina_id) {
        if (!$oficina_id) {
            $oficina_id = Auth::get("base_oficina_id");
        }
        $feriados = Load::model("base_oficina_feriados")->buscar_feriados_por_oficina($oficina_id);
        $j = count($lista_fechas);
        $aux_dias = 0;
        for ($i = 0; $i <= $j - 1; $i++) {
            $dia = $lista_fechas[$i];
            $fecha = getdate($dia);
            $feriado = $fecha['mday'] . "/" . $fecha['mon'];
            if ($fecha["wday"] == 0 or $fecha["wday"] == 6) {
                $aux_dias ++;
            } else {
                foreach ($feriados as $fe) {
                    if ($fe->fecha == $feriado) {
                        $aux_dias ++;
                    }
                }
            }
        }
        $rlt = $j - $aux_dias;
        return $rlt;
    }
}
