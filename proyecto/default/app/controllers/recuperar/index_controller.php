<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Load::models('usuarios');

class IndexController extends AppController {

    public function index($id) {
        view::template(NULL);
        if (Input::hasPost('usuarios')) {
            $usr = new Usuarios(Input::post('usuarios'));
            $userbd = new Usuarios();
            $userbd->filtrar_por_id($id);
            if ($userbd && $id == $userbd->id) {
                if ($usr->clave === $usr->repetida) {
                    $userbd->clave = MyAuth::hash($usr->clave);
                    $userbd->clave_blanqueada = true;
                    $userbd->update();
                    Flash::info("Se cambio exitosamente la clave del usuario: '{$userbd->login}'");
                    input::delete();
                    Router::redirect('login');
                } else {
                    Flash::warning("Las claves no coinciden");
                    input::delete();
                }
            } else {
                Flash::warning("No se ha podido cambiar la clave del usuario '{$userbd->login}'");
            }
        } else {
            $userbd = new Usuarios();
            $userbd->filtrar_por_id($id);
            $this->usuarios = $userbd;
            $userbd->clave = "";
        }

        $userbd = new Usuarios();
        $userbd->filtrar_por_id($id);
        $this->usuarios = $userbd;
        $userbd->clave = "";
    }

//    public function recuperar($id) {
//        view::template(NULL);
//
//        if (Input::hasPost('usuarios')) {
//            $usr = new Usuarios(Input::post('usuarios'));
//            $userbd = new Usuarios();
//            $userbd->filtrar_por_id($id);
//            if ($userbd && $id == $userbd->id) {
//                $userbd->clave = MyAuth::hash($usr->clave);
//                $userbd->clave_blanqueada = true;
//                $userbd->update();
//                Flash::info("Se cambio exitosamente la clave del usuario: '{$userbd->login}'");
//                input::delete();
//                Router::redirect('login');
//            } else {
//                Flash::warning("No se ha podido cambiar la clave del usuario '{$userbd->login}'");
//            }
//        } else {
//            $userbd = new Usuarios();
//            $userbd->filtrar_por_id($id);
//            $this->usuarios = $userbd;
//            $userbd->clave = "";
//        }
//        // return Router::redirect('login');
//    }

}
