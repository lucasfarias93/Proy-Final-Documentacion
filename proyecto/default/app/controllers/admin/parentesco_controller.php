<?php
/**
 * Carga del modelo tiporeclamo.
 */
Load::models('parentesco');

class ParentescoController extends AdminController {
    /**
     * Obtiene una lista para paginar los tiporeclamo
     */
    public function index($page=1) 
    {
        $tr = new Parentesco();
        $this->listParentesco = $tr->getParentesco($page);
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
        if(Input::hasPost('parentesco')){
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $tr = new Parentesco(Input::post('parentesco'));
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
        $tr = new Parentesco();
 
        //se verifica si se ha enviado el formulario (submit)
        if(Input::hasPost('parentesco')){
            $par = Input::post('parentesco');
            $fechahasta =DateTime::createFromFormat("d/m/Y", $par["fechahasta"]);
            $fechadesde =DateTime::createFromFormat("d/m/Y", $par["fechadesde"]);
        if($fechahasta && $fechadesde->format("d/m/Y") == $par["fechadesde"] && $fechadesde <= $fechahasta) {
            if($tr->update(Input::post('parentesco'))){
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
            $this->parentesco = $tr->find_by_id((int)$id);
            $this->parentesco->fechahasta = UtilApp::formatea_fecha_bd_to_pantalla($this->parentesco->fechahasta);
            $this->parentesco->fechadesde = UtilApp::formatea_fecha_bd_to_pantalla($this->parentesco->fechadesde);
        }
    }
 
    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id)
    {
        $tr = new Parentesco();
        if ($tr->delete((int)$id)) {
                Flash::valid('Operación exitosa');
        }else{
                Flash::error('Falló Operación'); 
        }
 
        //enrutando por defecto al index del controller
        return Redirect::to();
    }
    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function buscar_parentesco_tipolibro()
    {
        if(Input::hasPost('tipolibro')){
        $tr = new Parentesco();
        $listParentesco = $tr->filtrar_parentesco_por_tipolibro(input::post('tipolibro'));
            view::json($listParentesco);
        }
    }
}
