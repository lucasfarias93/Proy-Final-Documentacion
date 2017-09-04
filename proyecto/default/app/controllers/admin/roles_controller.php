<?php
Load::models('roles');

class RolesController extends AdminController {

    /**
     * Luego de ejecutar las acciones, se verifica si la petición es ajax
     * para no mostrar ni vista ni template.
     */
    protected function after_filter() {
        if (Input::isAjax()) {
            View::select(NULL, NULL);
        }
    }

    public function index($pag= 1) {
        try {
            $roles = new Roles();
            $this->roles = $roles->paginate("page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function crear() {
        $this->titulo = 'Crear Rol (Perfil)';
        try {

            if (Input::hasPost('rol')) {
                $rol = new Roles(Input::post('rol'));
                if (Input::hasPost('roles_padres')) {
                    //$rol->padres = join(',', Input::post('roles_padres'));
                }
                if ($rol->save()) {
                    Flash::valid('El Rol Ha Sido Agregado Exitosamente...!!!');
                    if (!Input::isAjax()) {
                        return Router::redirect();
                    }
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function editar($id) {
        $this->titulo = 'Editar Rol (Perfil)';
        try {

            $id = (int) $id;

            View::select('crear');

            $rol = new Roles();

            $this->rol = $rol->find_first($id);

            if ($this->rol) {//verificamos la existencia del rol
                if (Input::hasPost('rol')) {
                    if ($rol->update(Input::post('rol'))) {
                        Flash::valid('El Rol Ha Sido Actualizado Exitosamente...!!!');
                        if (!Input::isAjax()) {
                            return Router::redirect();
                        }
                    } else {
                        Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    }
                }
            } else {
                Flash::warning("No existe ningun rol con id '{$id}'");
                if (!Input::isAjax()) {
                    return Router::redirect();
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function eliminar($id = NULL) {
        try {
            $rol = new Roles();
            if (is_int($id)) {


                if (!$rol->find_first($id)) { //si no existe
                    Flash::warning("No existe ningun rol con id '{$id}'");
                } else if ($rol->delete()) {
                    Flash::valid("El rol <b>{$rol->rol}</b> fué Eliminado...!!!");
                } else {
                    Flash::warning("No se Pudo Eliminar el Rol <b>{$rol->rol}</b>...!!!");
                }
            } elseif (is_string($id)) {
                if ($rol->delete_all("id IN ($id)")) {
                    Flash::valid("Los Roles <b>{$id}</b> fueron Eliminados...!!!");
                } else {
                    Flash::warning("No se Pudieron Eliminar los Roles...!!!");
                }
            } elseif (Input::hasPost('roles_id')) {
                $this->ids = Input::post('roles_id');
                return;
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

    public function activar($id) {
        try {
            $id = (int) $id;

            $rol = new Roles();

            if (!$rol->find_first($id)) { //si no existe
                Flash::warning("No existe ningun rol con id '{$id}'");
            } else if ($rol->activar()) {
                Flash::valid("El rol <b>{$rol->rol}</b> Esta ahora <b>Activo</b>...!!!");
            } else {
                Flash::warning("No se Pudo Activar el Rol <b>{$rol->rol}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        Router::redirect();
    }

    public function desactivar($id) {
        try {
            $id = (int) $id;

            $rol = new Roles();

            if (!$rol->find_first($id)) { //si no existe
                Flash::warning("No existe ningun rol con id '{$id}'");
            } else if ($rol->desactivar()) {
                Flash::valid("El rol <b>{$rol->rol}</b> Esta ahora <b>Inactivo</b>...!!!");
            } else {
                Flash::warning("No se Pudo Desactivar el Rol <b>{$rol->rol}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

}
