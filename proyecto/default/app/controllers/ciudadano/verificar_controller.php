<?php

Load::models('usuarios');
Load::models('solicitudacta');
Load::models('solicitudestado');

class VerificarController extends AdminController {

    protected function before_filter() {
        if (input::isAjax()) {
            view::select(NULL, NULL);
        }
    }

    public function index() {
        view::template('verificar');
        view::select(NULL);
    }

    public function verificar_validez_usuario() {
        if (Input::hasPost('codigo')) {
            $codigo = Input::post('codigo');
            $sola = new Solicitudacta();
            $solacta = $sola->buscar_solicitud_acta_por_codigo_pago(Auth::get("id"), $codigo, $page = 1);
            $fecha = $solacta->fechacambioestado;
            $fechaactual = UtilApp::fecha_actual();
            $diasrestantes = 300 - UtilApp::calcular_dias_entre_fechas($fecha, $fechaactual);
            if ($solacta != null) {
                if ($diasrestantes > 1) {
                    Flash::info("Acta valida quedan " . $diasrestantes . " dias");
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

    

    public function verificar_validez_usuario_mobile($id, $codigo) {
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
