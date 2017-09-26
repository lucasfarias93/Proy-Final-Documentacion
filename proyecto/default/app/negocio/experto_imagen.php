<?php

/**
 * Este experto realiza todas las operaciones con las imagenes fisicas y todo lo concerniente a ellas (rutas , uris, etc )
 */
class ExpertoImagen {

    /**
     * Permite verificar que la ruta que se intenta acceder exista, en caso contrario, se crea
     * 
     * @param type $ruta
     */
    public static function verificar_ruta($ruta) {
        try {
            $old = umask(0);
            if (!file_exists($ruta)) {//existe el archivo y es una ruta a un directorio?
                Logger::info("File not exists: $ruta");
//                if (stripos($ruta, ".TIF") !== FALSE) { //es una ruta a un archivo?
                    if (!is_file($ruta)) {
                        $salida = shell_exec("mkdir $ruta -p");
                        Logger::debug("Creando directorio: mkdir $ruta -p -> output: $salida");
                        if (!chmod($ruta, 0777)) {
                            $salida = shell_exec("chmod 777 $ruta -R");
                            Logger::debug("chmod 777 $ruta -R -> output: $salida");
                        } else {
                            Logger::debug("Permisos correctamente otorgados, recurso inexistente");
                        }
                    } else {
                        Logger::debug("La ruta no es un directorio: $ruta");
                    }
//                }
            }
            if (!is_file($ruta)) {
                $permisos = fileperms($ruta);
                switch ($permisos & 0xF000) {
                    case 0x4000: // Directorio
                        if ($permisos != 16895) {// binario 0100000111111111 -> D777
                            if (!chmod($ruta, 0777)) {
                                $salida = shell_exec("chmod 777 $ruta -R");
                                $permisos_nuevos = fileperms($ruta);
                                Logger::debug("Permisos viejos $permisos, nuevos $permisos_nuevos: chmod 777 $ruta -R -> output: $salida");
                            } else {
                                Logger::debug("Permisos correctamente otorgados, recurso ya existente sin permisos");
                            }
                        }
                        break;
                    default: // Desconocido
                        Logger::error("archivo de tipo desconocido: $permisos");
                }
            }
        } catch (NegocioExcepcion $ex) {
            Logger::error("NO se pudo crear el directorio $ruta, error: " . $ex->getMessage());
        }

//        try {
//            $old = umask(0);
//            if (stripos($ruta, ".") !== FALSE ) { //es una ruta a un archivo?
//                $posRuta = strrpos($ruta, "/");
//                $ruta = substr($ruta, 0, $posRuta);
////                Logger::debug("$ruta");
//                if (!file_exists($ruta)) {
//                    $salida = shell_exec("mkdir $ruta -p");
//                    $salida = shell_exec("chmod 777 $ruta -R");
//                }
//            }else{
//                if(!file_exists($ruta) && !is_file($ruta)){
//                    $permisos = fileperms($ruta);
//                    switch ($permisos & 0xF000) {
//                        case 0x4000: // Directorio
//                            if($permisos != 16895){// binario 0100000111111111 -> D777
//                                if( !chmod($ruta, 0777) ) {
//                                    $salida = shell_exec("chmod 777 $ruta -R");
//                                    $permisos_nuevos = fileperms($ruta);
//                                    Logger::debug("Permisos viejos $permisos, nuevos $permisos_nuevos: chmod 777 $ruta -R -> output: $salida");
//                                }else{
//                                    Logger::debug("Permisos correctamente otorgados, recurso ya existente sin permisos");
//                                }
//
//                            }
//                            break;
//                        default: // Desconocido
//                            Logger::error("archivo de tipo desconocido: $permisos");
//                    }
//                }
//            }
//        } catch (NegocioExcepcion $ex) {
//            Logger::error("NO se pudo crear el directorio $ruta, error: " . $ex->getMessage());
//        }
    }

    public static function buscar_imagen_por_id($imagen_id) {
        return Load::model("enlace_imagen")->find_first($imagen_id);
    }

