<?php
class IndexController extends AppController {

    public function index() {
                view::template('registrar');
                view::select(NULL);
        //aca pongo lo que quiero mostrar al inicio
//        try {
//            echo PUBLIC_PATH;
//            $this->config = Configuracion::leer();
//            if (Input::hasPost('config')) {
//                foreach (Input::post('config') as $variable => $valor) {
//                    Configuracion::set($variable, $valor);
//                }
//                if (Configuracion::guardar()) {
//                    Flash::valid('La Configuración fue Actualizada Exitosamente...!!!');
//                    Acciones::add("Editó la Configuración de la aplicación", 'archivo config.ini');
//                    $this->config = Configuracion::leer();
//                } else {
//                    Flash::warning('No se Pudieron guardar los Datos...!!!');
//                }
//                $this->config = Configuracion::leer();
//            }
//        } catch (KumbiaException $e) {
//            View::excepcion($e);
//        }
    }

}
