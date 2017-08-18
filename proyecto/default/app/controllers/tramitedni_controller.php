<?php

/**
 * Carga del modelo Menus...
 */
Load::models('tramitedni');

class TramitedniController extends AdminController {

    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index() {
        $r = new Tramitedni();
        if (Input::hasPost("dni")) {
                $this->listTramitedni = $r->filtrar_por_dni(Input::post("dni"));
            }
    }
    
        public function buscar_ciudadano_por_documento() {
        $r = new Tramitedni();
        if (Input::hasPost("dni")) {
                $persona = $r->filtrar_por_ultimo_tramite(Input::post("dni"));
                view::json($persona);
            }
    }

}
