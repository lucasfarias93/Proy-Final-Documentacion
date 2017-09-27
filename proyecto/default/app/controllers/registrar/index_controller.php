<?php
class IndexController extends AppController {

    public function index() {
                view::template('registrar');
                view::select(NULL);
    }

}
