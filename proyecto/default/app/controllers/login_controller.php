<?php

/**
 * Carga del modelo Menus...
 */
class LoginController extends RestController {

    /**
     * Obtiene una lista para paginar los menus
     */
    public function index() {

//       echo "HOLA";
//       Logger::info("Probando el servicio");
//       view::select(NULL, NULL);
        view::select(null, NULL);
        $ret = $this->_logueoValido("diego", "00000");
        var_dump($ret);

        if ($ret) {
            view::json(TRUE);
        } else {
            View::json(FALSE);
            var_dump($ret);
        }
    }

    public function get_login($user, $pass) {
        view::select(null, NULL);
        $ret = $this->_logueoValido("diego", "23000");
        var_dump($ret);

        if ($ret) {
            view::json(TRUE);
        } else {
            View::json(FALSE);
            var_dump($ret);
        }
    }

    /**
     * Crea un Registro
     */
}
