<?php

Load::models('solicitudacta');
Load::models('solicitudestado');

class SolicitudController extends AdminController {

    public function index() {
        view::template('solicitar');
        view::select(NULL);
    }

    public function crear_solicitud() {
        view::select(NULL);
        try {
            $sa = new Solicitudacta();
            $sa->nombrepropietarioacta = 'prueba';
            Logger::info($sa->buscar_ultimo_nro_solicitud());
            $sa->numerosolicitud = 48;//1+$sa->buscar_ultimo_nro_solicitud();
            $sa->idusuario = Auth::get('id');
            $sa->idimagenacta = 3;
            $sa->idcupondepago = 4;
            $sa->idparentesco = 1;//isset($parentesco);
            $sa->idtipolibro = 1;//isset($tipo);
            $sa->ultimosolicitudestado = 4; //confirmada
            $sa->create();
            $se = new Solicitudestado();
            $se->idsolicitudacta =    48; //$sa->id;///le asigno el id del acta a la solicitud estado
            $se->idestadosolicitud = 4; //confirmada
            $se->fechacambioestado = UtilApp::fecha_actual();
            $se->create();
        } catch (NegocioExcepcion $e) {
            View::excepcion($e);
            Logger::info("paso por aca");
        }
    }

}