    public static function buscar_imagen_por_imagen_id($imagen_id) {
        $imagen = ExpertoImagen::buscar_imagen_por_id($imagen_id);
        $ruta = ExpertoImagen::obtener_ruta_completa(str_replace("-", "/", $imagen->ubicacion));
        $uri = "$ruta/$imagen->nombre";
//        echo $uri;
        $img = ExpertoImagen::convertir_imagen($uri, ESTAMPA_CONSULTA);
        return $img->imagen;
    }

    /**
     * Busca las imagenes que existen en un determinado directorio, tomando como parametro el id del libro
     * @param type $libro_id
     * @return type
     */
    public static function buscar_imagenes_directorio($libro_id) {
        $ruta = ExpertoEnlace::obtener_ruta_directorio_segun_libro_id($libro_id);
        //ExpertoImagen::verificar_ruta($ruta);
        //Flash::info("la ruta es ....".$ruta);
        $ruta_opc = substr($ruta, 0, -6);
        //Flash::info("la ruta es ....".$ruta_opc);
//        $salida = shell_exec("chmod 777 '$ruta' ");
//        echo $salida;
        $imagenes = scandir($ruta, SCANDIR_SORT_ASCENDING);
        return $imagenes;
    }

    /**
     * Permite encontrar una imagen segurn la uri del archivo y ademas tranforma la imagen a base64
     * @param type $uri
     * @return \DTO
     */
    public static function buscar_datos_imagen_por_uri($uri) {
        $type = pathinfo($uri, PATHINFO_EXTENSION);
        $data = file_get_contents($uri);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $imagen = imagecreatefrompng($uri);
        $dto = new stdClass();
        $dto->imagen = $base64;
        $dto->x = imagesx($imagen);
        $dto->y = imagesy($imagen);
        return $dto;
    }

    public static function buscar_imagen_ficticia_visualizar() {
        $ret = array();
        $ruta = dirname(APP_PATH) . '/public/img/no_disponible.gif';
        $type = pathinfo($ruta, PATHINFO_EXTENSION);
        $data = file_get_contents($ruta);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $dto = new stdClass();
        $dto->imagen = $base64;
        $dto->id = "";
        $dto->x = 400;
        $dto->y = 400;
        $ret[] = $dto;
        return $dto;
    }

    public static function buscar_imagen_ficticia() {
        $ret = array();
        $ruta = dirname(APP_PATH) . '/public/img/no_disponible.gif';
        $type = pathinfo($ruta, PATHINFO_EXTENSION);
        $data = file_get_contents($ruta);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $dto = new stdClass();
        $dto->imagen = $base64;
        $dto->id = "";
        $dto->x = 400;
        $dto->y = 400;
        $ret[] = $dto;
        return $ret;
    }

    public static function crop($imagen_id, $pWidth, $pHeight, $selectorX, $selectorY, $selectorH, $selectorW, $viewPortH, $viewPortW) {
        $ext = "png";
        $enlace_imagen = Load::model("enlace_imagen")->find_first($imagen_id);
        $tmp = str_replace("TIF", "", $enlace_imagen->nombre);
        $ruta_temporal_original = Config::get("config.application.carpeta_temporal_original") . "/" . $tmp . $ext;
        //echo "original ".$ruta_temporal_original;
        $ruta_temporal_crop_original = Config::get("config.application.carpeta_temporal_original") . "/crop/" . $tmp . $ext;
        $ruta_temporal_crop_estampa = Config::get("config.application.carpeta_temporal_estampa") . "/crop/" . $tmp . $ext;
        $data = file_get_contents($ruta_temporal_original);
        $image = imagecreatefromstring($data);

        $width = imagesx($image);
        $height = imagesy($image);
        if ($width > $height) {
            $porcentaje = (800) / $width;
        } else {
            $porcentaje = (1200) / $height;
            if ($porcentaje * $width > 800) {
                $porcentaje = (800) / $width;
            }
        }
        if (!$height) {
            $height = 1200;
            $width = 800;
            $porcentaje = 1;
        }
        // $porcentaje = 800/$width;
        $selectorW = $selectorW / $porcentaje;
        $selectorH = $selectorH / $porcentaje;
        $selectorX = $selectorX / $porcentaje;
        $selectorY = $selectorY / $porcentaje;
        $comando = "convert $ruta_temporal_original -crop {$selectorW}x{$selectorH}+{$selectorX}+{$selectorY} +repage $ruta_temporal_crop_original";

        shell_exec($comando);
        Load::negocio("imagen/fabrica_estampa");
        $data = file_get_contents($ruta_temporal_crop_original);
        $imagenes[$imagen_id] = "";
        $selector = imagecreatefromstring($data);
        $experto = FabricaEstampa::get_experto(ESTAMPA_CONSULTA);
        $experto->estampar_imagen($selector);


        imagepng($selector, $ruta_temporal_crop_estampa);

        ob_start();
        imagepng($selector);
        $contents = ob_get_contents();
        ob_end_clean();

        echo 'data:image/png;base64,' . base64_encode($contents);
        View::template(NULL);
        imagedestroy($selector);
    }

