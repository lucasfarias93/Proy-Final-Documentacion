<?php
/**
 * Carga del modelo Menus...
 */
Load::models('reclamoerroractaestado');
class ReclamoerroractaestadoController extends AdminController {
    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index($page=1) 
    {
        $r = new Reclamoerroractaestado();
        $this->listReclamoErrorActaEstado = $r->getReclamoerroractaestado($page);
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
        if(Input::hasPost('reclamoerroractaestado')){
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $r = new Reclamoerroractaestado(Input::post('reclamoerroractaestado'));
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
        $r = new Reclamoerroractaestado();
 
        //se verifica si se ha enviado el formulario (submit)
        if(Input::hasPost('reclamoerroractaestado')){
 
            if($r->update(Input::post('reclamoerroractaestado'))){
                 Flash::valid('Operación exitosa');
                //enrutando por defecto al index del controller
                return Redirect::to();
            } else {
                Flash::error('Falló Operación');
            }
        } else {
            //Aplicando la autocarga de objeto, para comenzar la edición
            $this->reclamoerroractaestado = $r->find_by_id((int)$id);
        }
    }
 
    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id)
    {
        $r = new Reclamoerroractaestado();
        if ($r->delete((int)$id)) {
                Flash::valid('Operación exitosa');
        }else{
                Flash::error('Falló Operación'); 
        }
 
        //enrutando por defecto al index del controller
        return Redirect::to();
    }
}