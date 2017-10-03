<?php

class FrameworkController extends RestController {

    protected $fw = array(1 => array(
            "name" => "KumbiaPHP",
            "description" => "Funciono el login"
        ),
        array(
            "name" => "Laravel",
            "description" => "The new boy in the neighbourhood"
        ),
        array(
            "name" => "Symfony",
            "description" => "The old veteran man"
        ),
    );

    public function get($id) {
        if (isset($this->fw[$id])) {
            $this->data = $this->fw[$id];
        } else {
            $this->error('This framework doesn\'t exist', 404);
        }
    }

    public function getAll() {
        $this->data = $this->fw;
    }

}
