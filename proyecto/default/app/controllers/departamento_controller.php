<?php
/**
 * Carga del modelo Menus...
 */
 Load::models('departamento');
class DepartamentoController extends AppController {
    
    //Obtener deptos segun provincia
    public function Depto_segun_provincia() {
        $r = new Departamento();
        $departamento = $r ->filtrar_por_dpto(Input::post("provincia"));
        view::json($departamento);
    }
 
}