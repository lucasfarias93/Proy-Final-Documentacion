<?php

class Estadoimagenacta extends ActiveRecord {

    /**

     * Retorna los menu para ser paginado

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
    public function filtrar_por_nombre($estadoimagenacta, $pagina = 1) {
        $cols = "estadoimagenacta.*";
        $where = " nombreestado ilike '%$estadoimagenacta%'";
        return $this->paginate($where, "columns: $cols", "", "page: $pagina");
    }

    public function getEstadoimagenacta($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
