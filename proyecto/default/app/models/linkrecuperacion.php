<?php

class Linkrecuperacion extends ActiveRecord {

    /**

     * Retorna los menu para ser paginados

     *

     */
    public function getLinkrecuperacion($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

    public function filtrar_por_codigo($codigo, $pagina = 1) {
        $cols = "linkrecuperacion.*";
        $where = " enlacerecuperacion = '$codigo'";
        return $this->find_first($where, "columns: $cols", "", "page: $pagina");
    }

}
