<?php

class FabricaEstampa {

    public static function get_experto($estampa) {
        if ($estampa === ESTAMPA_CONSULTA) {
            Load::negocio("imagen/experto_estampa_consulta");
            return new ExpertoEstampaConsulta();
        } else if ($estampa === ESTAMPA_CADUCADA) {
            Load::negocio("imagen/experto_estampa_caducada");
            return new ExpertoEstampaCaducada();
        } else if ($estampa === ESTAMPA_JUSTICIA) {
            Load::negocio("imagen/experto_estampa_justicia");
            return new ExpertoEstampaJusticia();
        } else if ($estampa === ESTAMPA_OFICIAL) {
            Load::negocio("imagen/experto_estampa_oficial");
            return new ExpertoEstampaOficial();
        }
    }

}

?>
