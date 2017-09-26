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
        view::select(null, null);
        try {
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
                if ($usr->guardarCiudadano($usr, 3)) {
                Logger::info("Usuario registrado"); 
                } else {
                    throw  new NegocioExcepcion("Verifique los datos ingresasdos");
                }
            }
        }
        catch (Exception $e) {
            Logger::error("Excepcion en el try");
            Flash::error("No se pudo guardar el usuario");
            Logger::error($e->getMessage());
            Logger::error($e->getTraceAsString());
        }
    }
    
    
    public function crear_mobile($login, $idtramite, $dni, $clave, $clave2, $nombres, $apellido, $email) {
        view::select(null, null);
        try {
                //crear usuario nuevo y usuario de la bd segun el nombre del login
                $usr = new Usuarios();
                $usr -> login = $login;
                $usr -> idtramite = $idtramite;
                $usr -> dni = $dni;
                $usr -> clave = $clave;
                $usr -> clave2 = $clave2;
                $usr -> nombres = $nombres;
                $usr -> apellido = $apellido;
                $usr -> email = $email;
          
                $usrbd = new Usuarios();
                $usrbd->filtrar_por_login($usr->login);
                
                //Buscar coincidencias segun el nombre de login
                if ($usrbd && $usr->login == $usrbd->login) {
                    throw new NegocioExcepcion("El usuario ingresado ya existe");
                }
                
                //Buscar coincidencias segun el id tramite
                $usrbd->filtrar_por_id($usr->idtramite);
                if ($usrbd && $usr->idtramite == $usrbd->idtramite) {
                    throw new NegocioExcepcion("El idtramite ingresado ya existe");
                }
                
                //Buscar coincidencias segun el DNI
                $usrbd->filtrar_por_dni($usr->dni);
                if ($usrbd && $usr->dni == $usrbd->dni) {
                    throw new NegocioExcepcion("El dni ingresado ya existe");
                }        
                $usr->guardarCiudadano($usr, 3);
                view::json(TRUE);
        } catch (Exception $e) {
            view::json(FALSE);
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
