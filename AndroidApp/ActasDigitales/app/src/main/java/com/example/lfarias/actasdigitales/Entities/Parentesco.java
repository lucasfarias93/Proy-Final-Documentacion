package com.example.lfarias.actasdigitales.Entities;

import java.util.Date;

/**
 * Created by lfarias on 8/28/17.
 */

public class Parentesco {
    private int id;
    private Date fechaAltaParentesco;
    private Date fechaBajaParentesco;
    private String nombreParentesco;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public Date getFechaAltaParentesco() {
        return fechaAltaParentesco;
    }

    public void setFechaAltaParentesco(Date fechaAltaParentesco) {
        this.fechaAltaParentesco = fechaAltaParentesco;
    }

    public Date getFechaBajaParentesco() {
        return fechaBajaParentesco;
    }

    public void setFechaBajaParentesco(Date fechaBajaParentesco) {
        this.fechaBajaParentesco = fechaBajaParentesco;
    }

    public String getNombreParentesco() {
        return nombreParentesco;
    }

    public void setNombreParentesco(String nombreParentesco) {
        this.nombreParentesco = nombreParentesco;
    }
}