    public static function crop_enlace($libro_id, $archivo, $pWidth, $pHeight, $selectorX, $selectorY, $selectorH, $selectorW, $viewPortH, $viewPortW, $rotate) {
        $ext = "png";
        $libro = Load::model("base_libro")->find_first($libro_id);
        //$tmp = str_replace("TIF", "", $enlace_imagen->nombre);
        $tipo_libro = Load::model("base_tipo_libro")->find_first($libro->base_tipo_libro_id);
        $ubic = $tipo_libro->tipo . "/" . $libro->anio . "/" . $libro->numero;
        $ruta_temporal_original = Config::get("config.application.ruta_imagenes") . "/" . $ubic . "/" . $archivo;
        $tmp = str_replace("TIF", "", $archivo);
        $tmp = str_replace("tif", "", $tmp);
        $ruta_temporal_png = Config::get("config.application.carpeta_temporal_original") . "/" . $tmp . $ext;
        //echo "original ".$ruta_temporal_original;
        //$ruta_temporal_crop_original = Config::get("config.application.carpeta_temporal_original") . "/crop/" . $tmp . $ext;
        //$ruta_temporal_crop_estampa = Config::get("config.application.carpeta_temporal_estampa") . "/crop/" . $tmp . $ext;
        //echo "ruta png ".$ruta_temporal_png;
        $data = file_get_contents($ruta_temporal_png);
        $image = imagecreatefromstring($data);

        $width = imagesx($image);
        $height = imagesy($image);
        //echo "altura ".$height." - ".$width;
        if ($width > $height) {
            $porcentaje = (800) / $width;
        } else {
            $porcentaje = (1200) / $height;
            if ($porcentaje * $width > 800) {
                $porcentaje = (800) / $width;
            }
        }
        if (!$height) {
            $height = 1200;
            $width = 800;
            $porcentaje = 1;
        }
        // $porcentaje = 800/$width;
        //$porcentaje = 1;
        $selectorW = $selectorW / $porcentaje;
        $selectorH = $selectorH / $porcentaje;
        $selectorX = $selectorX / $porcentaje;
        $selectorY = $selectorY / $porcentaje;
        if ($rotate != '' && $rotate > 0) {
            $comando = "convert $ruta_temporal_original -crop {$selectorW}x{$selectorH}+{$selectorX}+{$selectorY} -rotate $rotate +repage $ruta_temporal_original";
        } else {
            $comando = "convert $ruta_temporal_original -crop {$selectorW}x{$selectorH}+{$selectorX}+{$selectorY} +repage $ruta_temporal_original";
        }

        //echo $comando;
        shell_exec($comando);
        //Load::negocio("imagen/fabrica_estampa");
        //$data = file_get_contents($ruta_temporal_original);
        //$imagenes[$imagen_id] = "";
        //$selector = imagecreatefromstring($data);
        //$experto = FabricaEstampa::get_experto(ESTAMPA_CONSULTA);
        //$experto->estampar_imagen($selector);
        /*

          imagepng($selector, $ruta_temporal_original);

          ob_start();
          imagepng($selector);
          $contents = ob_get_contents();
          ob_end_clean();

          echo 'data:image/png;base64,' . base64_encode($contents);
          View::template(NULL);
          imagedestroy($selector); */
    }

