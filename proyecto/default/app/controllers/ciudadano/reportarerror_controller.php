<?php

Load::models('tiporeclamo');
Load::models('reclamoerroracta');
Load::models('reclamoerroractaestado');

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
        $rea = new Reclamoerroracta();

        if (input::hasPost('idtiporeclamo')) {
            $rea->idtiporeclamo = Input::post('anioacta');
            if (input::hasPost('anioacta')) {
                $rea->anioacta = Input::post('anioacta');
            } else {
                $rea->anioacta = 0;
            }
            if (input::hasPost('nroacta')) {
                $rea->numeroacta = Input::post('nroacta');
            } else {
                $rea->numeroacta = 0;
            }
            if (input::hasPost('nrolibro')) {
                $rea->numerolibro = Input::post('nrolibro');
            } else {
                $rea->numerolibro = 0;
            }
            if (input::hasPost('nombre')) {
                $rea->nombrepropietarioacta = Input::post('nombre');
            } else {
                $rea->nombrepropietarioacta = "";
            }
            if (input::hasPost('apellido')) {
                $rea->apellidopropietarioacta = Input::post('apellido');
            } else {
                $rea->apellidopropietarioacta = "";
            }
            if (input::hasPost('comentarios')) {
                $rea->observaciones = Input::post('comentarios');
            } else {
                $rea->observaciones = "";
            }
            $rea->numeroreclamo = 1 + $rea->buscar_ultimo_reclamo();
            ///Ver como me traigo elacta que acabo de crear en la solicitud
            $rea->idsolicitudacta = 1;
            $rea->create();
            $reclamoerroractaestado = new Reclamoerroractaestado();
            $reclamoerroractaestado->fechacambioreclamoestado = UtilApp::fecha_actual();
            ///Con $rea->id le estoy seteando el id de lo que acabo de crear?
            $reclamoerroractaestado->idreclamoerroracta = $rea->id;
            ////////////El estado 1 es enviado al archivo
            $reclamoerroractaestado->idestadoreclamoerroracta = 1;
            $reclamoerroractaestado->create();
            Flash::info("Reclamo realizado con éxito");
        } else {
            Flash::error("Debe elegir un tipo de reclamo para continuar");
        }
    }

}
