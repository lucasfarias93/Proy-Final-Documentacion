<?php

class IndexController extends AppController {

    public function index() {
        view::template('menu');
        view::select(NULL);
    }

}
