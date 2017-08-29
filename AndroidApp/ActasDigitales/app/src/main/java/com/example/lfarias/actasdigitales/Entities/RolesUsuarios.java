package com.example.lfarias.actasdigitales.Entities;

/**
 * Created by lfarias on 8/28/17.
 */

public class RolesUsuarios {
    private int id;
    private int roles_id;
    private int usuarios_id;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getRoles_id() {
        return roles_id;
    }

    public void setRoles_id(int roles_id) {
        this.roles_id = roles_id;
    }

    public int getUsuarios_id() {
        return usuarios_id;
    }

    public void setUsuarios_id(int usuarios_id) {
        this.usuarios_id = usuarios_id;
    }
}
