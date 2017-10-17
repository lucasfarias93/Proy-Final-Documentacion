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
        $cols = "solicitudacta.*, p.nombreparentesco, c.codigodepago, t.nombrelibro, se.fechacambioestado, es.nombreestadosolicitud";
        $where = " idusuario = '$id'";
        $join = " join parentesco p on p.id = solicitudacta.idparentesco";
        $join .= " join cupondepago c on c.id = solicitudacta.idcupondepago ";
        $join .= " join tipolibro t on t.id = solicitudacta.idtipolibro ";
        $join .= " join solicitudestado se on se.id = solicitudacta.ultimosolicitudestado";
        $join .= " join estadosolicitud es on es.id = se.idestadosolicitud";

        return $this->find($where, "columns: $cols", "join: $join");
    }

    public function buscar_solicitud_acta_por_codigo_pago($id, $codigop, $page, $ppage = 20) {
        $cols = " fechacambioestado ";
        $where = " codigodepago = '$codigop' AND idusuario = '$id' AND idestadosolicitud = '2'";
        $join = " join cupondepago c on c.id = solicitudacta.idcupondepago ";
        $join .= " join solicitudestado se on se.id = solicitudacta.ultimosolicitudestado";
        $join .= " join estadosolicitud es on es.id = se.idestadosolicitud";

        return $this->find_first($where, "columns: $cols", "join: $join");
    }

    public function getSolicitudacta($page, $ppage = 20) {

        return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');
    }

    public function reporte_solicitudes_generadas() {
        $cols = "solicitudacta.*, p.nombreparentesco, c.codigodepago, t.nombrelibro, se.fechacambioestado, es.nombreestadosolicitud";
        $where = " 1 = '1'";
        $join = " join parentesco p on p.id = solicitudacta.idparentesco";
        $join .= " join cupondepago c on c.id = solicitudacta.idcupondepago ";
        $join .= " join tipolibro t on t.id = solicitudacta.idtipolibro ";
        $join .= " join solicitudestado se on se.id = solicitudacta.ultimosolicitudestado";
        $join .= " join estadosolicitud es on es.id = se.idestadosolicitud";

        return $this->find($where, "columns: $cols", "join: $join");
    }

}
