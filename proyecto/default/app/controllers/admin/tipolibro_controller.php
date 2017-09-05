<?php

/**
 * Carga del modelo tiporeclamo.
 */
Load::models('tipolibro');

class TipolibroController extends AdminController {

    /**
     * Obtiene una lista para paginar los tipolibro
     */
    public function index($page = 1) {
        $tr = new Tipolibro();
        if (Input::hasPost("nombrelibro")) {
            $this->listTipolibro = $tr->filtrar_por_nombre(Input::post("nombrelibro"), $page);
        } else {
            $this->listTipolibro = $tr->paginar($page);
        }
    }

    /**
     * Crea un Registro
     */
    public function crear() {
        /**
         * Se verifica si el usuario envio el form (submit) y si ademas 
         * dentro del array POST existe uno llamado "tipolibro"
         * el cual aplica la autocarga de objeto para guardar los 
         * datos enviado por POST utilizando autocarga de objeto
         */
        if (Input::hasPost('tipolibro')) {
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $tl = new Tipolibro(Input::post('tipolibro'));
            //En caso que falle la operación de guardar
            if ($tl->create()) {
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
        $tl = new Tipolibro();

        //se verifica si se ha enviado el formulario (submit)
        if (Input::hasPost('tipolibro')) {
            $tr2 = Input::post('tipolibro');
            $fechahasta = DateTime::createFromFormat("d/m/Y", $tr2["fechahasta"]);
            $fechadesde = DateTime::createFromFormat("d/m/Y", $tr2["fechadesde"]);
            if ($fechahasta && $fechadesde->format("d/m/Y") == $tr2["fechadesde"] && $fechadesde <= $fechahasta) {
                if ($tl->update(Input::post('tipolibro'))) {
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
            $this->tipolibro = $tl->find_by_id((int) $id);
            $this->tipolibro->fechahasta = UtilApp::formatea_fecha_bd_to_pantalla($this->tipolibro->fechahasta);
            $this->tipolibro->fechadesde = UtilApp::formatea_fecha_bd_to_pantalla($this->tipolibro->fechadesde);
        }
    }

    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id) {
        $tr = new Tipolibro();
        if ($tr->delete((int) $id)) {
            Flash::valid('Operación exitosa');
        } else {
            Flash::error('Falló Operación');
        }

        //enrutando por defecto al index del controller
        return Redirect::to();
    }

}