    /**
     * obtiene la ruta especificada en las propiedades, en caso de que sea para obtener una ruta principal
     * solo devuelve la definida en ruta_imagenes, en otro caso, busca otro intento en la ruta especificada en ruta_imagenes_6
     * @param type $ubicacion
     * @param type $principal
     * @return string
     */
    public static function obtener_ruta_completa($ubicacion, $principal = FALSE) {
        $ruta = Config::get("config.application.ruta_imagenes") . $ubicacion;
        if ($principal) {
            ExpertoImagen::verificar_ruta($ruta);
            return $ruta;
        }
        if (!file_exists($ruta)) {
            ExpertoImagen::verificar_ruta($ruta);
            $ruta = Config::get("config.application.ruta_imagenes_6") . $ubicacion;
            if (!file_exists($ruta)) {
                ExpertoImagen::verificar_ruta($ruta);
                return $ruta;
            }
        }
        ExpertoImagen::verificar_ruta($ruta);
        return $ruta;
    }

    /**
     * convierte la imagen tif desde la uri a una imagen png especificada por los archivos de configuracion
     * @param type $uri
     * @param type $estampar_marca_agua si es necesario que se le aplique alguna marca de agua
     * @return type DTO  con la imagen mas propiedades x e y de la imagen convertida
     */
    public static function convertir_imagen($uri, $estampar_marca_agua) {
        $nombre = @end(explode("/", $uri));
        $nombre_nuevo = explode(".", $nombre);
        $nombre_nuevo = $nombre_nuevo[0];
        $ruta_temporal_original = Config::get("config.application.carpeta_temporal_original") . $nombre_nuevo . ".png";
//        echo "convert --resize 1200x800 '$uri' '$ruta_temporal_original'";
        exec("convert  -size 1200x800 '$uri' '$ruta_temporal_original'");
//        $imagick = new \Imagick($uri);
//        $imagick->resizeImage(1200, 800, Imagick::FILTER_LANCZOS);
//        $imagick->writeimage($ruta_temporal_original);
//            echo "creando estampa1";
        //colocar marca de agua para mostrar en pantalla
        if ($estampar_marca_agua !== ESTAMPA_NINGUNA) {
//            echo "creando estampa";
            $ruta_temporal_estampa = Config::get("config.application.carpeta_temporal_estampa") . $nombre_nuevo . ".png";
            if (file_exists($ruta_temporal_original)) {
                $imagen = imagecreatefrompng($ruta_temporal_original);
                Load::negocio("imagen/fabrica_estampa");
                $experto = FabricaEstampa::get_experto($estampar_marca_agua);
                $experto->estampar_imagen($imagen);
//                $experto->estampar_imagen($imagick);
                imagepng($imagen, $ruta_temporal_estampa);
                $ruta_temporal_original = $ruta_temporal_estampa;
                imagedestroy($imagen);
            }
        }
        if (file_exists($ruta_temporal_original)) {
            return ExpertoImagen::buscar_datos_imagen_por_uri($ruta_temporal_original);
        } else {
//            echo "aca";
            return ExpertoImagen::buscar_imagen_ficticia_visualizar();
        }
    }

