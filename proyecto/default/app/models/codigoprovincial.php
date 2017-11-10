<?php

class Codigoprovincial extends ActiveRecord {

    /**

     * Retorna los menu para ser paginados

     *

     */
    public function getCodigoprovincial($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

    public function paginar($pagina = 1) {
        return $this->paginate("page: $pagina");
    }

    /**
     * Devuelve los usuarios de la bd Paginados.
     * 
     * @param  integer $pagina numero de pagina a mostrar
     * @return array          resultado de la consulta
     */
    public function filtrar_por_numero_codigo($codigo, $pagina = 1) {
        $cols = "codigoprovincial.*";
        $where = " numerocodigoprovincial = '$codigo'";
        return $this->paginate($where, "columns: $cols", "", "page: $pagina");
    }

    public function obtener_codigos() {
        $cols = "codigoprovincial.*";
        $where = " 1 = 1";
        return $this->find($where, "columns: $cols");
    }

}
