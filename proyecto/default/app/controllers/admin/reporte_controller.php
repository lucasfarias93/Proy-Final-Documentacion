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
    }
    
    public function actasFirmadas() {
        
    }
    
    public function reportePDF(){
        
    }
}
