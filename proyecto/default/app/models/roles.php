<?php

class Roles extends ActiveRecord {
    
//    public $debug = true;
    
    protected function initialize() {
        //relaciones
        $this->has_and_belongs_to_many('recursos', 'model: recursos', 'fk: recursos_id', 'through: roles_recursos', 'key: roles_id');
        $this->has_and_belongs_to_many('usuarios', 'model: usuarios', 'fk: usuarios_id', 'through: roles_usuarios', 'key: roles_id');
        
        //validaciones
        $this->validates_presence_of('rol','message: Debe escribir el <b>Nombre del Rol</b>');
        $this->validates_uniqueness_of('rol','message: Este Rol <b>ya existe</b> en el sistema');
        
    }

    /**
     * Devuelve los recursos a los que un rol tiene acceso.
     * 
     * @return array 
     */
    public function getRecursos(){
        $columnas = "r.*";
        $join = "INNER JOIN roles_recursos as rr ON rr.roles_id = roles.id ";
        $join .= "INNER JOIN recursos as r ON rr.recursos_id = r.id ";
        $where = "roles.id = '$this->id'";
        return $this->find($where, "columns: $columnas" , "join: $join");
    }

}

