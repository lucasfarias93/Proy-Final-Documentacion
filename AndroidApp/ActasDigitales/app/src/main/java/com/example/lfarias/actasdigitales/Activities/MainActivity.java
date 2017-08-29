package com.example.lfarias.actasdigitales.Activities;

import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;

import com.example.lfarias.actasdigitales.AsyncTask.DatabaseReadObject;
import com.example.lfarias.actasdigitales.Entities.Roles;
import com.example.lfarias.actasdigitales.R;

import java.util.List;

public class MainActivity extends AppCompatActivity implements DatabaseReadObject.Callback {

    private TextView textView1, textView2, textView3, textView4, textView5, textView6, textView7, textView8, textView9, textView10, textView11, textView12;
    private ImageView imageView1, imageView2, imageView3;

    private Button buttonLaunch;

    //DatabaseOpenHelper databaseInstance = DatabaseOpenHelper.getInstance();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        textView1 = (TextView) findViewById(R.id.text1);
        textView2 = (TextView) findViewById(R.id.text2);
        textView3 = (TextView) findViewById(R.id.text3);
        textView4 = (TextView) findViewById(R.id.text4);
        textView5 = (TextView) findViewById(R.id.text5);
        textView6 = (TextView) findViewById(R.id.text6);
        textView7 = (TextView) findViewById(R.id.text7);
        textView8 = (TextView) findViewById(R.id.text8);
        textView9 = (TextView) findViewById(R.id.text9);
        textView10 = (TextView) findViewById(R.id.text10);
        textView11 = (TextView) findViewById(R.id.text11);
        textView12 = (TextView) findViewById(R.id.text12);

        buttonLaunch = (Button) findViewById(R.id.buttonLaunch);
        buttonLaunch.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent i = new Intent(MainActivity.this, LoginActivity.class);
                startActivity(i);
            }
        });

        DatabaseReadObject readObjectAsyncTask = new DatabaseReadObject(this, this);
        readObjectAsyncTask.execute("select * from roles");
    }

    @Override
    public void getResultSet(List<Object> object) {
        for (int i = 0; i < object.size(); i++) {
            Log.i("Database objects", roles.get(i).getRol());
        }
    }
}

