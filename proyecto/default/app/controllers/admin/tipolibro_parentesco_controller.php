<?php

/**
 * Carga del modelo tiporeclamo.
 */
Load::models('tipolibro_parentesco');

class Tipolibro_parentescoController extends AdminController {

    /**
     * Obtiene una lista para paginar los tipolibro
     */
    public function index($page = 1) {
        
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
        if (Input::hasPost('tipolibro_parentesco')) {
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $tl = new Tipolibro_parentesco(Input::post('tipolibro_parentesco'));
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
        $tl = new Tipolibro_parentesco();

        //se verifica si se ha enviado el formulario (submit)
        if (Input::hasPost('tipolibro_parentesco')) {
            $tr2 = Input::post('tipolibro_parentesco');
            if ($tl->update(Input::post('tipolibro_parentesco'))) {
                Flash::valid('Operación exitosa');
                //enrutando por defecto al index del controller
                return Redirect::to();
            } else {
                Flash::error('Falló Operación');
            }
        }
    }

    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id) {
        $tr = new Tipolibro_parentesco();
        if ($tr->delete((int) $id)) {
            Flash::valid('Operación exitosa');
        } else {
            Flash::error('Falló Operación');
        }

        //enrutando por defecto al index del controller
        return Redirect::to();
    }

}
