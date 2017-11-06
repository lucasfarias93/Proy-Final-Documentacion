<?php

Load::models('usuarios');
Load::models('solicitudacta');
Load::models('solicitudestado');

/**
 * Carga del modelo Menus...
 */
class VerificarentidadController extends AppController {

    protected function before_filter() {
        if (input::isAjax()) {
            view::select(NULL, NULL);
        }
    }

    /**
     * Obtiene una lista para paginar los menus
     */
    public function index() {
        view::select(NULL, 'verificarentidad');
        MyAuth::cerrar_sesion();
    }

    public function verificar_validez_entidad() {
        if (Input::hasPost('codigo') && Input::hasPost('dni')) {
            $codigo = Input::post('codigo');
            $dni = Input::post('dni');
            $id = new Usuarios();
            $id = $id->filtrar_por_dni($dni);
            $sola = new Solicitudacta();
            Logger::error($id->id);
            $solacta = $sola->buscar_solicitud_acta_por_codigo_pago($id->id, $codigo, 1);
            $fecha = $solacta->fechacambioestado;
            $fechaactual = UtilApp::fecha_actual();
            $diasrestantes = 180 - UtilApp::calcular_dias_entre_fechas($fecha, $fechaactual);
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
