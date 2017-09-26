<?php

Load::models('parentesco');
Load::negocio('nacimiento_propia');

class IndexController extends AdminController {

    protected function before_filter() {
        view::select(NULL, 'solicitar');
    }

    public function index() {
//        $servicio = 'http://localhost:8000/RCWebService.asmx?wsdl'; //url del servicio
//        //$parametros = new Nacimiento_propia("29222236");
//        //$dni = new Dni('29222236');
//        $client = new SoapClient($servicio);
//        $params = array();
//        $params ["dni"] = "29222236";
//        $result = $client->__soapCall('nacimiento_propia', array($params)); //llamamos al métdo que nos interesa con los parámetros 
//        //$result = $client->nacimiento_propia($parametros); //llamamos al métdo que nos interesa con los parámetros 
//        Logger::info("Hola2");
//        var_dump($result);
    }

    public function buscar_parentesco_tipolibro() {

        if (Input::hasPost('tipolibro')) {
            $tr = new Parentesco();
            $listParentesco = $tr->filtrar_parentesco_por_tipolibro(input::post('tipolibro'));
            view::json($listParentesco);
        }
    }
}
