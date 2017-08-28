package com.example.lfarias.actasdigitales.Entities;

import java.util.Date;

/**
 * Created by lfarias on 8/28/17.
 */

public class LinkRecuperacion {
    private boolean enlaceActivo;
    private String enlaceRecuperacion;
    private Date fechaDeEmision;
    private int id;

    public boolean isEnlaceActivo() {
        return enlaceActivo;
    }

    public void setEnlaceActivo(boolean enlaceActivo) {
        this.enlaceActivo = enlaceActivo;
    }

    public String getEnlaceRecuperacion() {
        return enlaceRecuperacion;
    }

    public void setEnlaceRecuperacion(String enlaceRecuperacion) {
        this.enlaceRecuperacion = enlaceRecuperacion;
    }

    public Date getFechaDeEmision() {
        return fechaDeEmision;
    }

    public void setFechaDeEmision(Date fechaDeEmision) {
        this.fechaDeEmision = fechaDeEmision;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }
}
