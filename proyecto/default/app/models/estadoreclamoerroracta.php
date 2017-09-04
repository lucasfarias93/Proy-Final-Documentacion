<?php

class Estadoreclamoerroracta extends ActiveRecord {

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
    public function filtrar_por_nombre($estadoreclamoerroracta, $pagina = 1) {
        $cols = "estadoreclamoerroracta.*";
        $where = " nombreestadoreclamoerroracta ilike '%$estadoreclamoerroracta%'";
        return $this->paginate($where, "columns: $cols", "", "page: $pagina");
    }

    public function getEstadoreclamoerroracta($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
