<?php

class Localidad extends ActiveRecord {

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
    public function filtrar_por_nombre($localidad, $pagina = 1) {
        $cols = "localidad.*";
        $where = " nombrelocalidad ilike '%$localidad%'";
        return $this->paginate($where, "columns: $cols", "", "page: $pagina");
    }

    public function getLocalidad($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
