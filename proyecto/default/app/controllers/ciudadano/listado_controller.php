<?php

Load::models('solicitudacta');
Load::models('parentesco');

class ListadoController extends AdminController {

    public function index($page = 1) {
        $id = Auth::get('id');
        view::template('listado');
        view::select(NULL);
        $sa = new Solicitudacta();
        $this->listSolicitudacta = $sa->buscar_solicitud_acta($id, $page);
    }

    public function listado_android($page = 1) {
        $id = Auth::get('id');
        view::select(null, null);
        $sa = new Solicitudacta();
        $this->listSolicitudacta = $sa->buscar_solicitud_acta($id, $page);
        view::json($this);
    }

}
