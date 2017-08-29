package com.example.lfarias.actasdigitales.Entities;

/**
 * Created by lfarias on 8/28/17.
 */

public class TramiteDni {
    /**
     * Realizar la consulta siempre con un dni particular, sino explota la consulta por traer 2 millones de registros
     */

    private int id;
    private String apellido;
    private String nombres;
    private String dni;
    private long idTramite;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getApellido() {
        return apellido;
    }

    public void setApellido(String apellido) {
        this.apellido = apellido;
    }

    public String getNombres() {
        return nombres;
    }

    public void setNombres(String nombres) {
        this.nombres = nombres;
    }

    public String getDni() {
        return dni;
    }

    public void setDni(String dni) {
        this.dni = dni;
    }

    public long getIdTramite() {
        return idTramite;
    }

    public void setIdTramite(long idTramite) {
        this.idTramite = idTramite;
    }
}
