<?php

Load::models('tiporeclamo');
Load::models('reclamoerroracta');
Load::models('reclamoerroractaestado');
Load::models('solicitudacta');
Load::models('solicitudestado');

class ReportarerrorController extends AppController {

    protected function before_filter() {
        if (input::isAjax()) {
            view::select(NULL, NULL);
        }
    }

    public function index() {

        view::template('reportarerror');
        view::select(NULL);
        $tr = new Tiporeclamo();
        $this->listTiporeclamo = $tr->find();
    }

    public function crear_reclamo() {
        view::select(NULL);
        if (input::hasPost('tiporeclamo')) {
        $rea = new Reclamoerroracta(input::post('tiporeclamo'));
//            $tr = input::post('tiporeclamo');
//            $rea->idtiporeclamo = $tr['anioacta'];
//            if (input::hasPost('anioacta')) {
//                $rea->anioacta = Input::post('anioacta');
//            } else {
//                $rea->anioacta = 0;
//            }
//            if (input::hasPost('nroacta')) {
//                $rea->numeroacta = Input::post('nroacta');
//            } else {
//                $rea->numeroacta = 0;
//            }
//            if (input::hasPost('nrolibro')) {
//                $rea->numerolibro = Input::post('nrolibro');
//            } else {
//                $rea->numerolibro = 0;
//            }
//            if (input::hasPost('nombre')) {
//                $rea->nombrepropietarioacta = Input::post('nombre');
//            } else {
//                $rea->nombrepropietarioacta = "";
//            }
//            if (input::hasPost('apellido')) {
//                $rea->apellidopropietarioacta = Input::post('apellido');
//            } else {
//                $rea->apellidopropietarioacta = "";
//            }
//            if (input::hasPost('comentarios')) {
//                $rea->observaciones = Input::post('comentarios');
//            } else {
//                $rea->observaciones = "";
//            }
            ///Ver como me traigo el acta que acabo de crear en la solicitud
            if (session::has("solicitudid")) {
                $rea->idsolicitudacta = session::get("solicitudid");
            } else {
                $sa = new Solicitudacta();
                $sa->nombrepropietarioacta = 'prueba';
                $sa->idusuario = Auth::get('id');
                $sa->idimagenacta = 3;
                $sa->idcupondepago = 4;
                $sa->idparentesco = session::get("parentesco");
                $sa->idtipolibro = session::get("tipolibro");
                $sa->create();
                $se = new Solicitudestado();
                $se->idsolicitudacta = $sa->id; ///le asigno el id del acta a la solicitud estado
                $se->idestadosolicitud = 6; //Enviada al archivo
                $se->fechacambioestado = UtilApp::fecha_actual();
                $se->create();
                $sa->ultimosolicitudestado = $se->id;
                $sa->update();
                $rea->idsolicitudacta = $sa->id;
            }

            $rea->create();
            $reclamoerroractaestado = new Reclamoerroractaestado();
            $reclamoerroractaestado->fechacambioreclamoestado = UtilApp::fecha_actual();
            ///Con $rea->id le estoy seteando el id de lo que acabo de crear?
            $reclamoerroractaestado->idreclamoerroracta = $rea->id;
            ////////////El estado 1 es enviado al archivo
            $reclamoerroractaestado->idestadoreclamoerroracta = 1;
            $reclamoerroractaestado->create();
            Flash::info("Reclamo realizado con éxito");
            Router::redirect('ciudadano');
        } else {
            Flash::error("Debe elegir un tipo de reclamo para poder continuar");
        }
    }

}
