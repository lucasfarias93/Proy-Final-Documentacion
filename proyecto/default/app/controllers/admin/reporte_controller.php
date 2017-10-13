<?php

/**
 * Carga del modelo Menus...
 */
Load::models('tiporeporte');

class ReporteController extends AdminController {

    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index($page = 1) {
        $tr = new Tiporeporte();
        $this->listTiporeporte = $tr->getTiporeporte($page);
        if (Input::hasPost('tiporeporte')) {
            $tr = Input::post('tiporeporte');
            var_dump($tr);
        if(($tr['idtiporeporte']) ==2){
                    $lista = Load::model('solicitudacta')->reporte_solicitudes_generadas();
                    view::partial("solicitudes", FALSE,array("lista"=>$lista));
        }
        }
    }

    public function actasFirmadas() {
        
    }

    public function reportePDF() {
        
    }

}
