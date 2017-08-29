package com.example.lfarias.actasdigitales.Entities;

import java.util.Date;

/**
 * Created by lfarias on 8/28/17.
 */

public class CodigoProvincial {
    private Date fechaDesde;
    private Date fechaHasta;
    private int id;
    private int importeCodigo;
    private int numeroCodigoProvincial;

    public Date getFechaDesde() {
        return fechaDesde;
    }

    public void setFechaDesde(Date fechaDesde) {
        this.fechaDesde = fechaDesde;
    }

    public Date getFechaHasta() {
        return fechaHasta;
    }

    public void setFechaHasta(Date fechaHasta) {
        this.fechaHasta = fechaHasta;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getImporteCodigo() {
        return importeCodigo;
    }

    public void setImporteCodigo(int importeCodigo) {
        this.importeCodigo = importeCodigo;
    }

    public int getNumeroCodigoProvincial() {
        return numeroCodigoProvincial;
    }

    public void setNumeroCodigoProvincial(int numeroCodigoProvincial) {
        this.numeroCodigoProvincial = numeroCodigoProvincial;
    }
}