    /**
     * Coloca la imagen subida por el usuario al directorio correspondiente al libro, 
     * devuelve la imagen en base64
     * @param type $nombre_imagen
     * @param type $libro_id
     * @return type
     */
    public static function subir_imagen($nombre_imagen, $libro_id) {
        for ($i = 0; $i < count($_FILES[$nombre_imagen]['name']); $i++) {
            $nombre_nuevo = str_replace(" ", "_", $_FILES[$nombre_imagen]['name'][$i]);
            $nombres[] = $nombre_nuevo;
        }
        $_FILES[$nombre_imagen]['name'] = $nombres;
        $ruta = ExpertoEnlace::obtener_ruta_directorio_segun_libro_id($libro_id);
        $ruta_temporal = $_SERVER['DOCUMENT_ROOT'] . PUBLIC_PATH . 'default/public/files/actas/';
        $uri = "$ruta/$nombre_nuevo";
        $libro = Load::model("base_libro")->find_first($libro_id);
        if (Auth::get("id") != 3 || !ExpertoSeguridad::verificar_permiso(PERMISO_AUDITOR_LIBRO)) {
            if ($libro->base_oficina_id != Auth::get("base_oficina_id")) {
                Flash::warning("El libro no pertenece a su oficina. Verifique que los datos sean correctos");
                throw new NegocioExcepcion("El libro no pertenece a su oficina. Verifique que los datos sean correctos");
            }
        }
        $img = Upload::factory($nombre_imagen, 'image');
        //$img->setPath($ruta);
        $img->setPath($ruta_temporal);
        if (!$img->isUploaded()) {
            Flash::error('La imagen ' . $nombre_nuevo . " ya esta subida");
            throw new NegocioExcepcion('La imagen ' . $nombre_nuevo . " ya esta subida");
        }
        if ($libro->base_tipo_libro_id == LIBRO_TIPO_PERDIDA_PATRIA_POTESTAD || $libro->base_tipo_libro_id == LIBRO_TIPO_PRESCRIPCIONES || $libro->base_tipo_libro_id == LIBRO_TIPO_REHABILITACION || $libro->base_tipo_libro_id == LIBRO_TIPO_CANCELACIONES || $libro->base_tipo_libro_id == LIBRO_TIPO_INCAPACIDAD
        ) {
            $img->setMaxSize(10485760); // 10*1024*1024 = 10MB
        } else {
            $img->setMaxSize(2097152); // 2*1024*1024 = 2MB
        }
        $img->setExtensions(IMAGEN_EXTENSIONES);
        $img->overwrite(True);
        // Guarda la imagen
        if ($img->save()) {
            $comando = "mv '$ruta_temporal/$nombre_nuevo' '$uri'";
            $salida = shell_exec($comando);
            return $nombre_nuevo;
        } else {
            $comando = "mv '$ruta_temporal/$nombre_nuevo' '$uri'";
            Logger::error("No se ha guardado la imagen, comando: " . $comando);
            throw new NegocioExcepcion("No se ha guardado la imagen");
        }
        return $nombre_nuevo;
    }

    public static function subir_imagen_enlace($nombre_imagen, $libro_id) {
        $nombre_nuevo = str_replace(" ", "_", $_FILES[$nombre_imagen]['name']);
        $_FILES[$nombre_imagen]['name'] = $nombre_nuevo;
        $ruta = ExpertoEnlace::obtener_ruta_directorio_segun_libro_id($libro_id);
        $ruta_temporal = $_SERVER['DOCUMENT_ROOT'] . PUBLIC_PATH . 'default/public/files/actas/';
        $uri = "$ruta/$nombre_nuevo";
        $libro = Load::model("base_libro")->find_first($libro_id);
        if (Auth::get("id") != 3 || !ExpertoSeguridad::verificar_permiso(PERMISO_AUDITOR_LIBRO)) {
            if ($libro->base_oficina_id != Auth::get("base_oficina_id")) {
                //Flash::error("No puede subir una imagen, debe pertenecer a la oficina");
                Flash::warning("El libro no pertenece a su oficina. Verifique que los datos sean correctos");
            }
        }
        $imagick_type = new Imagick();

        $img = Upload::factory($nombre_imagen, 'image');
        //$img->setPath($ruta);
        //echo " la ruta es ".$ruta_temporal."  uri  ".$uri;
        $img->setPath($ruta_temporal);
        if (!$img->isUploaded()) {
            Flash::error('La imagen ' . $nombre_nuevo . "ya esta subida");
        }
        if ($libro->base_tipo_libro_id == LIBRO_TIPO_PERDIDA_PATRIA_POTESTAD || $libro->base_tipo_libro_id == LIBRO_TIPO_PRESCRIPCIONES || $libro->base_tipo_libro_id == LIBRO_TIPO_REHABILITACION || $libro->base_tipo_libro_id == LIBRO_TIPO_CANCELACIONES || $libro->base_tipo_libro_id == LIBRO_TIPO_INCAPACIDAD
        ) {
            $img->setMaxSize(10485760); // 10*1024*1024 = 10MB
        } else {
            $img->setMaxSize(2097152); // 2*1024*1024 = 2MB
        }
        $img->setExtensions(IMAGEN_EXTENSIONES);
        $img->overwrite(True);
        // Guarda la imagen
        if ($img->save()) {
            $comando = 'mv ' . $ruta_temporal . $nombre_nuevo . ' ' . $uri;
            $salida = shell_exec($comando);
            $file_handle_for_viewing_image_file = fopen($uri, 'a+');
            $imagick_type->readImageFile($file_handle_for_viewing_image_file);
            $image_type = $imagick_type->getImageType();
            //echo " el tipo de imagen es ".$image_type;
            if ($image_type != 1) {
                unlink($uri);
                throw new NegocioExcepcion("No se ha guardado la imagen. La imagen debe ser subida en blanco y negro");
            }
            return $nombre_nuevo;
        } else {
            $comando = 'mv ' . $ruta_temporal . $nombre_nuevo . ' ' . $uri;
            Logger::error("No se ha guardado la imagen, comando: " . $comando);
            throw new NegocioExcepcion("No se ha guardado la imagen");
        }
        return $nombre_nuevo;
    }

