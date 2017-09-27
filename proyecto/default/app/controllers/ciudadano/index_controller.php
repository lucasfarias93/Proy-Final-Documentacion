<?php

Load::models('parentesco');

class IndexController extends AdminController {

    protected function before_filter() {
        view::select(NULL, 'menu');
    }

    public function index() {
        
        $servicio = "http://localhost:8000/RCWebService.asmx/nacimiento_propia?wsdl"; //url del servicio
        $parametros = array(); //parametros de la llamada
        $parametros['dni'] = "32020039"; //es lo mismo con comillas simples que dobles
        $client = new SoapClient($servicio);
        //$result= $client->__soapCall('nacimiento_propia', array($parametros));//, array('typemap' => array(
        $result = $client->nacimiento_propia($parametros); //llamamos al métdo que nos interesa con los parámetros 
        $datos = $result->nacimiento_propiaResult->Objetos;
    var_dump($datos->ubicacion);
        $json = file_get_contents($servicio);
        $array = json_decode($json, true);
        return($array);
    }

    public function buscar_parentesco_tipolibro() {

        if (Input::hasPost('tipolibro')) {
            $tr = new Parentesco();
            $listParentesco = $tr->filtrar_parentesco_por_tipolibro(input::post('tipolibro'));
            view::json($listParentesco);
        }
    }

    public function convertir_resultado_ws($obj) {
        $out = array();
        foreach ($obj as $key => $val) {
            switch (true) {
                case is_object($val):
                    $out[$key] = datos($val);
                    break;
                case is_array($val):
                    $out[$key] = datos($val);
                    break;
                default:
                    $out[$key] = $val;
            }
        }
        return $out;
    }

}
