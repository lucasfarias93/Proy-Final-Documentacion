<?php

class RolesUsuarios extends ActiveRecord {

//    public $debug = true;

    protected function initialize() {
        $this->belongs_to('usuarios');
        $this->belongs_to('roles');
    }

    /**
     * Crea una asociación Usuario, Rol
     * @param  int $usuario id usuario
     * @param  int    $rol     id rol
     * @return boolean  
     */
    public function asignarRol($id_usuario,$id_rol){
        return ($this->create(array(
            'usuarios_id' => $id_usuario,
            'roles_id' => $id_rol,
        )));
    }
}

