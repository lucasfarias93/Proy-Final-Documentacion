<?php
/**
 * Carga del modelo tiporeclamo.
 */
Load::models('tiporeclamo');
class TiporeclamoController extends AdminController {
    /**
     * Obtiene una lista para paginar los tiporeclamo
     */
    public function index($page=1) 
    {
       $tr = new Tiporeclamo();
        if (Input::hasPost("nombretiporeclamo")) {
            $this->listTiporeclamo = $tr->filtrar_por_nombre(Input::post("nombretiporeclamo"), $page);
        } else {
            $this->listTiporeclamo = $tr->paginar($page);
        }
    }
 
    /**
     * Crea un Registro
     */
    public function crear ()
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
            $tr2 = Input::post('tiporeclamo');
            $fechahasta =DateTime::createFromFormat("d/m/Y", $tr2["fechahasta"]);
            $fechadesde =DateTime::createFromFormat("d/m/Y", $tr2["fechadesde"]);
        if($fechahasta && $fechadesde->format("d/m/Y") == $tr2["fechadesde"] && $fechadesde <= $fechahasta) {
            if($tr->update(Input::post('tiporeclamo'))){
                 Flash::valid('Operación exitosa');
                //enrutando por defecto al index del controller
                return Redirect::to();
            } else {
                Flash::error('Falló Operación');
            }
        } else {
            Flash::error('Falló fecha');}
        }
        else {
            //Aplicando la autocarga de objeto, para comenzar la edición
            $this->tiporeclamo = $tr->find_by_id((int)$id);
            $this->tiporeclamo->fechahasta = UtilApp::formatea_fecha_bd_to_pantalla($this->tiporeclamo->fechahasta);
            $this->tiporeclamo->fechadesde = UtilApp::formatea_fecha_bd_to_pantalla($this->tiporeclamo->fechadesde);
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