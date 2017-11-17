<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
load::model('codigoprovincial');

class ExpertoActas {

    public static function generar_pdf($imagenes) {
        $clave_key = 'registro';
        //verificamos que tenga permisos para imprimir
//        echo $imagenes; 
//        $imagenes = json_decode(stripslashes($imagenes));
//        $acta = ExpertoActas::buscar_acta_segun_imagen_id($imagenes[0]);
        Load::lib("fpdf");
        $pdf = new FPDF();
        $contador = 1;
//        foreach ($imagenes as $imagen):
        Logger::info($imagenes->uri);
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 15);
//            $enlace_imagen = Load::model("enlace_imagen")->find_first($imagen);
//            $tmp = str_replace("TIF", "", $enlace_imagen->nombre);
//            $ruta_temporal_original = Config::get("config.application.carpeta_temporal_original") . "/" . $tmp . "png";
        list($width_original, $height_original) = getimagesize($imagenes->uri);
//        $width_crop = $width_original;
//        $height_crop = $height_original;
//        $ruta_temporal_original_crop = Config::get("config.application.carpeta_temporal_original") . "/crop/" . $tmp . "png";

        $escalas = ExpertoActas::redimensionar($width_original, $height_original, 195, 276); //2480, 3508);//
        $x = $escalas["x_escalado"]; //$width_original*$porcentaje;//
        $y = $escalas["y_escalado"]; //$height_original*$porcentaje;//

        $pdf->Image($imagenes->uri, 10, 10, $x, $y, 'png');

        if ($contador < count($imagenes)) {
            $pdf->AddPage();
        } else {
            $pdf->AddPage();
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . PUBLIC_PATH . 'default/public/img/escudo_consulta.jpg', 10, 10, 190, 270);
            $pdf->SetFont('Arial', '', 15);
            /////////ubicar la fecha
            $pdf->Cell(25, 67);
            $pdf->Write(118, date("d/m/Y", time()));
            /////////Ubicar el importe
            $importe = 0;
            $codigos = new Codigoprovincial();
            $co = $codigos->obtener_codigos();
            $nombrecodigo;
            foreach ($co as $value) {
                $importe += floatval($value->importecodigo);
                $nombrecodigo .= $value->numerocodigoprovincial . " ";
            }
            $pdf->Text(25, 180, "$" . $importe . " correspondiente a los codigos provinciales " . $nombrecodigo);
            ////////Ubicar el cupon de pago
            $pdf->Text(125, 280, "cupon de pago: " . session::get('nrocupon'));
        }
        $contador += 1;
