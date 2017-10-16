<?php
require_once "mercadopago.php";

$mp = new MP ("8964709010269463", "6Oxo9JtYScHX7UcOiJYuQ9PEOy4rnbCy");

        $preference_data = array (
        "items" => array (
            array (
                "title" => "Codigo Acta",
                "quantity" => 1,
                "currency_id" => "USD",
                "unit_price" => 1
            )
        )
    );

    $preference = $mp->create_preference($preference_data);
    print_r($preference);
?>