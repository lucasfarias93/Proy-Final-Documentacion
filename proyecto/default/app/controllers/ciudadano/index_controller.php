<?php
class IndexController extends AdminController {
    protected function before_filter() {
view::select(NULL,'solicitar');
    }
    public function index() {
        
    }
    }