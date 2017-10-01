<?php

class ListadoController extends AdminController {

    public function index() {
        view::template('listado');
        view::select(NULL);
    }

}
