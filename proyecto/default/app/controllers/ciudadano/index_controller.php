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
        if (Input::hasPost('tipolibro') && Input::hasPost('parentesco')) {
            $servicio = "http://localhost:8000/RCWebService.asmx/nacimiento_propia?wsdl"; //url del servicio
            $parametros['dni'] = Auth::get("dni");
            $tipo = Input::post('tipolibro');
            $parentesco = Input::post('parentesco');
//            $tipoConversion[2]=1;
//            $tipoConversion[3]=3;
            $parametros['tipo'] = $tipo; //es lo mismo con comillas simples que dobles
            $parametros['parentesco'] = $parentesco; //es lo mismo con comillas simples que dobles
            $client = new SoapClient($servicio);
            //$result= $client->__soapCall('nacimiento_propia', array($parametros));//, array('typemap' => array(
            $result = $client->nacimiento_propia($parametros); //llamamos al métdo que nos interesa con los parámetros 
            $datos = $result->nacimiento_propiaResult->Objetos;
//            var_dump("resultado ws");
//            var_dump($datos);
            $ubicacion = str_replace("-", "/", $datos->ubicacion);
            $ubicacion = str_replace("Q:-ActasEscaneadas", "", $ubicacion);
            $ext = "png";
            $tmp = str_replace("TIF", "", $datos->nombre);
            $ruta_temporal_crop_original = Config::get("config.application.carpeta_temporal_original") . "crop/" . $tmp . $ext;
//            var_dump($ruta_temporal_crop_original);
//            var_dump($ubicacion . "/$datos->nombre");
//            @unlink($ruta_temporal_crop_original);
//            var_dump("antes");
            $ruta = ExpertoImagen::obtener_ruta_completa($ubicacion . "/$datos->nombre");
//            var_dump($ruta);
            if (!file_exists($ruta)) {
//               throw new NegocioExcepcion("No existe el acta");
            }
            $dto = ExpertoImagen::convertir_imagen($ruta, ESTAMPA_CONSULTA);
            $ret[] = $dto;
            View::json($ret);
        } else {
//            throw new NegocioExcepcion("no se han pasado los parametros");
        }
    }

    public function buscar_parentesco_tipolibro() {

        if (Input::hasPost('tipolibro')) {
            $tr = new Parentesco();
            $listParentesco = $tr->filtrar_parentesco_por_tipolibro(input::post('tipolibro'));
            view::json($listParentesco);
        }
    }

}
