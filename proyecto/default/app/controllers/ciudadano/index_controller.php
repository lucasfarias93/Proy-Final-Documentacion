<?php
class IndexController extends AppController {

    public function index() {
        view::select(NULL, 'solicitar');
    }
    }