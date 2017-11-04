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
        $dto = session::get("imagen");
        $arreglo['numeroacta'] = $dto->nroacta;
        $arreglo['numerolibro'] = $dto->nrolibro;
        $arreglo['nombrepropietarioacta'] = $dto->persona;
        $arreglo['apellidopropietarioacta'] = $dto->apellido;
        $this->tiporeclamo = $arreglo; //
    }

    public function crear_reclamo() {
        view::select(NULL);
        if (input::hasPost('tiporeclamo')) {
            $rea = new Reclamoerroracta(input::post('tiporeclamo'));
            $rea->idusuario = Auth::get('id');
            try {
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
            } catch (NegocioExcepcion $e) {
                Flash::info($e->getMessage());
                if (!Input::isAjax()) {
                    return Router::redirect();
                } else {
                    Flash::info("Debe elegir un tipo de reclamo para poder continuar");
                }
            } catch (NegocioExcepcion $e) {
                Flash::info("Debe elegir un tipo de reclamo para poder continuar");
                Router::redirect();
            } catch (Exception $e) {
                
            }
        }
    }

    public function crear_reclamo_mobile($apellido, $nombre, $nroacta, $observaciones, $idtiporeclamo, $idusuario, $nrolibro) {
        view::select(NULL, NULL);
        if ($idtiporeclamo != NULL) {
            $rea = new Reclamoerroracta();
            if ($apellido == 'null') {
                $rea->apellidopropietarioacta = 'Sin datos';
            } else {
                $rea->apellidopropietarioacta = $apellido;
            }
            if ($nombre == 'null') {
                $rea->nombrepropietarioacta = 'Sin datos';
            } else {
                $rea->nombrepropietarioacta = $nombre;
            }
            if ($nroacta == 'null') {
                $rea->numeroacta = floatval($nroacta = 0);
            } else {
                $rea->numeroacta = $nroacta;
            }
            if ($observaciones) {
                $rea->observaciones = 'Sin datos';
            } else {
                $rea->observaciones = $observaciones;
            }
            $rea->idtiporeclamo = $idtiporeclamo;
            $rea->idusuario = $idusuario;
            if ($nrolibro == 'null') {
                $rea->numerolibro = floatval($nrolibro = 0);
            } else {
                $rea->numerolibro = $nrolibro;
            }
            try {
                $rea->create();
                $reclamoerroractaestado = new Reclamoerroractaestado();
                $reclamoerroractaestado->fechacambioreclamoestado = UtilApp::fecha_actual();
///Con $rea->id le estoy seteando el id de lo que acabo de crear?
                $reclamoerroractaestado->idreclamoerroracta = $rea->id;
////////////El estado 1 es enviado al archivo
                $reclamoerroractaestado->idestadoreclamoerroracta = 1;
                $reclamoerroractaestado->create();
                Flash::info("Reclamo realizado con éxito");
                view::json(TRUE);
            } catch (NegocioExcepcion $e) {
                Flash::info($e->getMessage());
                view::json(FALSE);
            }
        } else {
            view::json("usado");
        }
    }

    public function buscar_tipo_reclamo_mobile() {
        view::select(null, null);
        $tr = new Tiporeclamo();
        $listTiporeclamo = $tr->find();
        view::json($listTiporeclamo);
    }

}
