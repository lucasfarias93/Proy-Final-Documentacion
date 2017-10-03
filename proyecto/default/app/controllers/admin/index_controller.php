<?php

class IndexController extends AdminController {

    public function index() {
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
//        view::select(null, NULL);
//        $ret = $this->_logueoValido("diego", "23000");
//        var_dump($ret);
//
//        if ($ret) {
//            view::json(TRUE);
//        } else {
//            View::json(FALSE);
//            var_dump($ret);
//        }
    }

    public function loginmobile($login, $pass) {
        view::select(null, null);
        try {
            $ret = $this->_logueoValido($login, $pass);
            var_dump($ret);
            view::json(FALSE);
            if ($ret) {
                view::json(TRUE);
            } else {
                
            }
        } catch (Exception $e) {
            view::json(FALSE);
            Flash::error("No se pudo loguear el usuario");
            Logger::error($e->getMessage());
            Logger::error($e->getTraceAsString());
        }
    }

}
