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
            $id = (int) $id;
            $usuario = new Usuarios();
            if (!$usuario->find_first($id)) { //si no existe el usuario
                Flash::warning("No existe ningun usuario con id '{$id}'");
            } else {
                $usuario->clave = $clave;
                $usuario->clave_blanqueada = true;
                if (!$usuario->update()) {
                    Flash::warning("No se ha podido blanquear la contraseña del usuario '{$usuario->login}'");
                } else {
                    Flash::info("Se ha blanqueado exitosamente la contraseña del usuario: '{$usuario->login}' a '1234' ");
                }
            }
        //return Router::redirect('login');
    }

}
