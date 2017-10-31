<?php

class Reclamoerroracta extends ActiveRecord {

    /**

     * Retorna los menu para ser paginados

     *

     */

    public function cantidad_reclamos() {
        $cols = "reclamoerroracta.*, t.nombretiporeclamo, u.login, es.nombreestadoreclamoerroracta ";
        $where = " 1 = '1'";
        $join = " join tiporeclamo t on t.id = reclamoerroracta.idtiporeclamo ";
        $join .= " join usuarios u on u.id = reclamoerroracta.idusuario ";
        $join .= "  join reclamoerroractaestado se on se.id = reclamoerroracta.id ";
        $join .= " join estadoreclamoerroracta es on es.id = se.idestadoreclamoerroracta ";

        return $this->find($where, "columns: $cols", "join: $join");
    }

    public function getReclamoerroractaestado($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
