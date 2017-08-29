package com.example.lfarias.actasdigitales.Entities;

/**
 * Created by lfarias on 8/28/17.
 */

public class Departamento {
    private int id;
    private String nombreDepartamento;
    private int idProvincia;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNombreDepartamento() {
        return nombreDepartamento;
    }

    public void setNombreDepartamento(String nombreDepartamento) {
        this.nombreDepartamento = nombreDepartamento;
    }

    public int getIdProvincia() {
        return idProvincia;
    }

    public void setIdProvincia(int idProvincia) {
        this.idProvincia = idProvincia;
    }
}
