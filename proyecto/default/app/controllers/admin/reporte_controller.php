<?php

/**
 * Carga del modelo Menus...
 */
Load::models('tiporeporte');
Load::negocio('experto_actas');

class ReporteController extends AdminController {

    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index($page = 1) {
        $tr = new Tiporeporte();
        $this->listTiporeporte = $tr->getTiporeporte($page);
        if (Input::hasPost('tiporeporte')) {
            $tr = Input::post('tiporeporte');
            $criterio = Input::post('criterio');
            session::set('criterio', $criterio);
            if (($tr['idtiporeporte']) == 1) {//Actas Firmadas
                $lista1 = Load::model('solicitudacta')->actas_firmadas($criterio);
                view::partial("actas_firmadas", FALSE, array("lista1" => $lista1));
            }
            if (($tr['idtiporeporte']) == 2) {//Solicitudes generadas
                $lista2 = Load::model('solicitudacta')->reporte_solicitudes_generadas($criterio);
                $lista8 = Load::model('solicitudacta')->actasxvencer($criterio);
                $lista9 = Load::model('solicitudacta')->actas_pendientes($criterio);
                $lista10 = Load::model('solicitudacta')->actas_firmadas($criterio);
                $lista11 = Load::model('solicitudacta')->actas_canceladas($criterio);
                $sol = "";
                $sol .= count($lista2) . ",";
                $sol .= count($lista8) . ",";
                $sol .= count($lista9) . ",";
                $sol .= count($lista10) . ",";
                $sol .= count($lista11) . ",";
                $sol = substr($sol, 0, -1);
                view::partial("solicitudes", FALSE, array("lista2" => $lista2, "sol" => $sol));
            }
            if (($tr['idtiporeporte']) == 3) {//Ganancias
                $lista3 = Load::model('cupondepago')->monto_pagado($criterio);
                $lista7 = Load::model('cupondepago')->grafico_ganancias($criterio);
                $datos = "";
                foreach ($lista7 as $data) {
                    $datos .= $data->total . ",";
                }
                $datos = substr($datos, 0, -1);
                view::partial("ganancias", FALSE, array("lista3" => $lista3, "datos" => $datos));
            }
            if (($tr['idtiporeporte']) == 4) {//Usuarios registrados
                $lista4 = Load::model('usuarios')->cantidad_usuarios($criterio);
                view::partial("usuarios_registrados", FALSE, array("lista4" => $lista4));
            }
            if (($tr['idtiporeporte']) == 5) {//Actas a vencer
                $lista5 = Load::model('solicitudacta')->actasxvencer($criterio);
                view::partial("actasxvencer", FALSE, array("lista5" => $lista5));
            }
            if (($tr['idtiporeporte']) == 6) {//Cantidad de reclamos
                $lista6 = Load::model('reclamoerroracta')->cantidad_reclamos($criterio);
                view::partial("reclamos", FALSE, array("lista6" => $lista6));
            }
        } else {
            session::delete('criterio');
        }
    }

    /**
     * Obtiene una lista para paginar las provincias
     */
    public function imprimir() {
        view::select(null, NULL);
//        if (session::has('criterio')) {
            $tr = Input::post('tiporeporte');
            $criterio = Input::post('criterio');
            if (($tr['idtiporeporte']) == 1) {//Actas Firmadas
                $lista1 = Load::model('solicitudacta')->actas_firmadas($criterio);
                
            }
            if (($tr['idtiporeporte']) == 2) {//Solicitudes generadas
                $lista2 = Load::model('solicitudacta')->reporte_solicitudes_generadas($criterio);
                $url = ExpertoActas::reporte_pdf_sol_gen($lista2);
                echo $url;
            }
            if (($tr['idtiporeporte']) == 3) {//Ganancias
                $lista3 = Load::model('cupondepago')->monto_pagado($criterio);
                $lista7 = Load::model('cupondepago')->grafico_ganancias($criterio);
                $datos = "";
                foreach ($lista7 as $data) {
                    $datos .= $data->total . ",";
                }
                $datos = substr($datos, 0, -1);
                view::partial("ganancias", FALSE, array("lista3" => $lista3, "datos" => $datos));
            }
            if (($tr['idtiporeporte']) == 4) {//Usuarios registrados
                $lista4 = Load::model('usuarios')->cantidad_usuarios($criterio);
                $url = ExpertoActas::reporte_pdf_usr_reg($lista4);
                echo $url;
            }
            if (($tr['idtiporeporte']) == 5) {//Actas a vencer
                $lista5 = Load::model('solicitudacta')->actasxvencer($criterio);
                $url = ExpertoActas::reporte_pdf_actasxvencer($lista5);
                echo $url;
            }
            if (($tr['idtiporeporte']) == 6) {//Cantidad de reclamos
                $lista6 = Load::model('reclamoerroracta')->cantidad_reclamos($criterio);
                $url = ExpertoActas::reporte_pdf_reclamos($lista6);
                echo $url;
            }
    }

}
