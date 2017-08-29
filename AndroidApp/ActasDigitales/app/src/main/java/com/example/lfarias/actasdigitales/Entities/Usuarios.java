package com.example.lfarias.actasdigitales.Entities;

/**
 * Created by lfarias on 8/28/17.
 */

public class Usuarios {
    private int id;
    private String login;
    private String clave;
    private String nombres;
    private String email;
    private int activo;
    private boolean clave_blanqueada;
    private String dni;
    private String apellido;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getLogin() {
        return login;
    }

    public void setLogin(String login) {
        this.login = login;
    }

    public String getClave() {
        return clave;
    }

    public void setClave(String clave) {
        this.clave = clave;
    }

    public String getNombres() {
        return nombres;
    }

    public void setNombres(String nombres) {
        this.nombres = nombres;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public int getActivo() {
        return activo;
    }

    public void setActivo(int activo) {
        this.activo = activo;
    }

    public boolean isClave_blanqueada() {
        return clave_blanqueada;
    }

    public void setClave_blanqueada(boolean clave_blanqueada) {
        this.clave_blanqueada = clave_blanqueada;
    }

    public String getDni() {
        return dni;
    }

    public void setDni(String dni) {
        this.dni = dni;
    }

    public String getApellido() {
        return apellido;
    }

    public void setApellido(String apellido) {
        this.apellido = apellido;
    }
}
