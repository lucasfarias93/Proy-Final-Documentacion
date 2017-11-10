<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Load::models('usuarios');
Load::models('solicitudacta');
Load::models('solicitudestado');

class NotificarController extends AppController {

    public function index() {
        view::select(NULL, NULL);
        $fechaactual = UtilApp::fecha_actual();
        $sola = new Solicitudacta();
        $solacta = $sola->buscar_todas();
        foreach ($solacta as $sa) {
            $diasrestantes = 180 - UtilApp::calcular_dias_entre_fechas($sa->fechacambioestado, $fechaactual);
            if ($diasrestantes < 5 && $diasrestantes > 0 && $sa->notificado == "no") {
                Flash::info("Acta valida quedan " . $diasrestantes . " dias");
                $this->mail_vigencia($diasrestantes, $sa->idusuario, $sa->fechacambioestado, $sa->nombrepropietarioacta, $sa->nombreparentesco, $sa->nombrelibro);
                $sa->notificado = "si";
                $sa->update();
            } else {
                Flash::error("No hay actas por vencer");
            }
        }
    }

    public function mail_vigencia($diasrestantes, $id, $fechacambioestado, $nombre, $parentesco, $libro) {
        load::lib("phpmailer/class.phpmailer");
        view::template(NULL);
        $usuario = new Usuarios();
        $usuario = $usuario->filtrar_por_id($id);
        $email = $usuario->email;
        try {
            ////Mandar mail
            $mail = new PHPMailer();
            $mail->SetLanguage('en', '/phpmailer/language/');
//Luego tenemos que iniciar la validación por SMTP:
            $mail->IsSMTP();
            $mail->SMTPDebug = false;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAutoTLS = false;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
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
            $link = '<a href="http://190.15.213.87:81">Aqui</a>';
            $mail->Subject = "Tu acta digital se esta por vencer"; // Este es el titulo del email.
            $body = "Estimado usuario el acta correspondiente a " . $nombre . " realizada el dia " . $fechacambioestado . " se esta por vencer le restan " . $diasrestantes . " dias. Para solicitarla nuevamente hace click " . $link;
            $mail->Body = $body; // Mensaje a enviar
            $exito = $mail->Send(); // Envía el correo.
//También podríamos agregar simples verificaciones para saber si se envió:
            if ($exito) {
                Flash::info("El correo de la vigencia fue enviado correctamente");
            } else {
                $mail->ErrorInfo;
                throw new NegocioExcepcion($mail->ErrorInfo);
            }
        } catch (NegocioExcepcion $e) {
            Logger::error($e->getMessage());
            Flash::info($e->getMessage());
        }
    }

}
