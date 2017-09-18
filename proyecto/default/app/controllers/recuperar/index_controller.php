<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Load::models('usuarios');

class IndexController extends AppController {

    public function index() {
        view::template(NULL);
        if (Input::hasPost('usuarios')) {
            $usr = new Usuarios(Input::post('usuarios'));
            $userbd = new Usuarios();
            $userbd->filtrar_por_id($usr->id);
            if ($userbd && $usr->id == $userbd->id) {
                $userbd->clave = MyAuth::hash($usr->clave);
                $userbd->clave_blanqueada = true;
                $userbd->update();
                Flash::info("Se cambio exitosamente la clave del usuario: '{$userbd->login}'");
                input::delete();
                Router::redirect('login');
            } else {
                Flash::warning("No se ha podido cambiar la clave del usuario '{$userbd->login}'");
            }
        }
        // return Router::redirect('login');
    }

}
