<?php
/**
 * Carga del modelo Menus...
 */
Load::models('direccion');
class DireccionController extends AdminController {
    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index($page=1) 
    {
        $r = new Direccion();
        $this->listDireccion = $r->getDireccion($page);
    }
 
    /**
     * Crea un Registro
     */
    public function create ()
    {
        /**
         * Se verifica si el usuario envio el form (submit) y si ademas 
         * dentro del array POST existe uno llamado "menus"
         * el cual aplica la autocarga de objeto para guardar los 
         * datos enviado por POST utilizando autocarga de objeto
         */
        if(Input::hasPost('direccion')){
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $r = new Direccion(Input::post('direccion'));
            //En caso que falle la operación de guardar
            if($r->create()){
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
        $r = new Direccion();
 
        //se verifica si se ha enviado el formulario (submit)
        if(Input::hasPost('direccion')){
 
            if($r->update(Input::post('direccion'))){
                 Flash::valid('Operación exitosa');
                //enrutando por defecto al index del controller
                return Redirect::to();
            } else {
                Flash::error('Falló Operación');
            }
        } else {
            //Aplicando la autocarga de objeto, para comenzar la edición
            $this->direccion = $r->find_by_id((int)$id);
        }
    }
 
    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id)
    {
        $r = new Direccion();
        if ($r->delete((int)$id)) {
                Flash::valid('Operación exitosa');
        }else{
                Flash::error('Falló Operación'); 
        }
 
        //enrutando por defecto al index del controller
        return Redirect::to();
    }
}