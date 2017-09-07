<?php

class IndexController extends AppController {

    public function index() {
        view::template('olvido');
        view::select(NULL);
    }

}
