<?php
/**
 * Carga del modelo tiporeclamo.
 */
 
class TiporeclamoController extends AppController {
    /**
     * Obtiene una lista para paginar los tiporeclamo
     */
    public function index($page=1) 
    {
        $tr = new Tiporeclamo();
        $this->listTiporeclamo = $tr->getTiporeclamo($page);
    }
 
    /**
     * Crea un Registro
     */
    public function create ()
    {
        /**
         * Se verifica si el usuario envio el form (submit) y si ademas 
         * dentro del array POST existe uno llamado "tiporeclamo"
         * el cual aplica la autocarga de objeto para guardar los 
         * datos enviado por POST utilizando autocarga de objeto
         */
        if(Input::hasPost('tiporeclamo')){
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $tr = new Tiporeclamo(Input::post('tiporeclamo'));
            //En caso que falle la operación de guardar
            if($tr->create()){
                Flash::valid('Operación exitosa');
                //Eliminamos el POST, si no queremos que se vean en el form
                Input::delete();
                return;               
            }else{
                Flash::error('Falló Operación');
            }
        }
    }
 
    /**
     * Edita un Registro
     *
     * @param int $id (requerido)
     */
    public function edit($id)
    {
        $tr = new Tiporeclamo();
 
        //se verifica si se ha enviado el formulario (submit)
        if(Input::hasPost('tiporeclamo')){
 
            if($tr->update(Input::post('tiporeclamo'))){
                 Flash::valid('Operación exitosa');
                //enrutando por defecto al index del controller
                return Redirect::to();
            } else {
                Flash::error('Falló Operación');
            }
        } else {
            //Aplicando la autocarga de objeto, para comenzar la edición
            $this->tiporeclamo = $tr->find_by_id((int)$id);
        }
    }
 
    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id)
    {
        $tr = new Tiporeclamo();
        if ($tr->delete((int)$id)) {
                Flash::valid('Operación exitosa');
        }else{
                Flash::error('Falló Operación'); 
        }
 
        //enrutando por defecto al index del controller
        return Redirect::to();
    }
}