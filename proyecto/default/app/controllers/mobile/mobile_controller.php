<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MobileController extends AppController {

    public function login($user, $pass) {
        view::select(null, null);
        if (MyAuth::autenticar($user, $pass, TRUE)) {
            if (Auth::is_valid()) {
                view::json(TRUE);
                $this->redirect("ciudadano");
            } else {
                view::json(FALSE);
            }
        } else {
            view::json(FALSE);
        }
    }

    public function verificar_validez_usuario_mobile($user, $pass, $id, $codigo) {
        if (MyAuth::autenticar($user, $pass, TRUE)) {
            view::select(NULL, NULL);
            if ($id != NULL) {
                $sola = new Solicitudacta();
                $solacta = $sola->buscar_solicitud_acta_por_codigo_pago($id, $codigo, $page = 1);
                $fecha = $solacta->fechacambioestado;
                $fechaactual = UtilApp::fecha_actual();
                $diasrestantes = 300 - UtilApp::calcular_dias_entre_fechas($fecha, $fechaactual);
                if ($solacta != null) {
                    if ($diasrestantes > 1) {
// Flash::info("Acta valida quedan " . $diasrestantes . " dias");
                        view::json("Acta valida quedan " . $diasrestantes . " dias");
                    } else {
                        Flash::error("Acta vencida");
                    }
                } else {
                    Logger::info($solacta);
                    Flash::error("No existen actas con los datos ingresados");
                }
            } else {
                Flash::error("debe ingresar un codigo de pago");
            }
        }
    }

}
