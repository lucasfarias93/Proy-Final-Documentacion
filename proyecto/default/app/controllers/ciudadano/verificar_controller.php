<?php

Load::models('usuarios');
Load::models('solicitudacta');
Load::models('solicitudestado');

class VerificarController extends AdminController {

    public function index() {
        view::template('verificar');
        view::select(NULL);
        $sola = new Solicitudacta();
        $sole = new Solicitudestado();
        $solacta = $sola->buscar_solicitud_acta_por_codigo_pago(Auth::get("id"),"1xd32", $page = 1);
        
        $fecha =$solacta->fechacambioestado;
        $fechaactual = UtilApp::fecha_actual();
        if($solacta != null){
           Flash::info("Acta valida quedan ");
                Flash::info(UtilApp::calcular_dias_entre_fechas($fecha, $fechaactual) + " dias");
            } else {
        Logger::info($solacta);
                Flash::error("Acta invalida");
            }
        }
    

    public function verificar_validez_usuario() {
        if (Input::hasPost('codigo')) {
            
            if ($sola->codigodepago == $codigo) {
                Flash::info("Acta Valida");
            } else {
                Flash::error("Acta invalida");
            }
        } else {
            Flash::error("debe ingresar un codigo de pago");
        }
    }

}
