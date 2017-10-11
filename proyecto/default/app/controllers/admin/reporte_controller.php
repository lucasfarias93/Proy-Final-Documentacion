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
            foreach ($tr as $value) {
                var_dump($value);
                if ($value == 2) {
                    var_dump("paso por el 2");
                    $this->listTiporeporte = $tr->reporte_solicitudes_generadas();
                    
                }
            }
        }
    }

    public function actasFirmadas() {
        
    }

    public function reportePDF() {
        
    }

}
