<?php

class Solicitudacta extends ActiveRecord {

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
    public function buscar_solicitud_acta($id, $page, $ppage = 20) {
        $cols = "solicitudacta.*";
        $where = " idusuario = '$id'";
        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

    public function getSolicitudacta($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
