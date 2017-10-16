<?php

Load::models('solicitudacta');
Load::models('solicitudestado');

class SolicitudController extends AdminController {

    public function index() {
        view::template('solicitar');
        view::select(NULL);
    }

    public function crear_solicitud() {
        try {
            $sa = new Solicitudacta();
            $sa->nombrepropietarioacta = 'prueba';
            $sa->numerosolicitud = 9;//1+$sa->buscar_ultimo_nro_solicitud();
            $sa->idusuario = Auth::get('id');
            $sa->idimagenacta = 3;
            $sa->idcupondepago = 1;
            $sa->idparentesco = 1;//session::get($parentesco);
            $sa->idtiposolicitudacta = 1;//session::get($tipo);
            $sa->ultimosolicitudestado = 4; //confirmada
            Logger::info(session::get("parentesco", $parentesco));
            $sa->create();
            $se = new Solicitudestado();
            $se->idsolicitudacta = 6; //$sa->id;///le asigno el id del acta a la solicitud estado
            $se->idestadosolicitud = 4; //confirmada
            $se->fechacambioestado = UtilApp::fecha_actual();
        } catch (NegocioExcepcion $e) {
            View::excepcion($e);
            Logger::info("paso por aca");
        }
    }

}
