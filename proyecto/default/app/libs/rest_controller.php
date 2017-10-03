<?php

/**
 * Controlador para manejar peticiones REST
 * 
 * Por defecto cada acción se llama como el método usado por el cliente
 * (GET, POST, PUT, DELETE, OPTIONS, HEADERS, PURGE...)
 * ademas se puede añadir mas acciones colocando delante el nombre del método
 * seguido del nombre de la acción put_cancel, post_reset...
 *
 * @category Kumbia
 * @package Controller
 * @author kumbiaPHP Team
 */
require_once CORE_PATH . 'kumbia/kumbia_rest.php';

class RestController extends KumbiaRest {

    /**
     * Inicialización de la petición
     * ****************************************
     * Aqui debe ir la autenticación de la API
     * ****************************************
     */
    protected $users = array('diego' => '00000');

    final protected function initialize() {
        
//        $user = isset($_SERVER['PHP_AUTH_USER']) ? filter_var($_SERVER['PHP_AUTH_USER']) : null;
//        $pass = isset($_SERVER['PHP_AUTH_PW']) ? filter_var($_SERVER['PHP_AUTH_PW']) : null;
//        if (isset($this->users[$user]) && ($this->users[$user] == $pass)) {
//            return true;
//        } else {
//            $this->data = $this->error("Fail authentication", 401);
//            header('WWW-Authenticate: Basic realm="Private Area"');
//            return false;
//        }
    }

    final protected function finalize() {
        
    }

}
