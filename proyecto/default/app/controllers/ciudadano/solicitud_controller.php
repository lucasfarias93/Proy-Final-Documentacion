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
            $sa->nombrepropietarioacta = $datos->persona . " " . $datos->apellido;
            $sa->idusuario = Auth::get('id');
            $sa->idimagenacta = 3;
            $sa->idcupondepago = 4;
            $sa->idparentesco = session::get("parentesco");
            $sa->idtipolibro = session::get("tipolibro");
            $sa->notificado = "no";
            $sa->create();
            $se = new Solicitudestado();
            $se->idsolicitudacta = $sa->id; ///le asigno el id del acta a la solicitud estado
            $se->idestadosolicitud = 4; //confirmada
            $se->fechacambioestado = UtilApp::fecha_actual();
            $se->create();
            $sa->ultimosolicitudestado = $se->id;
            $sa->update();
            session::set("solicitudid", $sa->id);
        } catch (NegocioExcepcion $e) {
            Logger::info("no se pudo crear la solicitud: " . $e);
        }
    }

    public function crear_solicitud_android($idusuario, $parentesco, $tipolibro, $nombre, $apellido) {
        view::select(NULL, NULL);
        try {

            $sa = new Solicitudacta();
            $sa->nombrepropietarioacta = $nombre . " " . $apellido;
            $sa->idusuario = $idusuario;
            $sa->idimagenacta = 3;
            $sa->idcupondepago = 4;
            $sa->idparentesco = $parentesco;
            $sa->idtipolibro = $tipolibro;
            $sa->notificado = "no";
            $sa->create();
            $se = new Solicitudestado();
            $se->idsolicitudacta = $sa->id; ///le asigno el id del acta a la solicitud estado
            $se->idestadosolicitud = 4; //confirmada
            $se->fechacambioestado = UtilApp::fecha_actual();
            $se->create();
            $sa->ultimosolicitudestado = $se->id;
            $sa->update();
        } catch (NegocioExcepcion $e) {
            view::json("no se pudo crear la solicitud " . $e);
        }
        view::json($sa->id);
    }

}
