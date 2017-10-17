<?php

class Reclamoerroracta extends ActiveRecord {

    /**

     * Retorna los menu para ser paginados

     *

     */

//    $cols = "solicitudacta.*, p.nombreparentesco, c.codigodepago, t.nombrelibro, se.fechacambioestado, es.nombreestadosolicitud";
//        $where = " idusuario = '$id'";
//        $join = " join parentesco p on p.id = solicitudacta.idparentesco";
//        $join .= " join cupondepago c on c.id = solicitudacta.idcupondepago ";
//        $join .= " join tipolibro t on t.id = solicitudacta.idtipolibro ";
//        $join .= " join solicitudestado se on se.id = solicitudacta.ultimosolicitudestado";
//        $join .= " join estadosolicitud es on es.id = se.idestadosolicitud";
//
//        return $this->find($where, "columns: $cols", "join: $join");

    public function cantidad_reclamos() {
        $cols = "reclamoerroracta.*, t.nombretiporeclamo";
        $where = " 1 = '1'";
        $join = " join tiporeclamo t on t.id = reclamoerroracta.idtiporeclamo ";
        return $this->find($where, "columns: $cols","join: $join");
    }

    public function getReclamoerroractaestado($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
