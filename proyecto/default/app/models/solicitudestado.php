<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Solicitudestado extends ActiveRecord {

    /**

     * Retorna los menu para ser paginados

     *

     */
    public function getSolicitudestado($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

    public function paginar($pagina = 1) {
        return $this->paginate("page: $pagina");
    }

    public function filtrar_por_id($id) {
        $cols = "solicitudestado.fechacambioestado";
        $where = " id = '$id'";
        return $this->find($where, "columns: $cols");
    }

    public function buscar_solicitud_acta_por_codigo_pago($id, $codigop, $page, $ppage = 20) {
        $cols = " solicitudestado.fechacambioestado";
        $where = " codigodepago = '$codigop' AND idusuario = '$id' ";
        $join = " join cupondepago c on c.id = solicitudacta.idcupondepago ";
        $join .= " join solicitudestado se on se.id = solicitudacta.ultimosolicitudestado";

        return $this->find($where, "columns: $cols", "join: $join");
    }

}
