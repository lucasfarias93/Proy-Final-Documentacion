<?php
/**
 * Carga del modelo Menus...
 */
 
class RolController extends AppController {
    /**
     * Obtiene una lista para paginar los menus
     */
    public function index($page=1) 
    {
        $r = new Rol();
        $this->listRol = $r->getRol($page);
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
        if(Input::hasPost('rol')){
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $r = new Rol(Input::post('rol'));
            //En caso que falle la operación de guardar
            if($r->create()){
                foreach (Input::post('operaciones') as $operacion){
                $oprol = Load::model('operacionrol');
                $oprol->idoperacion = $operacion;
                $oprol->idrol = $r->id;
                $oprol->create();
                }
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
        $r = new Rol();
 
        //se verifica si se ha enviado el formulario (submit)
        if(Input::hasPost('rol')){
 
            if($r->update(Input::post('rol'))){
                 Flash::valid('Operación exitosa');
                //enrutando por defecto al index del controller
                return Redirect::to();
            } else {
                Flash::error('Falló Operación');
            }
        } else {
            //Aplicando la autocarga de objeto, para comenzar la edición
            $this->rol = $r->find_by_id((int)$id);
        }
    }
 
    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id)
    {
        $r = new Rol();
        if ($r->delete((int)$id)) {
                Flash::valid('Operación exitosa');
        }else{
                Flash::error('Falló Operación'); 
        }
 
        //enrutando por defecto al index del controller
        return Redirect::to();
    }
}