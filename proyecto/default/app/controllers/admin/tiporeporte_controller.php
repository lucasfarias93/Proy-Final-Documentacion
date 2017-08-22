<?php
/**
 * Carga del modelo Menus...
 */
Load::models('tiporeporte');
class TiporeporteController extends AdminController {
    /**
     * Obtiene una lista para paginar los menus
     */
    public function index($page=1) 
    {
       $tr = new Tiporeporte();
        if (Input::hasPost("nombretiporeporte")) {
            $this->listTiporeporte = $tr->filtrar_por_nombre(Input::post("nombretiporeporte"), $page);
        } else {
            $this->listTiporeporte = $tr->paginar($page);
        }
    }
 
    /**
     * Crea un Registro
     */
    public function crear ()
    {
        /**
         * Se verifica si el usuario envio el form (submit) y si ademas 
         * dentro del array POST existe uno llamado "menus"
         * el cual aplica la autocarga de objeto para guardar los 
         * datos enviado por POST utilizando autocarga de objeto
         */
        if(Input::hasPost('tiporeporte')){
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $r = new tiporeporte(Input::post('tiporeporte'));
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
        $r = new tiporeporte();
 
        //se verifica si se ha enviado el formulario (submit)
        if(Input::hasPost('tiporeporte')){
 
            if($r->update(Input::post('tiporeporte'))){
                 Flash::valid('Operación exitosa');
                //enrutando por defecto al index del controller
                return Redirect::to();
            } else {
                Flash::error('Falló Operación');
            }
        } else {
            //Aplicando la autocarga de objeto, para comenzar la edición
            $this->tiporeporte = $r->find_by_id((int)$id);
        }
    }
 
    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id)
    {
        $r = new Tiporeporte();
        if ($r->delete((int)$id)) {
                Flash::valid('Operación exitosa');
        }else{
                Flash::error('Falló Operación'); 
        }
 
        //enrutando por defecto al index del controller
        return Redirect::to();
    }
}