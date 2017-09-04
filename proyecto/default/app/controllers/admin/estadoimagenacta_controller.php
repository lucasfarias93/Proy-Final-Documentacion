<?php

/**
 * Carga del modelo Menus...
 */
Load::models('estadoimagenacta');

class EstadoimagenactaController extends AdminController {

    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index($page = 1) {
        $es = new Estadoimagenacta();
        $this->listEstadoimagenacta = $es->getEstadoimagenacta($page);
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
        if (Input::hasPost('estadoimagenacta')) {
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $r = new Estadoimagenacta(Input::post('estadoimagenacta'));
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
        $es = new Estadoimagenacta();
        //se verifica si se ha enviado el formulario (submit)
        if (Input::hasPost('estadoimagenacta')) {
            $tr2 = Input::post('estadoimagenacta');
            $fechahasta = DateTime::createFromFormat("d/m/Y", $tr2["fechahasta"]);
            $fechadesde = DateTime::createFromFormat("d/m/Y", $tr2["fechadesde"]);
            if ($fechahasta && $fechadesde->format("d/m/Y") == $tr2["fechadesde"] && $fechadesde <= $fechahasta) {
                if ($es->update(Input::post('estadoimagenacta'))) {
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
            $this->estadoimagenacta = $es->find_by_id((int) $id);
            $this->estadoimagenacta->fechahasta = UtilApp::formatea_fecha_bd_to_pantalla($this->estadoimagenacta->fechahasta);
            $this->estadoimagenacta->fechadesde = UtilApp::formatea_fecha_bd_to_pantalla($this->estadoimagenacta->fechadesde);
        }
    }

    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id) {
        $es = new estadoimagenacta();
        if ($es->delete((int) $id)) {
            Flash::valid('Operación exitosa');
        } else {
            Flash::error('Falló Operación');
        }

        //enrutando por defecto al index del controller
        return Redirect::to();
    }

}
