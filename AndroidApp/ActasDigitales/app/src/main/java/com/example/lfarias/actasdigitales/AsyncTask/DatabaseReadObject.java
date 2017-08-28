package com.example.lfarias.actasdigitales.AsyncTask;

import android.content.Context;
import android.os.AsyncTask;

import com.example.lfarias.actasdigitales.Entities.Roles;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

/**
 * Created by acer on 28/08/2017.
 */

public class DatabaseReadObject extends AsyncTask<String, Void, ResultSet> {

    Callback callback;
    Context context;
    //List<Object> object = new ArrayList<>();

    public DatabaseReadObject(Context context, Callback callback) {
        this.callback = callback;
        this.context = context;
    }

    public interface Callback {
        void getResultSet(List<Object> object);
    }

    @Override
    protected ResultSet doInBackground(String... params) {
        ResultSet rs = null;
        try {
            Class.forName("org.postgresql.Driver");
            // "jdbc:postgresql://IP:PUERTO/DB", "USER", "PASSWORD");
            Connection conn = DriverManager.getConnection(
                    "jdbc:postgresql://190.15.213.87:8888/digital", "postgres", "postgres");
            //En el stsql se puede agregar cualquier consulta SQL deseada.
            Statement st =
                    conn.createStatement(ResultSet.TYPE_SCROLL_SENSITIVE,
                            ResultSet.CONCUR_READ_ONLY);
            rs = st.executeQuery(params[0]);
            conn.close();
        } catch (SQLException se) {
            System.out.println("oops! No se puede conectar. Error: " + se.toString());
        } catch (ClassNotFoundException e) {
            System.out.println("oops! No se encuentra la clase. Error: " + e.getMessage());
        }
        return rs;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        //TODO: implement loading indicator while database write or read objects.
    }

    @Override
    protected void onPostExecute(ResultSet resultSet) {
        super.onPostExecute(resultSet);
        //TODO: return a resultset and the method that call the asynctask will convert the object into a Specific one and set into a list
        List<Object> objects = new ArrayList<>();
        try {
            while (resultSet.next()) {
                Object object = new Roles();
                object.setRol(resultSet.getArray("rol").toString());
                object.setActivo(Integer.parseInt(resultSet.getArray("activo").toString()));
                object.setId(Integer.parseInt(resultSet.getArray("id").toString()));
                objects.add(object);
            }
            callback.getResultSet(object);
        } catch (SQLException e) {
            e.printStackTrace();
        }

    }
}