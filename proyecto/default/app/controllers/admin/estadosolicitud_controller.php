<?php

/**
 * Carga del modelo Menus...
 */
Load::models('estadosolicitud');

class EstadosolicitudController extends AdminController {

    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index($page = 1) {
        $r = new Estadosolicitud();
        if (Input::hasPost("nombreestadosolicitud")) {
            $this->listEstadosolicitud = $r->filtrar_por_nombre(Input::post("nombreestadosolicitud"), $page);
        } else {
            $this->listEstadosolicitud = $r->paginar($page);
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
        if (Input::hasPost('estadosolicitud')) {
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $r = new Estadosolicitud(Input::post('estadosolicitud'));
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
        $es = new Estadosolicitud();
        //se verifica si se ha enviado el formulario (submit)
        if (Input::hasPost('estadosolicitud')) {
            $tr2 = Input::post('estadosolicitud');
            $fechahasta = DateTime::createFromFormat("d/m/Y", $tr2["fechahasta"]);
            $fechadesde = DateTime::createFromFormat("d/m/Y", $tr2["fechadesde"]);
            if ($fechahasta && $fechadesde->format("d/m/Y") == $tr2["fechadesde"] && $fechadesde <= $fechahasta) {
                if ($es->update(Input::post('estadosolicitud'))) {
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
            $this->estadosolicitud = $es->find_by_id((int) $id);
            $this->estadosolicitud->fechahasta = UtilApp::formatea_fecha_bd_to_pantalla($this->estadosolicitud->fechahasta);
            $this->estadosolicitud->fechadesde = UtilApp::formatea_fecha_bd_to_pantalla($this->estadosolicitud->fechadesde);
        }
    }

    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id) {
        $es = new Estadosolicitud();
        if ($es->delete((int) $id)) {
            Flash::valid('Operación exitosa');
        } else {
            Flash::error('Falló Operación');
        }

        //enrutando por defecto al index del controller
        return Redirect::to();
    }

}
