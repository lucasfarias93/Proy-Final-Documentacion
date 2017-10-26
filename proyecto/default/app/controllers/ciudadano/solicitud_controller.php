<?php

Load::models('solicitudacta');
Load::models('solicitudestado');

class SolicitudController extends AdminController {

    public function index() {
        view::template('solicitar');
        view::select(NULL);
    }

    public function crear_solicitud() {
        view::select(NULL, NULL);
        try {
            $datos = session::get("imagen");
            $sa = new Solicitudacta();
            $sa->nombrepropietarioacta = $datos->persona." ".$datos->apellido;
            $sa->idusuario = Auth::get('id');
            $sa->idimagenacta = 3;
            $sa->idcupondepago = 4;
            $sa->idparentesco = session::get("parentesco");
            $sa->idtipolibro = session::get("tipolibro");
            $sa->create();
            $se = new Solicitudestado();
            $se->idsolicitudacta = $sa->id;///le asigno el id del acta a la solicitud estado
            $se->idestadosolicitud = 4; //confirmada
            $se->fechacambioestado = UtilApp::fecha_actual();
            $se->create();
            $sa->ultimosolicitudestado = $se->id;
            $sa->update();
            session::set("solicitudid", $sa->id);
        } catch (NegocioExcepcion $e) {
            Logger::info("paso por aca");
        }
    }

}
