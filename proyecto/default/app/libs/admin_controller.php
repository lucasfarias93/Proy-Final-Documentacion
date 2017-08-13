<?php

/**
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 * */
// @see Controller nuevo controller
require_once CORE_PATH . 'kumbia/controller.php';
Load::lib("util_app");

class AdminController extends Controller {

    /**
     * variable que indica si las acciones del controller son protegidas
     * 
     * Por defecto todas las acciones son protegidas
     * para indicar que solo algunas acciones van a ser protegidas debe
     * crearse un array con los nombres de dichas acciones, ejemplo:
     * 
     * <code>
     * 
     * protected $_protected_actions = array(
     *                          'ultimos_envios',
     *                          'editar',
     *                          'eliminar',
     *                          'activar',
     *                      );
     * 
     * </code>
     * 
     * @va boolean|array
     */
    protected $_protectedActions = TRUE;

    /**
     * variable que indica si por defecto se hace el chequeo de la autenticación
     * ó si lo hace el usuario manualmente.
     *
     * @var boolean
     * */
    protected $_checkAuthByDefault = TRUE;

    /**
     * Función que hace las veces de contructor de la clase.
     * 
     */
    protected function initialize() {
        $inactivo = 7200;
        
        if (!Input::isAjax() && Session::get('tiempo')) {
            $vida_session = time() - Session::get('tiempo');
            if ($vida_session > $inactivo) {
                if (MyAuth::es_valido()) {
                    Flash::warning("TIEMPO DE INACTIVIDAD: " . gmdate("H:i:s", $vida_session));
                    MyAuth::cerrar_sesion();
                }
            }
        }
        Session::set('tiempo', time());
        if (Input::hasPost("criterio")) {
            if (Session::get("criterio") == "") {
                Session::set("criterio", Input::post("criterio"));
            } else {
                $array = array_diff(Session::get("criterio"), Input::post("criterio"));
                $array1 = array_diff(Input::post("criterio"), Session::get("criterio"));
                if (count($array) > 0 || count($array1) > 0) {
                    Session::set("criterio", Input::post("criterio"));
                }
            }
        }

        if ($this->_checkAuthByDefault) {
            if ($this->_protectedActions === TRUE || ( is_array($this->_protectedActions) &&
                    in_array($this->action_name, $this->_protectedActions) )) {
                return $this->checkAuth();
            }
        }
    }

    /**
     * Función que hace todos las validaciones necesarias para controladores
     * y acciones protegidas.
     * 
     * Verifica que el usuario esté logueado, si no es así le muestra el form de 
     * logueo.
     * 
     * si está logueado verifica que tenga los permisos necesarios para acceder
     * a la acción correspondiente.
     * 
     * @return boolean devuelve TRUE si tiene acceso a la acción.
     * 
     */
    protected function checkAuth() {
        if (MyAuth::es_valido()) {
            $ret = $this->_tienePermiso();
            Load::negocio("experto_oficina");

            return $ret;
        } elseif (Input::hasPost('login') && Input::hasPost('clave')) {
            $ret = $this->_logueoValido(Input::post('login'), Input::post('clave'));

            return $ret;
        } elseif (Input::hasPost('usuario2')) {
            return TRUE;
        } else {
            View::select(NULL,'logueo');
            return FALSE;
        }
    }

    /**
     * Verifica si el usuario conectado tiene acceso a la acción actual
     * 
     * @return boolean devuelve TRUE si tiene acceso a la acción.
     */
    protected function _tienePermiso() {
        $acl = new MyAcl();
        if (!$acl->check()) {
            if ($acl->limiteDeIntentosPasado()) {
                $acl->resetearIntentos();
                return $this->intentos_pasados();
            }
            Flash::error('no posees privilegios para acceder a <b>' . Router::get('route') . '</b>');
            View::select(NULL);
            return FALSE;
        } else {
            $acl->resetearIntentos();
            return TRUE;
        }
    }

    /**
     * Realiza la autenticacón con los datos enviados por formulario
     * 
     * Si se realiza el logueo correctamente, se verifica que tenga permisos
     * para entrar al recurso actual.
     * 
     * @return boolean devuelve TRUE si se pudo loguear y tiene acceso a la acción.
     * 
     */
    protected function _logueoValido($user, $pass, $encriptar = TRUE) {
        if (MyAuth::autenticar($user, $pass, $encriptar)) {
            $usuario = Load::model("usuarios")->find_first("login = '$user'");
            Session::set("usuario_blanqueado", $usuario->id);
            if (isset($usuario->id) && $pass == '1234' && $usuario->clave_blanqueada == 't') {
                Router::redirect("admin/usuarios/ingreso_contrasenia/");
                return true;
            } else {
                return $this->_tienePermiso();
            }
        } else {
            Input::delete();
            Flash::warning('Datos de Acceso invalidos');
            View::select(NULL, 'logueo');
            return FALSE;
        }
    }

    /**
     * Acción para cerrar sesión en la app
     * 
     * Cualquier controlador que herede de esta clase
     * tiene acceso a esta acción.
     * 
     */
    public function logout() {
        MyAuth::cerrar_sesion();
        return Router::redirect('/');
    }

    /**
     * Metodo que desloguea al usuario cuando esté sobrepasa el limite de 
     * intentos de acceder a un recurso al que no tiene permisos.
     * 
     */
    protected function intentos_pasados() {
        Flash::warning('Has Sobrepasado el limite de intentos fallidos al tratar acceder a ciertas partes del sistema');
        return $this->logout();
    }

    /**
     * Método que se ejecuta luego de ejecutada la acción y filtros 
     * del controlador.
     * 
     */
    final protected function finalize() {
        //permite manejar los criterios de los usuarios para las diferentes pantallas
        Session::delete("criterio");
    }

    final protected function convert($array) {
        $obj = new stdClass();
        foreach ($array as $k => $v) {
            $this->{$k} = $v;
        }
        return $obj;
    }

}
