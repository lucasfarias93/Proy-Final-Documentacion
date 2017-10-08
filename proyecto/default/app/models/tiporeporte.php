<?php

class Tiporeporte extends ActiveRecord {

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
    public function filtrar_por_nombre($tiporeporte, $pagina = 1) {
        $cols = "tiporeporte.*";
        $where = " nombretiporeporte ilike '%$tiporeporte%'";
        return $this->paginate($where, "columns: $cols", "", "page: $pagina");
    }
    
    public function reporte_actas_firmadas($tiporeporte, $pagina = 1) {
        $cols = "tiporeporte.*";
        $where = " nombretiporeporte ilike '%$tiporeporte%'";
        return $this->paginate($where, "columns: $cols", "", "page: $pagina");
    }

    public function getTiporeporte($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
