<?php

Load::models('parentesco');
Load::negocio('experto_imagen');

class IndexController extends AdminController {

    protected function before_filter() {
        view::select(NULL, 'menu');
    }

    public function index() {
        
    }

    public function buscar_imagen() {
        try {
            if (Input::hasPost('tipolibro') && Input::hasPost('parentesco')) {
                $servicio = "http://localhost:8000/RCWebService.asmx/nacimiento_propia?wsdl"; //url del servicio
                $parametros['dni'] = Auth::get("dni");
                $tipo = Input::post('tipolibro');
                $parentesco = Input::post('parentesco');
                $parametros['tipo'] = $tipo; //es lo mismo con comillas simples que dobles
                $parametros['parentesco'] = $parentesco; //es lo mismo con comillas simples que dobles
                $client = new SoapClient($servicio);
                $result = $client->nacimiento_propia($parametros); //llamamos al métdo que nos interesa con los parámetros 
                $datos = $result->nacimiento_propiaResult->Objetos;
                if (!isset($datos->ubicacion)) {
                    throw new NegocioExcepcion("No existen datos");
                }
                $ubicacion = str_replace("-", "/", $datos->ubicacion);
                $ubicacion = str_replace("Q:-ActasEscaneadas", "", $ubicacion);
                $ext = "png";
                $tmp = str_replace("TIF", "", $datos->nombre);
                $ruta_temporal_crop_original = Config::get("config.application.carpeta_temporal_original") . "crop/" . $tmp . $ext;
                $ruta = ExpertoImagen::obtener_ruta_completa($ubicacion . "/$datos->nombre");
                if (!file_exists($ruta)) {
                    Flash::error("No existe el acta");
                    throw new NegocioExcepcion("No existe el acta");
                }
                $dto = ExpertoImagen::convertir_imagen($ruta, ESTAMPA_CONSULTA);
                $ret[] = $dto;
                View::json($ret);
                session::set("tipolibro", $tipo);
                session::set("parentesco", $parentesco);
            } else {
                throw new NegocioExcepcion("no se han pasado los parametros");
            }
        } catch (NegocioExcepcion $ex) {
            view::select(null, null);
        }
    }

    public function buscar_parentesco_tipolibro() {

        if (Input::hasPost('tipolibro')) {
            $tr = new Parentesco();
            $listParentesco = $tr->filtrar_parentesco_por_tipolibro(input::post('tipolibro'));
            view::json($listParentesco);
        }
    }

    public function buscar_parentesco_tipolibro_mobile($tipolibro) {
        view::select(null, null);
        $tr = new Parentesco();
        $listParentesco = $tr->filtrar_parentesco_por_tipolibro($tipolibro);
        view::json($listParentesco);
    }

    public function buscar_imagen_mobile($tipolibro, $parentesco) {
        try {
            if ($tipolibro != null && $parentesco != null) {
                $servicio = "http://localhost:8000/RCWebService.asmx/nacimiento_propia?wsdl"; //url del servicio
                $parametros['dni'] = Auth::get("dni");
                $parametros['tipo'] = $tipolibro; //es lo mismo con comillas simples que dobles
                $parametros['parentesco'] = $parentesco; //es lo mismo con comillas simples que dobles
                $client = new SoapClient($servicio);
                $result = $client->nacimiento_propia($parametros); //llamamos al métdo que nos interesa con los parámetros 
                $datos = $result->nacimiento_propiaResult->Objetos;
                if (!isset($datos->ubicacion)) {
                    throw new NegocioExcepcion("No existen datos");
                }
                $ubicacion = str_replace("-", "/", $datos->ubicacion);
                $ubicacion = str_replace("Q:-ActasEscaneadas", "", $ubicacion);
                $ext = "png";
                $tmp = str_replace("TIF", "", $datos->nombre);
                $ruta_temporal_crop_original = Config::get("config.application.carpeta_temporal_original") . "crop/" . $tmp . $ext;
                $ruta = ExpertoImagen::obtener_ruta_completa($ubicacion . "/$datos->nombre");
                if (!file_exists($ruta)) {
                    throw new NegocioExcepcion("No existe el acta");
                }
                $dto = ExpertoImagen::convertir_imagen_mobile($ruta, ESTAMPA_CONSULTA);
                $ret[] = $dto;
                View::json($ret);
            } else {
                throw new NegocioExcepcion("no se han pasado los parametros");
            }
        } catch (NegocioExcepcion $ex) {
            view::select(null, null);
        }
    }

    public function getCurrentId() {
        view::select(NULL, NULL);
        if (Auth::get('id') != NULL) {
            view::json(Auth::get('id'));
        } else {
            view::json(FALSE);
        }
    }

}
