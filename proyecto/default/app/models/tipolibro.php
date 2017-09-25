<?php

class Tipolibro extends ActiveRecord {

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
    public function filtrar_por_nombre($tipolibro, $pagina = 1) {
        $cols = "tipolibro.*";
        $where = " nombrelibro ilike '%$tipolibro%'";
        return $this->paginate($where, "columns: $cols", "", "page: $pagina");
    }

    public function getTipolibro($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

    public function getParentescos($page, $ppage = 20) {
        $cols = "tipolibro_parentesco.*, p.nombreparentesco";
        $join = "join parentesco p on p.id= tipolibro_parentesco.id_parentesco";
        $where = "1=1";
        return $this->paginate($where, "join: $join", "columns: $cols", "page: $page", "per_page: $ppage", 'order: id desc');
    }

}
