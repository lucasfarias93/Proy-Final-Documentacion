<?php

class Tramitedni extends ActiveRecord {

    /**

     * Retorna los menu para ser paginados

     *

     */
    public function filtrar_por_dni($dni) {
        $cols = "tramitedni.*";
        $where = " dni = '$dni'";
        return $this->find($where, "columns: $cols");
    }

    public function filtrar_por_id_dni($idtramite, $dni) {
        $cols = "tramitedni.*";
        $where = "idtramite = '$idtramite' AND dni = '$dni'";
        return $this->find_first($where, "columns: $cols");
    }

    public function filtrar_por_ultimo_tramite($dni) {
        $cols = "tramitedni.*";
        $where = " dni = '$dni'";
        return $this->find_first($where, "columns: $cols", "order: id desc");
    }

    public function getTramitedni($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
