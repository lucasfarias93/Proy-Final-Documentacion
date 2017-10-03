<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MobileController extends AppController {

    public function login($user, $pass) {
        view::select(null, null);
        view::json(MyAuth::autenticar($user, $pass, TRUE));
    }

}