    public static function subir_imagen_generica($nombre_imagen) {
        for ($i = 0; $i < count($_FILES[$nombre_imagen]['name']); $i++) {
            $nombre_nuevo = str_replace(" ", "_", $_FILES[$nombre_imagen]['name'][$i]);
            $nombres = uniqid('imagen_');

            $_FILES[$nombre_imagen]['name'] = $nombres;
            $foto_type = $_FILES[$nombre_imagen]['type'];
            $ruta_temporal = $_SERVER['DOCUMENT_ROOT'] . PUBLIC_PATH . 'default/public/files/actas/';
            $uri = "$ruta_temporal$nombres";

            $img = Upload::factory($nombre_imagen, 'image');
            $img->setPath($ruta_temporal);
            if (!$img->isUploaded()) {
                Flash::error('La imagen ' . $nombres . " ya esta subida");
                throw new NegocioExcepcion('La imagen ' . $nombres . " ya esta subida");
            }
            $img->setMaxSize(2097152); // 2*1024*1024 = 2MB
            $img->overwrite(True);
            if ($img->save()) {
                Flash::info($nombres);
            } else {
                Logger::error("No se ha guardado la imagen, comando: ");
                throw new NegocioExcepcion("No se ha guardado la imagen");
            }
        }
        return $nombres;
    }

    /**
     * 
     * @param string $nombre_viejo
     * @param string $nombre_nuevo
     * @throws Exception
     */
    public static function renombrar($nombre_viejo, $nombre_nuevo) {
//        echo "renombrar: $nombre_viejo , $nombre_nuevo";
        if (!file_exists($nombre_viejo)) {
            Logger::critical("Error al intentar renombrar la imagen , nombre viejo: $nombre_viejo");
            throw new NegocioExcepcion("No se pudo renombrar, no existe archivo de origen", "100");
        }
        if (!rename($nombre_viejo, $nombre_nuevo)) {
            Logger::debug("No se pudo renombrar nombre viejo: $nombre_viejo , nombre nuevo : $nombre_nuevo, intentando 2da opcion");
            $salida = shell_exec("cp '$nombre_viejo' '$nombre_nuevo' ");
            if (!file_exists($nombre_nuevo)) {
                throw new NegocioExcepcion("NO SE HA PODIDO RENOMBRAR LA IMAGEN", "100");
            }
        }
    }

    /* Función
      Toma la ruta de una imagen, un valor máximo
      de ancho y otro máximo de alto. Si la imagen
      rebasa dichas medidas, calcula las medidas
      máximas que podría tener manteniendo el
      formato original para no salirse de las medidas
      indicadas.
      Finalmente la función imprime la imagen.
     */

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

    /**
     * buscar enlaces segun acta id
     * @param type $acta_id
     * @return type
     */
    public static function buscar_informacion_respaldatoria_por_acta_id($acta_id) {
        return Load::model("enlace_imagen")->buscar_imagen_segun_acta_id($acta_id);
    }

    public static function buscar_informacion_respaldatoria_por_id($acta_id) {
        return Load::model("enlace_imagen")->find_first($acta_id);
    }

}