//        endforeach;

        $nombre = 'pdf/acta_' . date("dmYHis", time()) . '.pdf';
        $pdf->Output($nombre, 'F');
        ///// Comando para firmar digitalmente con jsignpdf
        $comando = "java -jar " . $_SERVER['DOCUMENT_ROOT'] . "/default/firma/jsignpdf/JSignPdf.jar " . $_SERVER['DOCUMENT_ROOT'] . "/default/public/" . $nombre . " -d " . $_SERVER['DOCUMENT_ROOT'] . "/default/public/pdf -kst BCPKCS12 -ksf " . $_SERVER['DOCUMENT_ROOT'] . "/default/firma/certificado.p12 -ksp " . $clave_key . " --bg-path " . $_SERVER['DOCUMENT_ROOT'] . "/default/firma/escudo.png --out-suffix '_firmado' --bg-scale 0.7 -fs 5 -a --l2-text 'Firmado Digitalmente por: \${signer} \${timestamp}' -urx 700 -ury 50 -lly 0 -llx 350 --page 1 -V";
        exec($comando);
        $url = PUBLIC_PATH . $nombre;
        $url = str_replace('.pdf', '', $url);
        $url = "/public" . $url . "_firmado.pdf";
        return $url;
    }

    public static function reporte_pdf_sol_gen($listado) {
        $clave_key = 'registro';
        Load::lib("fpdf");
        $pdf = new FPDF();
        $pdf->AddPage('L');
        $pdf->Image($_SERVER['DOCUMENT_ROOT'] .'/default/public/img/reporte.jpg',250,10,30);
        $pdf->SetFont('Arial', '', 24);
        $pdf->Cell(10, 15, 'Solicitudes generadas           '.UtilApp::fecha_actual(), 0,1);
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        // Header
        $pdf->SetFillColor(160, 180, 230);
        $pdf->Cell(20, 10, 'Solicitud', 1,0, 'L',TRUE);
        $pdf->Cell(80, 10, 'Propietario', 1,0, 'L',TRUE);
        $pdf->Cell(25, 10, 'Parentesco', 1,0, 'L',TRUE);
        $pdf->Cell(35, 10,utf8_decode('Cupón de pago'), 1,0, 'L',TRUE);
        $pdf->Cell(25, 10, 'Tipo libro', 1,0, 'L',TRUE);
        $pdf->Cell(35, 10, 'Fecha solicitud', 1,0, 'L',TRUE);
        $pdf->Cell(35, 10, 'Estado solicitud', 1, 1, 'L',TRUE);
        $pdf->SetFont('Arial', '', 10);
        // Data
        foreach ($listado as $row) {
            $pdf->Cell(20, 10, $row->id, 1);
            $pdf->Cell(80, 10, utf8_decode($row->nombrepropietarioacta), 1);
            $pdf->Cell(25, 10, $row->nombreparentesco, 1);
            $pdf->Cell(35, 10, $row->codigodepago, 1);
            $pdf->Cell(25, 10, $row->nombrelibro, 1);
            $pdf->Cell(35, 10, UtilApp::formatea_fecha_bd_to_pantalla($row->fechacambioestado), 1);
            $pdf->Cell(35, 10, $row->nombreestadosolicitud, 1, 1);
        }
        $nombre = 'pdf/reporte_solicitudes_' . date("dmYHis", time()) . '.pdf';
        $pdf->Output($nombre, 'F');
        ///// Comando para firmar digitalmente con jsignpdf
        $comando = "java -jar " . $_SERVER['DOCUMENT_ROOT'] . "/default/firma/jsignpdf/JSignPdf.jar " . $_SERVER['DOCUMENT_ROOT'] . "/default/public/" . $nombre . " -d " . $_SERVER['DOCUMENT_ROOT'] . "/default/public/pdf -kst BCPKCS12 -ksf " . $_SERVER['DOCUMENT_ROOT'] . "/default/firma/certificado.p12 -ksp " . $clave_key . " --bg-path " . $_SERVER['DOCUMENT_ROOT'] . "/default/firma/escudo.png --out-suffix '_firmado' --bg-scale 0.7 -fs 5 -a --l2-text 'Firmado Digitalmente por: \${signer} \${timestamp}' -urx 700 -ury 50 -lly 0 -llx 350 --page 1 -V";
        exec($comando);
        $url = PUBLIC_PATH . $nombre;
        $url = str_replace('.pdf', '', $url);
        $url = "/public" . $url . "_firmado.pdf";
        return $url;
    }

    public static function enviar_mail($url) {
        load::lib("phpmailer/class.phpmailer");
        view::template(NULL);
        try {
            $email = Auth::get('email');
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
            $mail->FromName = "Gestion Digital de Actas";
//Estas dos líneas, cumplirían la función de encabezado (En mail() usado de esta forma: “From: Nombre <correo@dominio.com>”) de //correo.
            $mail->AddAddress($email); // Esta es la dirección a donde enviamos
            //$mail->AddAddress("dggomez@mendoza.gov.ar"); // Esta es la dirección a donde enviamos
            $mail->IsHTML(true); // El correo se envía como HTML
            $mail->Subject = "Solicitud de partida"; // Este es el titulo del email.
            $body = "Ya tenés tu partida disponible para usar por 6 meses a partir del " . UtilApp::fecha_actual();
            $mail->Body = $body; // Mensaje a enviar
            $mail->AddAttachment($url);
            $exito = $mail->Send(); // Envía el correo.
//También podríamos agregar simples verificaciones para saber si se envió:
            if ($exito) {
                Flash::info("El correo de la firma fue enviado correctamente");
                input::delete();
            } else {
                $mail->ErrorInfo;
                throw new NegocioExcepcion($mail->ErrorInfo);
                input::delete();
            }
        } catch (NegocioExcepcion $e) {
            Logger::error($e->getMessage());
            Flash::info($e->getMessage());
        }
    }

    public static function buscar_acta_segun_imagen_id($imagen_id) {
        //busco datos del acta
        $enlaces_acta = Load::model("enlace_acta_imagen")->find("enlace_imagen_id = $imagen_id");
        if (count($enlaces_acta) > 0)
            return Load::model("acta_acta")->find_first($enlaces_acta[0]->acta_acta_id);
        return null;
    }

    public static function redimensionar($dim_x, $dim_y, $ancho, $alto) {
        if ($dim_y) { //Para asegurarnos de que dim[1] es diferente de cero
            $cociente = $dim_x / $dim_y;
        }
        if ($alto) {//Para asegurarnos de que alto es diferente de cero
            $coc_max = $ancho / $alto;
        }

        if (($dim_x <= $ancho) && ($dim_y <= $alto)) {
            /* En este caso no pasa nada y 
              la imagen se imprime con su tamaño original */
            $ancho = $dim_x;
            $alto = $dim_y;
            //Flash::info("entra en 1");
        } else {
            if ($cociente >= $coc_max) {
                /* En este caso el factor más restrictivo
                  va a ser el ancho de la foto */
                $alto = $ancho / $cociente;
                //Flash::info("entra 2");
                //Flash::info("cociente    ". $cociente);
            } else {
                /* En este caso el factor restrictivo 
                  va a ser la altura de la foto */
                $ancho = $alto * $cociente;
                //Flash::info("entra en 3");
            }
        }
        //Flash::info($ancho."   alto   ", $alto);
        $img["x_escalado"] = $ancho;
        $img["y_escalado"] = $alto;
        return $img;
    }

}
