package com.example.lfarias.actasdigitales.Entities;

import java.util.Date;

/**
 * Created by lfarias on 8/28/17.
 */

public class TipoReclamo {
    private int id;
    private  String nobmreTipoReclamo;
    private Date fechaDesde;
    private String descripcionTipoReclamo;
    private Date fechaHasta;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNobmreTipoReclamo() {
        return nobmreTipoReclamo;
    }

    public void setNobmreTipoReclamo(String nobmreTipoReclamo) {
        this.nobmreTipoReclamo = nobmreTipoReclamo;
    }

    public Date getFechaDesde() {
        return fechaDesde;
    }

    public void setFechaDesde(Date fechaDesde) {
        this.fechaDesde = fechaDesde;
    }

    public String getDescripcionTipoReclamo() {
        return descripcionTipoReclamo;
    }

    public void setDescripcionTipoReclamo(String descripcionTipoReclamo) {
        this.descripcionTipoReclamo = descripcionTipoReclamo;
    }

    public Date getFechaHasta() {
        return fechaHasta;
    }

    public void setFechaHasta(Date fechaHasta) {
        this.fechaHasta = fechaHasta;
    }
}
