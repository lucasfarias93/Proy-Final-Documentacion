<?php

/**
 * Carga del modelo Menus...
 */
Load::models('localidad');

class LocalidadController extends AppController {

    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index($page = 1) {
        $r = new Localidad();
        if (Input::hasPost("nombrelocalidad")) {
            $this->listLocalidad = $r->filtrar_por_nombre(Input::post("nombrelocalidad"), $page);
        } else {
            $this->listLocalidad = $r->paginar($page);
        }
    }
    
    //Obtener deptos segun provincia
    public function localidad_segun_dpto() {
        $r = new Localidad();
        $localidad = $r ->filtrar_por_localidad(Input::post("departamento"));
        view::json($localidad);
    }
 
    //Obtener deptos segun provincia
    public function localidad_segun_dpto_mobile($departamento) {
        $r = new Localidad();
        view::select(null, null);
        $localidad = $r ->filtrar_por_localidad(($departamento));
        view::json($localidad);
    }

}
