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
//        view::template('rectificardatos');
        view::select(NULL,NULL);
        try {
            var_dump($id);
            $sa = new Solicitudacta();
            $sa->find_first($id);
            $se = new Solicitudestado();
            $se->idsolicitudacta = $id;
            $se->idestadosolicitud = 5;
            $se->fechacambioestado = UtilApp::fecha_actual();
            Logger::error($se);
            $se->create();
            $sa->ultimosolicitudestado = $se->id;
            $sa->update();
            return Redirect::to();
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

}
