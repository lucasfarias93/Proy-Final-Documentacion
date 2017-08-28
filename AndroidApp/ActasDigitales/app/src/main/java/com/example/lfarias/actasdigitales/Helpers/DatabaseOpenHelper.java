package com.example.lfarias.actasdigitales.Helpers;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

/**
 * Created by acer on 28/08/2017.
 */

/*public class DatabaseOpenHelper {
    private static DatabaseOpenHelper instance = null;
    ResultSet objectResultSet;
    protected DatabaseOpenHelper() {
        //Default constructor
    }

    public static DatabaseOpenHelper getInstance() {
        if (instance == null) {
            instance = new DatabaseOpenHelper();
        }
        return instance;
    }

    public void getDatabaseObjects(final String stsql) {
        Thread sqlstart = new Thread() {
            public void run() {
                try {
                    Class.forName("org.postgresql.Driver");
                    // "jdbc:postgresql://IP:PUERTO/DB", "USER", "PASSWORD");
                    Connection conn = DriverManager.getConnection(
                            "jdbc:postgresql://190.15.213.87:8888/digital", "postgres", "postgres");
                    //En el stsql se puede agregar cualquier consulta SQL deseada.
                    Statement st =
                            conn.createStatement(ResultSet.TYPE_SCROLL_SENSITIVE,
                                    ResultSet.CONCUR_READ_ONLY);
                    ResultSet rs = st.executeQuery(stsql);
                    conn.close();
                } catch (SQLException se) {
                    System.out.println("oops! No se puede conectar. Error: " + se.toString());
                } catch (ClassNotFoundException e) {
                    System.out.println("oops! No se encuentra la clase. Error: " + e.getMessage());
                }

            }

        };
    }
    sqlThread.start();
}*/
