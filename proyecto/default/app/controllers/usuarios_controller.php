<?php

Load::models('usuarios');

class UsuariosController extends AppController {

    public function index($pagina = 1, $migrar = False) {
        try {
            $usr = new Usuarios();
            if (Input::hasPost("nombre_usuario")) {
                $this->usuarios = $usr->filtrar_por_usuario(Input::post("nombre_usuario"), $pagina);
            } else {
                $this->usuarios = $usr->paginar($pagina);
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
    
    /**
     * Crea un usuario desde el backend.
     */
    public function crear() {
        try {
            Logger::error("log 1");
            //obtenemos los usuarios activos para listarlos en el form
            //ya que al crear un user se deben especificar los roles que poseerá
            $this->roles = Load::model('roles')->find_all_by_activo(1);

            if (Input::hasPost('usuario')) {
                //esto es para tener atributos que no son campos de la tabla
                $usr = new Usuarios(Input::post('usuario'));
                $usrbd = new Usuarios();
                $usrbd->filtrar_por_login($usr->login);
                
                if ($usrbd && $usr->login == $usrbd->login) {
                    throw new NegocioExcepcion("El usuario ingresado ya existe");
                }
                $usrbd->filtrar_por_id($usr->idtramite);
                if ($usrbd && $usr->idtramite == $usrbd->idtramite) {
                    throw new NegocioExcepcion("El idtramite ingresado ya existe");
                }
                $usrbd->filtrar_por_dni($usr->dni);
                if ($usrbd && $usr->dni == $usrbd->dni) {
                    throw new NegocioExcepcion("El dni ingresado ya existe");
                }
                //Verifico si el dni del usuario o el idtramite ya existe
                //guarda los datos del usuario, y le asigna los roles 
                //seleccionados en el formulario.
                Logger::error("log 2");
                if ($usr->guardarCiudadano(Input::post('usuario'), 3)) {
                    Flash::valid('El Usuario Ha Sido Agregado Exitosamente...!!!');
                    Logger::error("log 3");
                    if (!Input::isAjax()) {
                        return Router::redirect();
                        Logger::error("log 4");
                    }
                } else {
                    Logger::error("log 5");
                    throw new NegocioExcepcion("Verifique los datos ingresados");
                }
            }
        } catch (Exception $e) {
            Flash::error("No se pudo guardar el usuario");
            Logger::error($e->getMessage());
            Logger::error($e->getTraceAsString());
        }
    }
    
    
    public function crear_mobile($usuario, $rol) {
        try {
            Logger::error("log 1");
            View::select(null, null);
                //esto es para tener atributos que no son campos de la tabla
                $usr = new Usuarios($usuario);
                $usrbd = new Usuarios();
                $usrbd->filtrar_por_login($usr->login);
                
                if ($usrbd && $usr->login == $usrbd->login) {
                    throw new NegocioExcepcion("El usuario ingresado ya existe");
                }
                $usrbd->filtrar_por_id($usr->idtramite);
                if ($usrbd && $usr->idtramite == $usrbd->idtramite) {
                    throw new NegocioExcepcion("El idtramite ingresado ya existe");
                }
                $usrbd->filtrar_por_dni($usr->dni);
                if ($usrbd && $usr->dni == $usrbd->dni) {
                    throw new NegocioExcepcion("El dni ingresado ya existe");
                }
                //Verifico si el dni del usuario o el idtramite ya existe
                //guarda los datos del usuario, y le asigna los roles 
                //seleccionados en el formulario.
                Logger::error("log 2");
                if ($usr->guardarCiudadano($usuario, 3)) {
                    Flash::valid('El Usuario Ha Sido Agregado Exitosamente...!!!');
                    Logger::error("log 3");
                    if (!Input::isAjax()) {
                        return Router::redirect();
                        Logger::error("log 4");
                    }
                } else {
                    Logger::error("log 5");
                    throw new NegocioExcepcion("Verifique los datos ingresados");
                }
        } catch (Exception $e) {
            Flash::error("No se pudo guardar el usuario");
            Logger::error($e->getMessage());
            Logger::error($e->getTraceAsString());
        }
    }
    
    
    
    public function usuario($usuario) {
        $usr = new Usuarios();
        view::select(null, null);
        $user = $usr ->filtrar_por_usuario($usuario, $pagina = 1);
        view::json($user);
    }
    
}
