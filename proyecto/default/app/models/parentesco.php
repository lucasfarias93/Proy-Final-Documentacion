<?php

class Parentesco extends ActiveRecord {

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
    public function filtrar_por_nombre($parentesco, $pagina = 1) {
        $cols = "parentesco.*";
        $where = " nombreparentesco ilike '%$parentesco%'";
        return $this->paginate($where, "columns: $cols", "", "page: $pagina");
    }

    public function filtrar_parentesco_por_tipolibro($tipolibro) {
        $cols = "parentesco.*";
        $where = " tp.id_tipolibro = '$tipolibro'";
        $join = " join tipolibro_parentesco tp on tp.id_parentesco = parentesco.id";
        return $this->find($where, "columns: $cols", "join: $join");
    }

    public function getParentesco($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
