<?php

Load::models('parentesco');

class IndexController extends AdminController {

    protected function before_filter() {
        view::select(NULL, 'solicitar');
    }

    public function index() {
       $servicio='http://10.160.1.229:8888/RCWebService.asmx/nacimiento_propia?'; //url del servicio
//$parametros=array(); //parametros de la llamada
//$parametros['dni']="29222236";
//$client = new SoapClient($servicio, $parametros);
//$result = $client->nacimiento_propia($parametros);//llamamos al métdo que nos interesa con los parámetros 
//var_dump($result);
    }

    public function buscar_parentesco_tipolibro() {

        if (Input::hasPost('tipolibro')) {
            $tr = new Parentesco();
            $listParentesco = $tr->filtrar_parentesco_por_tipolibro(input::post('tipolibro'));
            view::json($listParentesco);
        }
    }

}
