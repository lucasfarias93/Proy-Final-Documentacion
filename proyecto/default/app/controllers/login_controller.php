<?php

Load::models('usuarios');
Load::models('solicitudacta');
Load::models('solicitudestado');

/**
 * Carga del modelo Menus...
 */
class LoginController extends AppController {

    protected function before_filter() {
        if (input::isAjax()) {
            view::select(NULL, NULL);
        }
    }

    /**
     * Obtiene una lista para paginar los menus
     */
    public function index() {
        view::select(NULL, 'logueo');
        //si viene un usuario logueado lo desologueo
        MyAuth::cerrar_sesion();
    }

    /**
     * Crea un Registro
     */
}
