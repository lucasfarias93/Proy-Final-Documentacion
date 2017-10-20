<?php

Load::negocio("imagen/experto_estampa");

class ExpertoEstampaConsulta extends ExpertoEstampa {

    public function estampar_imagen($image) {
        Load::lib('wideimage/WideImage');
        
        $estampa = imagecreatefrompng(dirname(APP_PATH) . '/public/img/marca_agua.png');
	$background = imagecolorallocatealpha($estampa,0,0,0,127);

        // removing the black from the placeholder
        imagecolortransparent($estampa, $background);
        for ($y = 0; $y < imagesy($image); $y = $y + 200):
            for ($x = 0; $x < imagesx($image); $x = $x + 400):
                imagecopymerge($image, $estampa, $x, $y, 0, 0, imagesx($estampa), imagesy($estampa), 50) ;
            endfor;
        endfor;
//        $watermark = new Imagick(dirname(APP_PATH) .'/public/img/marca_agua.png');
//        for ($y = 0; $y < $image->getImageHeight(); $y = $y + 200):
//            for ($x = 0; $x < $image->getImageWidth(); $x = $x + 400):
//                $image->compositeImage($watermark, imagick::COMPOSITE_OVER, $x, $y);
//            endfor;
//        endfor;
    }

}

?>
