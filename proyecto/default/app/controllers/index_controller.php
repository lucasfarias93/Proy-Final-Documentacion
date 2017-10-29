<?php

/**
 * Controller por defecto si no se usa el routes
 *
 */
class IndexController extends AppController
{

    public function index()
    {
        MyAuth::cerrar_sesion();
        view::template('principal');
        view::select(NULL);
    }
}
