<?php

Load::models('usuarios');
Load::models('linkrecuperacion');

class IndexController extends AppController {

    public function index() {

        load::lib("phpmailer/class.phpmailer");
        load::lib("phpmailer/class.smtp");
        $email_user = "diegocosas@gmail.com";
        $email_password = "gringodiego";
        $the_subject = "Phpmailer prueba by Evilnapsis.com";
        $address_to = "dggomez@mendoza.gov.ar";
        $from_name = "Evilnapsis";
        $phpmailer = new PHPMailer();
// ---------- datos de la cuenta de Gmail -------------------------------
        $phpmailer->Username = $email_user;
        $phpmailer->Password = $email_password;
//-----------------------------------------------------------------------
// $phpmailer->SMTPDebug = 1;
        $phpmailer->SMTPSecure = 'ssl';
        $phpmailer->Host = "smtp.gmail.com"; // GMail
        $phpmailer->Port = 465;
        $phpmailer->IsSMTP(); // use SMTP
        $phpmailer->SMTPAuth = true;
        $phpmailer->setFrom($phpmailer->Username, $from_name);
        $phpmailer->AddAddress($address_to); // recipients email
        $phpmailer->Subject = $the_subject;
        $phpmailer->Body .= "<h1 style='color:#3498db;'>Hola Mundo!</h1>";
        $phpmailer->Body .= "<p>Mensaje personalizado</p>";
        $phpmailer->Body .= "<p>Fecha y Hora: " . date("d-m-Y h:i:s") . "</p>";
        $phpmailer->IsHTML(true);
        $phpmailer->Send();



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
//                $mail->SMTPAuth = false;
//                $mail->SMTPSecure = "ssl";
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
