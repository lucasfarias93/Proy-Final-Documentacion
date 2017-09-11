<?php
/**
 * Carga del modelo Menus...
 */
Load::models('provincia');
class ProvinciaController extends AppController {
    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index_mobile($page=1) 
    {
        view::select(null, null);
        $r = new Provincia();
        $this->listProvincia = $r->getProvincia($page);
        view::json($this);
    }
}