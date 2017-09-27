<?php

class SolicitudController extends AdminController {

    public function index() {
        view::template('solicitar');
        view::select(NULL);
    }

}
