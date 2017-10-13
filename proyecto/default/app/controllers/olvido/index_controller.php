<?php

Load::models('usuarios');
Load::models('linkrecuperacion');

class IndexController extends AppController {

    public function index() {


        load::lib("phpmailer/class.phpmailer");
        $mail = new PHPMailer();
        $mail->IsSMTP();
//        $mail->CharSet = "UTF-8";
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->Username = 'diegocosas@gmail.com';
        $mail->Password = 'gringodiego';
        $mail->SMTPAuth = true;

        $mail->From = 'diegocosas@gmail.com';
        $mail->FromName = 'Mohammad Masoudian';
        $mail->AddAddress('anotherValidGmail@gmail.com');
        $mail->AddReplyTo('phoenixd110@gmail.com', 'Information');

        $mail->IsHTML(true);
        $mail->Subject = "PHPMailer Test Subject via Sendmail, basic";
        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
        $mail->Body = "Hello";

        if (!$mail->Send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent!";
        }
//        load::lib("phpmailer/class.phpmailer");
//        load::lib("phpmailer/class.smtp");
//        load::lib("phpmailer/PHPMailerAutoload");
//        view::template(NULL);
//        try {
//            if (Input::hasPost('usuarios')) {
//                $usr = new Usuarios(Input::post('usuarios'));
//                $usrbd = new Usuarios();
//                $usrbd->filtrar_por_email($usr->email);
//                if (!$usrbd) {
//                    throw new NegocioExcepcion("El mail ingresado no existe");
//                }
//                ////Mandar mail
//                load::lib("phpmailer/class.phpmailer");
//                $mail = new PHPMailer();
////Luego tenemos que iniciar la validación por SMTP:
//                $mail->SMTPOptions = array(
//                    'ssl' => array(
//                        'verify_peer' => false,
//                        'verify_peer_name' => false,
//                        'allow_self_signed' => true
//                    )
//                );
//                $mail->IsSMTP();
//                $mail->SMTPDebug = 2;
//                $mail->SMTPAuth = true;
//                $mail->SMTPSecure = 'ssl';
//                $mail->Host = "smtp.gmail.com"; // SMTP a utilizar. Por ej. smtp.elserver.com
//                $mail->Username = "diegocosas@gmail.com"; // Correo completo a utilizar
//                $mail->Password = "gringodiego"; // Contraseña
//                $mail->Port = 465; // Puerto a utilizar
////Con estas pocas líneas iniciamos una conexión con el SMTP. Lo que ahora deberíamos hacer, es configurar el mensaje a enviar, el //From, etc.
//                $mail->From = "diegocosas@gmail.com"; // Desde donde enviamos (Para mostrar)
//                $mail->FromName = "Soporte";
////Estas dos líneas, cumplirían la función de encabezado (En mail() usado de esta forma: “From: Nombre <correo@dominio.com>”) de //correo.
//                $mail->AddAddress($usr->email); // Esta es la dirección a donde enviamos
//                //$mail->AddAddress("dggomez@mendoza.gov.ar"); // Esta es la dirección a donde enviamos
//                $mail->IsHTML(true); // El correo se envía como HTML
//                $aux = $usrbd->id;
//                $link = '<a href="http://190.15.213.87:81/recuperar/index/index/' . $aux . '">Aqui</a>';
//                $mail->Subject = "Recuperacion de cuenta"; // Este es el titulo del email.
//                $body = "Para recuperar tu cuenta hace click " . $link;
//                $mail->Body = $body; // Mensaje a enviar
//                $exito = $mail->Send(); // Envía el correo.
////También podríamos agregar simples verificaciones para saber si se envió:
//                if ($exito) {
////                    $usrbd->clave = MyAuth::hash("");
////                    $usrbd->update();
//                    Flash::info("El correo fue enviado correctamente");
//                    input::delete();
//                    Router::redirect('login');
//                } else {
//                    $mail->ErrorInfo;
//                    throw new NegocioExcepcion("No se pudo enviar el correo");
//                    input::delete();
//                }
//            }
//        } catch (NegocioExcepcion $e) {
//            Logger::error($e->getMessage());
//            Flash::info($e->getMessage());
//        }
    }

    public function correoandroid($email) {
        view::select(null, null);
        try {
            $usrbd = new Usuarios();
            $usrbd->filtrar_por_email($email);
            if (!$usrbd) {
                throw new NegocioExcepcion("El mail ingresado no existe");
            } else {
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
                $mail->AddAddress($email); // Esta es la dirección a donde enviamos
                //$mail->AddAddress("dggomez@mendoza.gov.ar"); // Esta es la dirección a donde enviamos
                $mail->IsHTML(true); // El correo se envía como HTML
                $codigo = rand(0, 9999);
                $mail->Subject = "Recuperacion de su cuenta"; // Este es el titulo del email.
                $body = "Tu codigo de activacion es: " . $codigo;
                $mail->Body = $body; // Mensaje a enviar
                $exito = $mail->Send(); // Envía el correo.
                $l = new Linkrecuperacion();
                $l->enlacerecuperacion = $codigo;
                $l->enlaceactivo = TRUE;
                $l->fechadeemision = NULL;
                $l->idusuarios = $usrbd->id;
                $l->create();
            }
//También podríamos agregar simples verificaciones para saber si se envió:
            if ($exito) {
                Flash::info("El correo fue enviado correctamente");
                input::delete();
                view::json(TRUE);
            } else {
                Flash::info("No se pudo enviar el correo");
                input::delete();
                view::json(FALSE);
            }
        } catch (NegocioExcepcion $e) {
            echo "El mail ingresado no existe en la Base de datos ";
            Flash::error($e->getMessage());
            view::json(FALSE);
        }
    }

    public function codigoactivacionandroid($codigo) {
        view::select(null, null);
        $codigobd = new Linkrecuperacion();
        $codigobd->filtrar_por_codigo($codigo);

        if ($codigo == $codigobd->enlacerecuperacion) {
            view::json(TRUE);
        } else {
            view::json(FALSE);
        }
    }

}
