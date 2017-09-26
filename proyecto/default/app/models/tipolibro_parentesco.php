<?php

class TipolibroParentesco extends ActiveRecord {

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
    public function getTipolibroParentesco($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

//    public function getParentescos($page, $ppage = 20) {
//        $cols = "tipolibro.*, p.nombreparentesco";
//        $where = " nombrelibro ilike '%$tipolibro%'";
//        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
//    }

}
