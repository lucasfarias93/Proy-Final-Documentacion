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
        if ($sa->buscar_solicitud_acta($id, $page) != NULL) {
            $this->listSolicitudacta = $sa->buscar_solicitud_acta($id, $page);
        } else {
            Flash::info("No hay solicitudes");
        }
    }

    public function pagar($idsa) {
        view::template('pagar');
        view::select(NULL);
        $sa = new Solicitudacta();
        session::set('solicitudid', $idsa);
    }

    public function listado_android() {
        $id = Auth::get('id');
        view::select(null, null);
        $sa = new Solicitudacta();
        $this->listSolicitudacta = $sa->buscar_solicitud_acta($id, $page);
        view::json($this);
    }

    public function cancelar($id) {
        view::select(NULL, NULL);
        try {
            $sa = new Solicitudacta();
            $sa->find_first($id);
            $se = new Solicitudestado();
            $se->idsolicitudacta = $id;
            $se->idestadosolicitud = 5;
            $se->fechacambioestado = UtilApp::fecha_actual();
            $se->create();
            $sa->ultimosolicitudestado = $se->id;
            $sa->update();
            return Redirect::to();
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function cancelar_mobile($id) {
        view::select(NULL, NULL);
        ////////Creo el estado cancelada
        try {
            $sa = new Solicitudacta();
            $sa->find_first($id);
            $se = new Solicitudestado();
            $se->idsolicitudacta = $id;
            $se->idestadosolicitud = 5;
            $se->fechacambioestado = UtilApp::fecha_actual();
            $se->create();
            $sa->ultimosolicitudestado = $se->id;
            $sa->update();
            view::json(TRUE);
        } catch (KumbiaException $e) {
            View::json(FALSE);
        }
    }

}
