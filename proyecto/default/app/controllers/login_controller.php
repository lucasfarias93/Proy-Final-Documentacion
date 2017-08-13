<?php
/**
 * Carga del modelo Menus...
 */
 
class LoginController extends AdminController {
    /**
     * Obtiene una lista para paginar los menus
     */
    public function index() 
    {
       view::select(NULL, 'logueo');
    }
 
    /**
     * Crea un Registro
     */
    
    
}