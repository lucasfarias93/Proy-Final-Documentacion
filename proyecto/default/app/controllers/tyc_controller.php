<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TycController extends AppController {

    public function index() {
        view::select(NULL, 'tyc');
    }

}
