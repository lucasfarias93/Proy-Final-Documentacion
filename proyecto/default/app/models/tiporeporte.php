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
    
    public function reporte_actas_firmadas($page, $ppage = 20) {
        $cols = "solicitudacta.*, p.nombreparentesco, c.codigodepago, t.nombretiposolicitud, se.fechacambioestado, es.nombreestadosolicitud";
        $where = " ultimosolicitudestado = '6'";
        $join = " join parentesco p on p.id = solicitudacta.idparentesco";
        $join .= " join cupondepago c on c.id = solicitudacta.idcupondepago ";
        $join .= " join tiposolicitudacta t on t.id = solicitudacta.idtiposolicitudacta ";
        $join .= " join solicitudestado se on se.id = solicitudacta.ultimosolicitudestado";
        $join .= " join estadosolicitud es on es.id = se.idestadosolicitud";

        return $this->find($where, "columns: $cols", "join: $join");
    }
        

    public function reporte_solicitudes_generadas() {
        $cols = "solicitudacta.*";
        return $this->find("columns: $cols", "");
    }

    public function reporte_usuarios_generados() {
        //falta hacer el COUNT
        $cols = "usuarios.*";
        return $this->find("columns: $cols", "");
    }

    public function reporte_reclamos_generados() {
        //falta hacer el COUNT
        $cols = "reclamos.*";
        return $this->find("columns: $cols", "");
    }

    public function reporte_actas_por_vencer() {
        //es dificil hacer despues
        $cols = "reclamos.*";
        return $this->find("columns: $cols", "");
    }

    public function reporte_ganancias() {
        //para mas adelante
        $cols = "reclamos.*";
        return $this->find("columns: $cols", "");
    }

    public function getTiporeporte($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

}
