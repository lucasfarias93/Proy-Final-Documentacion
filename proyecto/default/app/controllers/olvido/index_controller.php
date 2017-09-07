<?php

class IndexController extends AppController {

    public function index() {
        view::template('olvido');
        view::select(NULL);
        ////Prueba mail
        load::lib("phpmailer/class.phpmailer");
        $mail = new PHPMailer();
//Luego tenemos que iniciar la validación por SMTP:
        $mail->IsSMTP();
        $mail->SMTPDebug = 2;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.gmail.com"; // SMTP a utilizar. Por ej. smtp.elserver.com
        $mail->Username = "diegocosas@gmail.com"; // Correo completo a utilizar
        $mail->Password = "gringodiego"; // Contraseña
        $mail->Port = 465; // Puerto a utilizar
        var_dump($mail->Port);
//Con estas pocas líneas iniciamos una conexión con el SMTP. Lo que ahora deberíamos hacer, es configurar el mensaje a enviar, el //From, etc.
        $mail->From = "diegocosas@gmail.com"; // Desde donde enviamos (Para mostrar)
        $mail->FromName = "Soporte";
//Estas dos líneas, cumplirían la función de encabezado (En mail() usado de esta forma: “From: Nombre <correo@dominio.com>”) de //correo.
        $mail->AddAddress("extintion007@gmail.com"); // Esta es la dirección a donde enviamos
//        $mail->AddAddress("dggomez@mendoza.gov.ar"); // Esta es la dirección a donde enviamos
        $mail->IsHTML(true); // El correo se envía como HTML
        $mail->Subject = "Recuperacion de contrase&ntildea"; // Este es el titulo del email.
        $body = "Para recuperar tu contraseña hace click en el link de abajo de esta!!!!<br />";
        $mail->Body = $body; // Mensaje a enviar
        $exito = $mail->Send(); // Envía el correo.
//También podríamos agregar simples verificaciones para saber si se envió:
        if ($exito) {
            echo "El correo fue enviado correctamente";
        } else {
            echo "Hubo un inconveniente. Contacta a un administrador.";
        }
        var_dump($exito);
    }

}
