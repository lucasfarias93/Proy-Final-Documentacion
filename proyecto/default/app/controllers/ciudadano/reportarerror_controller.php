<?php

Load::models('tiporeclamo');

class ReportarerrorController extends AppController {

    protected function before_filter() {
        if (input::isAjax()) {
            view::select(NULL, NULL);
        }
    }

    public function index() {
        view::template('reportarerror');
        view::select(NULL);
        $tr = new Tiporeclamo();
        $this->listTiporeclamo = $tr->find();
    }

    public function reclamar($id) {
        if($id == 8){
        view::template('rectificardatos');
        view::select(NULL);
        }
        if($id == 7){
        view::template('enlazar');
        view::select(NULL);
        }
        if($id == 6){
        view::template('digitalizar');
        view::select(NULL);
        }
//        if ($id) {
//            var_dump($id);
//        }
    }

}
