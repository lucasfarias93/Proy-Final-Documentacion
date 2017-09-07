<?php

Load::models('usuarios');

class UsuariosController extends AdminController {

    /**
     * Luego de ejecutar las acciones, se verifica si la petición es ajax
     * para no mostrar ni vista ni template.
     */
    protected function before_filter() {
        if (Input::isAjax()) {
            View::select(NULL, NULL);
        }
    }

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
     * Cambio de los datos personales de usuario.
     * 
     */
    public function perfil() {
        try {
            $usr = new Usuarios();
            $this->usuario1 = $usr->find_first(Auth::get('id'));
            if (Input::hasPost('usuario1')) {
                if ($usr->update(Input::post('usuario1'))) {
                    Flash::valid('Datos Actualizados Correctamente');
                    $this->usuario1 = $usr;
                }
            } else if (Input::hasPost('usuario2')) {
                if ($usr->cambiarClave(Input::post('usuario2'))) {
                    Flash::valid('Clave Actualizada Correctamente');
                    $this->usuario1 = $usr;
                }
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
                if ($usr->guardar(Input::post('usuario'), Input::post('rolesUser'))) {
                    Flash::valid('El Usuario Ha Sido Agregado Exitosamente...!!!');
                    if (!Input::isAjax()) {
                        return Router::redirect();
                    }
                } else {
                    throw new NegocioExcepcion("Verifique los datos ingresados");
                }
            }
        } catch (NegocioExcepcion $e) {
            Flash::error($e->getMessage());
        } catch (Exception $e) {
            Flash::error("No se pudo guardar el usuario");
            Logger::error($e->getMessage());
            Logger::error($e->getTraceAsString());
        }
    }

    /**
     * Edita los datos de un usuario desde el backend.
     * @param  int $id id del usuario a editar
     */
    public function editar($id) {
        try {

            $id = (int) $id;

            $usr = new Usuarios();

            $this->usuario = $usr->find_first($id);

            if ($this->usuario) {// verificamos la existencia del usuario
                //obtenemos los roles que tiene el usuario
                //para mostrar los checks seleccionados para estos roles.
                $this->rolesUser = $usr->rolesUserIds();

                //obtenemos los roles con los que se crearán los checks.
                $this->roles = Load::model('roles')->find_all_by_activo(1);

                if (Input::hasPost('usuario')) {

                    //guarda los datos del usuario, y le asigna los roles 
                    //seleccionados en el formulario.
                    if ($usr->guardar(Input::post('usuario'), Input::post('rolesUser'))) {
                        Flash::valid('El Usuario Ha Sido Actualizado Exitosamente...!!!');
                        if (!Input::isAjax()) {
                            return Router::redirect();
                        }
                    } else {
                        Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    }
                }
            } else {
                Flash::warning("No existe ningun usuario con id '{$id}'");
                if (!Input::isAjax()) {
                    return Router::redirect();
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    /**
     * Activa un usuario desde el backend
     * @param  int $id id del usuario a activar
     */
    public function activar($id) {
        try {
            $id = (int) $id;
            $usuario = new Usuarios();
            if (!$usuario->find_first($id)) { //si no existe el usuario
                Flash::warning("No existe ningun usuario con id '{$id}'");
            } else if ($usuario->activar()) {
                Flash::valid("La Cuenta del Usuario {$usuario->login} ({$usuario->nombres}) fué activada...!!!");
            } else {
                Flash::warning('No se Pudo Activar la cuenta del Usuario...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }

    /**
     * Desactiva un usuario desde el backend
     * @param  int $id id del usuario a desactivar
     */
    public function desactivar($id) {
        try {
            $id = (int) $id;
            $usuario = new Usuarios();
            if (!$usuario->find_first($id)) { //si no existe el usuario
                Flash::warning("No existe ningun usuario con id '{$id}'");
            } else if ($usuario->desactivar()) {
                Flash::valid("La Cuenta del Usuario {$usuario->login} ({$usuario->nombres}) fué desactivada...!!!");
            } else {
                Flash::warning('No se Pudo Desactivar la cuenta del Usuario...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }

    public function blanquear_clave($id) {
        try {
            $id = (int) $id;
            $usuario = new Usuarios();
            if (!$usuario->find_first($id)) { //si no existe el usuario
                Flash::warning("No existe ningun usuario con id '{$id}'");
            } else {
                $usuario->clave = 'badDr97Or40Ac'; //1234
                $usuario->clave_blanqueada = true;
                if (!$usuario->update()) {
                    Flash::warning("No se ha podido blanquear la contraseña del usuario '{$usuario->login}'");
                } else {
                    Flash::info("Se ha blanqueado exitosamente la contraseña del usuario: '{$usuario->login}'");
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }

    public function ingreso_contrasenia() {
        try {
            if (Input::hasPost('usuario2')) {
                $usuario = Input::post('usuario2');
                $usr = Load::model("usuarios")->find_first("id = '" . $usuario['id'] . "'");
                if ($usr->cambiarClave($usuario)) {
                    Session::delete("usuario_blanqueado");
                    Flash::valid('Clave Actualizada Correctamente');
                    MyAuth::cerrar_sesion();
                } else {
                    if ($usuario["nueva_clave"] === '1234') {
                        Flash::error('La Clave Ingresada NO puede ser 1234');
                    } else {
                        Flash::info('Clave NO SE ha actualizado Correctamente');
                    }
                }
            } else {
                View::select("ingreso_contrasenia", "reingreso_clave");
                $usuario = Load::model("usuarios")->find_first("id=" . Session::get("usuario_blanqueado"));
                $this->usuario2 = $usuario;
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

}
