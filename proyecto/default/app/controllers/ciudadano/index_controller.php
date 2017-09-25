<?php

Load::models('parentesco');

class IndexController extends AdminController {

    protected function before_filter() {
        view::select(NULL, 'solicitar');
    }

    public function index() {
        
    }

    public function buscar_parentesco_tipolibro() {

        if (Input::hasPost('tipolibro')) {
            $tr = new Parentesco();
            $listParentesco = $tr->filtrar_parentesco_por_tipolibro(input::post('tipolibro'));
            view::json($listParentesco);
        }
    }

}
