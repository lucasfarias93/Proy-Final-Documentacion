<?php

class VerificarController extends AdminController {

    public function index() {
        view::template('verificar');
        view::select(NULL);
    }

}
