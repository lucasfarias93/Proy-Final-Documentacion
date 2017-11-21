<?php

class Cupondepago extends ActiveRecord {

    /**

     * Retorna los menu para ser paginados

     *

     */
    public $debug = true;
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
    public function monto_pagado($criterio) {
        $cols = "cupondepago.* ";
        $where = " estadocupondepago = 'Pagada'";
        if (array_key_exists("fechadesde", $criterio) && $criterio['fechadesde']) {
            $where .= " and  fechaemisionpago >= '" . $criterio['fechadesde'] . "'";
        }
        if (array_key_exists("fechahasta", $criterio) && $criterio['fechahasta']) {
            $where .= " and  fechaemisionpago <= '" . $criterio['fechahasta'] . "'";
        }
        if (array_key_exists("cupon", $criterio) && $criterio['cupon']) {
            $where .= " and codigodepago = '" . $criterio['cupon'] . "'";
        }
        return $this->find($where, "columns: $cols", 'order: fechaemisionpago desc');
    }

    public function grafico_ganancias($criterio) {
        $cols = "extract (month from fechaemisionpago) as mes, sum(montototal) as total ";
        $where = " estadocupondepago = 'Pagada' and fechaemisionpago >= '01/10/2017' and fechaemisionpago <= '30/11/2017'";
        $groupby = " 1";
        if (array_key_exists("fechadesde", $criterio) && $criterio['fechadesde']) {
            $where .= " and  fechaemisionpago >= '" . $criterio['fechadesde'] . "'";
        }
        if (array_key_exists("fechahasta", $criterio) && $criterio['fechahasta']) {
            $where .= " and  fechaemisionpago <= '" . $criterio['fechahasta'] . "'";
        }
        if (array_key_exists("cupon", $criterio) && $criterio['cupon']) {
            $where .= " and codigodepago = '" . $criterio['cupon'] . "'";
        }
        return $this->find($where, "columns: $cols", "group: $groupby", "order by 1 asc");
    }

}
