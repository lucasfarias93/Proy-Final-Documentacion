<?php

class Reclamoerroracta extends ActiveRecord {

    /**

     * Retorna los menu para ser paginados

     *

     */
    public function buscar_ultimo_reclamo() {
        $cols = "reclamoerroracta.* MAX(numeroreclamo)";
        $where = " 1 = '1'";
        return $this->find($where, "columns: $cols");
    }
    public function cantidad_reclamos() {
        $cols = "reclamoerroracta.*";
        $where = " 1 = '1'";
        return $this->find($where, "columns: $cols");
    }

    public function getReclamoerroractaestado($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
