<?php

class Departamento extends ActiveRecord {

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
    public function filtrar_por_nombre($departamento, $pagina = 1) {
        $cols = "departamento.*";
        $where = " nombredepartamento ilike '%$departamento%'";
        return $this->paginate($where, "columns: $cols", "", "page: $pagina");
    }
    
    public function filtrar_por_dpto($provincia, $pagina = 1, $ppage = 20) {
        $cols = "departamento.*";
        $where = " departamento.idprovincia = $provincia";
        return $this->paginate($where, "columns: $cols", "per_page: $ppage", "page: $pagina");
    }
    
    public function getDepartamento($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
