<?php

require("class.phpmailer.php");
$mail = new PHPMailer();

//Luego tenemos que iniciar la validación por SMTP:
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->Host = "smtp.gmail.com"; // SMTP a utilizar. Por ej. smtp.elserver.com
$mail->Username = "diegocosas@gmail.com"; // Correo completo a utilizar
$mail->Password = "gringodiego"; // Contraseña
$mail->Port = 465; // Puerto a utilizar
//Con estas pocas líneas iniciamos una conexión con el SMTP. Lo que ahora deberíamos hacer, es configurar el mensaje a enviar, el //From, etc.
$mail->From = "diegocosas@gmail.com"; // Desde donde enviamos (Para mostrar)
$mail->FromName = "Soporte";

//Estas dos líneas, cumplirían la función de encabezado (En mail() usado de esta forma: “From: Nombre <correo@dominio.com>”) de //correo.
$mail->AddAddress("dggomez@mendoza.gov.ar"); // Esta es la dirección a donde enviamos
$mail->IsHTML(true); // El correo se envía como HTML
$mail->Subject = "Recuperacion de contraseña"; // Este es el titulo del email.
$body = "Para recuperar tu contraseña hace click en el link de abajo<br />";
$body .= "aca va el link";
$mail->Body = $body; // Mensaje a enviar
$exito = $mail->Send(); // Envía el correo.
//También podríamos agregar simples verificaciones para saber si se envió:
if ($exito) {
    echo "El correo fue enviado correctamente";
} else {
    echo "Hubo un inconveniente. Contacta a un administrador.";
}