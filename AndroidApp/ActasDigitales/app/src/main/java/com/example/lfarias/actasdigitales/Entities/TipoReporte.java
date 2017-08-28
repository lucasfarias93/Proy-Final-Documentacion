package com.example.lfarias.actasdigitales.Entities;

import java.util.Date;

/**
 * Created by lfarias on 8/28/17.
 */

public class TipoReporte {
    private int id;
    private Date fechaDesde;
    private String descripcionTipoReporte;
    private String nombreTipoReporte;
    private Date fechaHasta;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public Date getFechaDesde() {
        return fechaDesde;
    }

    public void setFechaDesde(Date fechaDesde) {
        this.fechaDesde = fechaDesde;
    }

    public String getDescripcionTipoReporte() {
        return descripcionTipoReporte;
    }

    public void setDescripcionTipoReporte(String descripcionTipoReporte) {
        this.descripcionTipoReporte = descripcionTipoReporte;
    }

    public String getNombreTipoReporte() {
        return nombreTipoReporte;
    }

    public void setNombreTipoReporte(String nombreTipoReporte) {
        this.nombreTipoReporte = nombreTipoReporte;
    }

    public Date getFechaHasta() {
        return fechaHasta;
    }

    public void setFechaHasta(Date fechaHasta) {
        this.fechaHasta = fechaHasta;
    }
}
