package com.example.lfarias.actasdigitales.Entities;

/**
 * Created by lfarias on 8/28/17.
 */

public class Localidad {
    private  int id;
    private  String nombreLocalidad;
    private  int idDepartamento;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNombreLocalidad() {
        return nombreLocalidad;
    }

    public void setNombreLocalidad(String nombreLocalidad) {
        this.nombreLocalidad = nombreLocalidad;
    }

    public int getIdDepartamento() {
        return idDepartamento;
    }

    public void setIdDepartamento(int idDepartamento) {
        this.idDepartamento = idDepartamento;
    }
}
