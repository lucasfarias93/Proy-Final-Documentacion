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
    
    public function grafico_ganancias() {
        $cols = "extract (month from fechaemisionpago), sum(montototal) as total ";
        $where = " estadocupondepago = 'Pagada' and fechaemisionpago >= '01/10/2017' and fechaemisionpago <= '30/11/2017'";
        $groupby = " 1";
        return $this->find($where, "columns: $cols","group: $groupby","order by 1 asc" );
    }

}
