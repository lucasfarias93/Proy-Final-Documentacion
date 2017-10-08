<?php

Load::models('usuarios');
Load::models('solicitudacta');
Load::models('solicitudestado');

class VerificarController extends AdminController {

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
    public function verificar_validez_entidad() {
        if (Input::hasPost('codigo') && Input::hasPost('dni')) {
            $codigo = Input::post('codigo');
            $dni = Input::post('dni');
            $id = new Usuarios();
            $id = $id->filtrar_por_dni($dni);
            $sola = new Solicitudacta();
            $solacta = $sola->buscar_solicitud_acta_por_codigo_pago($id->id, $codigo, $page = 1);
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

}
