<?php

class Oficinainscripcion extends ActiveRecord {

    /**

     * Retorna los menu para ser paginados

     *

     */
    public function paginar($pagina = 1) {
        return $this->paginate("page: $pagina");
    }

    /**
     * Devuelve los usuarios de la bd Paginados.
     * 
     * @param  integer $pagina numero de pagina a mostrar
     * @return array          resultado de la consulta
     */
    public function filtrar_por_nombre($oficina, $pagina = 1) {
        $cols = "oficina.*";
        $where = " nombreoficina ilike '%$oficina%'";
        return $this->paginate($where, "columns: $cols", "", "page: $pagina");
    }

    public function getOficinainscripcion($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
