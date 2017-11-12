<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Load::models('usuarios');
Load::models('linkrecuperacion');

class IndexController extends AppController {

    public function index($valor) {
        view::template(NULL);
        //$id = substr($valor, 0, 1);
        $codigo = substr($valor,-4);
        $link = new Linkrecuperacion();
        $l = $link->filtrar_por_codigo($codigo);
        $dias = floatval(UtilApp::calcular_dias_entre_fechas($l->fechadeemision, UtilApp::fecha_actual()));
        var_dump($codigo);
        var_dump($l->enlaceactivo);
        if ($dias <= 2 && $l->enlaceactivo == 'si') {
            if (Input::hasPost('usuarios')) {
                $usr = new Usuarios(Input::post('usuarios'));
                $userbd = new Usuarios();
                $userbd->filtrar_por_id($l->idusuarios);
                if ($userbd && $l->idusuarios == $userbd->id) {
                    if ($usr->clave === $usr->repetida) {
                        $userbd->clave = MyAuth::hash($usr->clave);
                        $userbd->clave_blanqueada = true;
                        $userbd->activo = '1';
                        $userbd->update();
                        $l->enlaceactivo = 'no';
                        $l->update();
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
                $userbd->filtrar_por_id($l->idusuarios);
                $this->usuarios = $userbd;
                $userbd->clave = "";
            }

            $userbd = new Usuarios();
            $userbd->filtrar_por_id($l->idusuarios);
            $this->usuarios = $userbd;
            $userbd->clave = "";
        } else {
            input::delete();
            Flash::error("El link esta caducado.");
            Router::redirect('login');
        }
    }

    public function cambiar_clave_mobile($email, $clave) {
        view::select(NULL, NULL);
        $usrbd = new Usuarios();
        $usrbd->filtrar_por_email($email);
        if (!$usrbd) {
            throw new NegocioExcepcion("El mail ingresado no existe");
        }
        if ($usrbd->id) {
            // si encuentro el ID en la base de datos:
            $usrbd->clave = MyAuth::hash($clave);
            $usrbd->clave_blanqueada = true;
            $usrbd->activo = '1';
            $usrbd->update();
            view::json(TRUE);
        } else {
            view::json(FALSE);
        }
    }

}
