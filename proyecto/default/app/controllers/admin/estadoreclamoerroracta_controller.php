<?php

/**
 * Carga del modelo Menus...
 */
Load::models('estadoreclamoerroracta');

class EstadoreclamoerroractaController extends AdminController {

    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index($page = 1) {
        $r = new Estadoreclamoerroracta();
        if (Input::hasPost("nombreestadoreclamoerroracta")) {
            $this->listEstadoreclamoerroracta = $r->filtrar_por_nombre(Input::post("nombreestadoreclamoerroracta"), $page);
        } else {
            $this->listEstadoreclamoerroracta = $r->paginar($page);
        }
    }

    /**
     * Crea un Registro
     */
    public function crear() {
        /**
         * Se verifica si el usuario envio el form (submit) y si ademas 
         * dentro del array POST existe uno llamado "menus"
         * el cual aplica la autocarga de objeto para guardar los 
         * datos enviado por POST utilizando autocarga de objeto
         */
        if (Input::hasPost('estadoreclamoerroracta')) {
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $r = new Estadoreclamoerroracta(Input::post('estadoreclamoerroracta'));
            //En caso que falle la operación de guardar
            if ($r->create()) {
                Flash::valid('Operación exitosa');
                //Eliminamos el POST, si no queremos que se vean en el form
                Input::delete();
                return;
            } else {
                Flash::error('Falló Operación');
            }
        }
    }

    /**
     * Edita un Registro
     *
     * @param int $id (requerido)
     */
    public function edit($id) {
        $es = new Estadoreclamoerroracta();
        //se verifica si se ha enviado el formulario (submit)
        if (Input::hasPost('estadoreclamoerroracta')) {
            $tr2 = Input::post('estadoreclamoerroracta');
            $fechahasta = DateTime::createFromFormat("d/m/Y", $tr2["fechahasta"]);
            $fechadesde = DateTime::createFromFormat("d/m/Y", $tr2["fechadesde"]);
            if ($fechahasta && $fechadesde->format("d/m/Y") == $tr2["fechadesde"] && $fechadesde <= $fechahasta) {
                if ($es->update(Input::post('estadoreclamoerroracta'))) {
                    Flash::valid('Operación exitosa');
                    //enrutando por defecto al index del controller
                    return Redirect::to();
                } else {
                    Flash::error('Falló Operación');
                }
            } else {
                Flash::error('Falló fecha');
            }
        } else {
            //Aplicando la autocarga de objeto, para comenzar la edición
            $this->estadoreclamoerroracta = $es->find_by_id((int) $id);
            $this->estadoreclamoerroracta->fechahasta = UtilApp::formatea_fecha_bd_to_pantalla($this->estadoreclamoerroracta->fechahasta);
            $this->estadoreclamoerroracta->fechadesde = UtilApp::formatea_fecha_bd_to_pantalla($this->estadoreclamoerroracta->fechadesde);
        }
    }

    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id) {
        $es = new Estadoreclamoerroracta();
        if ($es->delete((int) $id)) {
            Flash::valid('Operación exitosa');
        } else {
            Flash::error('Falló Operación');
        }

        //enrutando por defecto al index del controller
        return Redirect::to();
    }

}
