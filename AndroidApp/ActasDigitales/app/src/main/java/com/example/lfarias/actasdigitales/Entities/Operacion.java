package com.example.lfarias.actasdigitales.Entities;

import java.util.Date;

/**
 * Created by lfarias on 8/28/17.
 */

public class Operacion {
    private int id;
    private String descripcionOperacion;
    private String nombreOperacion;
    private Date fechaBajaOperacion;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getDescripcionOperacion() {
        return descripcionOperacion;
    }

    public void setDescripcionOperacion(String descripcionOperacion) {
        this.descripcionOperacion = descripcionOperacion;
    }

    public String getNombreOperacion() {
        return nombreOperacion;
    }

    public void setNombreOperacion(String nombreOperacion) {
        this.nombreOperacion = nombreOperacion;
    }

    public Date getFechaBajaOperacion() {
        return fechaBajaOperacion;
    }

    public void setFechaBajaOperacion(Date fechaBajaOperacion) {
        this.fechaBajaOperacion = fechaBajaOperacion;
    }
}
