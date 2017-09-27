<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NegocioExcepcion
 *
 * @author desarrollo
 */
class NegocioExcepcion extends Exception {

    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        header('HTTP/1.1 500 Internal Server Error');
        parent::__construct($message, $code, $previous);
    }

}

?>
