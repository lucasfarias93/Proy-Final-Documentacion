<?php

/**
 * Carga del modelo Menus...
 */
Load::models('tiporeporte');

class ReporteController extends AdminController {

    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index($page = 1) {
        $tr = new Tiporeporte();
        $this->listTiporeporte = $tr->getTiporeporte($page);
        if (Input::hasPost('tiporeporte')) {
            $tr = Input::post('tiporeporte');
            if (($tr['idtiporeporte']) == 2) {//Solicitudes generadas
                $lista2 = Load::model('solicitudacta')->reporte_solicitudes_generadas();
                view::partial("solicitudes", FALSE, array("lista2" => $lista2));
            }
            if (($tr['idtiporeporte']) == 3) {//Ganancias
                $lista2 = Load::model('cupondepago')->monto_pagado();
                view::partial("ganancias", FALSE, array("lista3" => $lista3));
            }
            if (($tr['idtiporeporte']) == 4) {//Usuarios registrados
                $lista4 = Load::model('usuarios')->cantidad_usuarios();
                view::partial("usuarios_registrados", FALSE, array("lista4" => $lista4));
            }
        }
    }

}
