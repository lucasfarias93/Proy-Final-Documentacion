<?php

class IndexController extends AppController {

    public function index() {
        view::template('solicitar');
        view::select(NULL);
    }

}
