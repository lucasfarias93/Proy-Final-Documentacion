<?php

Load::models('usuarios');

class IndexController extends AppController {

    public function index() {
        view::template(NULL);

        try {
            if (Input::hasPost('usuarios')) {
                $usr = new Usuarios(Input::post('usuarios'));
                $usrbd = new Usuarios();
                $usrbd->filtrar_por_email($usr->email);
                if ($usrbd && $usr->email == $usrbd->email) {
                    ////Mandar mail
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
//Con estas pocas líneas iniciamos una conexión con el SMTP. Lo que ahora deberíamos hacer, es configurar el mensaje a enviar, el //From, etc.
                    $mail->From = "diegocosas@gmail.com"; // Desde donde enviamos (Para mostrar)
                    $mail->FromName = "Soporte";
//Estas dos líneas, cumplirían la función de encabezado (En mail() usado de esta forma: “From: Nombre <correo@dominio.com>”) de //correo.
                    $mail->AddAddress($usr->email); // Esta es la dirección a donde enviamos
                    //$mail->AddAddress("dggomez@mendoza.gov.ar"); // Esta es la dirección a donde enviamos
                    $mail->IsHTML(true); // El correo se envía como HTML
                    $link ='<a href="http://190.15.213.87/proyecto/olvido/blanquear_clave/">Aqui</a>';
                    $mail->Subject = "Recuperacion de contraseña"; // Este es el titulo del email.
                    $body = "Para recuperar tu contrase&ntilde;a hace click " . $link . $usrbd->id;
                    $mail->Body = $body; // Mensaje a enviar
                    $exito = $mail->Send(); // Envía el correo.
                } if ($usr->email != $usrbd->email) {
                    throw new NegocioExcepcion("El mail ingresado no existe");
                }

//También podríamos agregar simples verificaciones para saber si se envió:
                if ($exito) {
                    Flash::info("El correo fue enviado correctamente");
                    input::delete();
                    Router::redirect('login');
                } else {
                    Flash::info("No se pudo enviar el correo");
                    input::delete();
                }
            }
        } catch (NegocioExcepcion $e) {
            echo "El mail ingresado no existe en la Base de datos ";
            Flash::error($e->getMessage());
        }
    }

}
