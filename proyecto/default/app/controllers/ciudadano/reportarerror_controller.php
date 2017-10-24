<?php

Load::models('tiporeclamo');
Load::models('reclamoerroracta');
Load::models('reclamoerroractaestado');
Load::models('solicitudacta');
Load::models('solicitudestado');
load::negocio('experto_actas');

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
            $rea->idusuario = Auth::get('id');
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
