<?php

class Reclamoerroracta extends ActiveRecord {

    /**

     * Retorna los menu para ser paginados

     *

     */
    public function cantidad_reclamos($criterio) {
        $cols = "reclamoerroracta.*, t.nombretiporeclamo, u.login, es.nombreestadoreclamoerroracta, se.fechacambioreclamoestado ";
        $where = " 1 = '1'";
        $join = " join tiporeclamo t on t.id = reclamoerroracta.idtiporeclamo ";
        $join .= " join usuarios u on u.id = reclamoerroracta.idusuario ";
        $join .= "  join reclamoerroractaestado se on se.id = reclamoerroracta.id ";
        $join .= " join estadoreclamoerroracta es on es.id = se.idestadoreclamoerroracta ";
        if (array_key_exists("nombrepropietarioacta", $criterio) && $criterio['nombrepropietarioacta']) {
            $where .= " and nombrepropietarioacta ilike '%" . UtilApp::normalizar_busqueda($criterio['nombrepropietarioacta']) . "%' or apellidopropietarioacta ilike '%" . UtilApp::normalizar_busqueda($criterio['apellidopropietarioacta']) . "%'";
        }
        if (array_key_exists("fechadesde", $criterio) && $criterio['fechadesde']) {
            $where .= " and  se.fechacambioreclamoestado >= '" . $criterio['fechadesde'] . "'";
        }
        if (array_key_exists("fechahasta", $criterio) && $criterio['fechahasta']) {
            $where .= " and  se.fechacambioreclamoestado <= '" . $criterio['fechahasta'] . "'";
        }
        if (array_key_exists("login", $criterio) && $criterio['login']) {
            $where .= " and login ilike '%" . UtilApp::normalizar_busqueda($criterio['login']) . "%'";
        }
        return $this->find($where, "columns: $cols", "join: $join");
    }

    public function getReclamoerroractaestado($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
