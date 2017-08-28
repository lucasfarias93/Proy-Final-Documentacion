package com.example.lfarias.actasdigitales.Entities;

/**
 * Created by lfarias on 8/28/17.
 */

public class Roles {
    private int id;
    /** Contiene los posibles roles administrados en el sistema. Al dia de la fecha (28 de Agosto) existen 3 roles:
     * 1º) Administrador de sistema
     * 2º) Pedido
     * 3º) Ciudadano
     */
    private String rol;
    private int activo;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getRol() {
        return rol;
    }

    public void setRol(String rol) {
        this.rol = rol;
    }

    public int getActivo() {
        return activo;
    }

    public void setActivo(int activo) {
        this.activo = activo;
    }
}
