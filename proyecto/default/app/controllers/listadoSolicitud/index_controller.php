<?php
class IndexController extends AppController {

    public function index() {
                view::template('listado');
                view::select(NULL);
    }

}
