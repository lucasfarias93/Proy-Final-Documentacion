<?php

Load::models('solicitudacta');
Load::models('solicitudestado');
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

    public function cancelar($id) {
        try {
            var_dump($id);
            $se = new Solicitudestado();
            $se->idsolicitudacta = $id;
            $se->idestadosolicitud = 5;
            $se->fechacambioestado = NULL;
            Logger::error($se);
            $se->create();
            return Redirect::to();
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

}
