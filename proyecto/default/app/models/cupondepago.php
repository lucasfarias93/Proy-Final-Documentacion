<?php

class Cupondepago extends ActiveRecord {

    /**

     * Retorna los menu para ser paginados

     *

     */
    public function getCupondepago($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

    public function paginar($pagina = 1) {
        return $this->paginate("page: $pagina");
    }

    /**
     * Devuelve los usuarios de la bd Paginados.
     * 
     * @param  integer $pagina numero de pagina a mostrar
     * @return array          resultado de la consulta
     */
    public function monto_pagado() {
        $cols = "cupondepago.* ";
        $where = " estadocupondepago = 'Pagada'";
        return $this->find($where, "columns: $cols", 'order: fechaemisionpago desc');
    }

}
