<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ExpertoActas {

    public static function generar_pdf($imagenes) {
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
        $pdf->Cell(40, 20);
//            $enlace_imagen = Load::model("enlace_imagen")->find_first($imagen);
//            $tmp = str_replace("TIF", "", $enlace_imagen->nombre);
//            $ruta_temporal_original = Config::get("config.application.carpeta_temporal_original") . "/" . $tmp . "png";
        list($width_original, $height_original) = getimagesize($imagenes->uri);
        $width_crop = $width_original;
        $height_crop = $height_original;
        $ruta_temporal_original_crop = Config::get("config.application.carpeta_temporal_original") . "/crop/" . $tmp . "png";

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
            $pdf->Cell(25, 67);
            $pdf->Write(118, date("d/m/Y", time()));
        }
        $contador += 1;
//        endforeach;

        $nombre = 'pdf/acta_' . date("dmY", time()) . '.pdf';
        $pdf->Output($nombre, 'F');
        $url = PUBLIC_PATH . $nombre;
        $json = '{"codigo":"1","mensaje":"' . $url . '" }';
        return $json;
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
