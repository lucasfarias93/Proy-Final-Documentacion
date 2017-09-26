<?php

/**
 * Carga del modelo Menus...
 */
Load::models('tramitedni');

class TramitedniController extends AppController {

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
    
    public function buscar_ciudadano_por_id_dni() {
        $r = new Tramitedni();
        if (Input::hasPost("idtramite") && Input::hasPost("dni")) {
            $persona = $r->filtrar_por_id_dni(Input::post("idtramite"),Input::post("dni"));
            view::json($persona);
        }
    }
    
    public function buscar_ciudadano_por_documento_mobile($dni) {
        $r = new Tramitedni();
        view::select(null, null);
        $persona = $r->filtrar_por_ultimo_tramite(($dni));
        view::json($persona);
    }
    
    public function buscar_ciudadano_por_id_dni_mobile($idtramite, $dni) {
        $r = new Tramitedni();
        view::select(null, null);
        $persona = $r->filtrar_por_id_dni($idtramite, $dni);
        view::json($persona);
    }
}
