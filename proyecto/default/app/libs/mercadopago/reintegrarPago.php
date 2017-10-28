<?php
require_once ('mercadopago.php');

$mp = new MP ("8964709010269463", "6Oxo9JtYScHX7UcOiJYuQ9PEOy4rnbCy");

$mp->refund_payment("3086334199");
?>