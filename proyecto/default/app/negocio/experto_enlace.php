<?php

Load::models("enlace_acta_imagen", "enlace_imagen", "base_estado");
Load::negocio("etl/experto_etl");
Load::negocio("experto_seguridad");
Load::negocio("experto_libros");
Load::negocio("experto_actas");
Load::negocio("experto_imagen");

class ExpertoEnlace {

    /**
     * Busca las imagenes que existen en un determinado directorio, tomando como parametro el id del libro
     * @param int $libro_id
     * @return type
     */
    public static function buscar_imagenes_directorio($libro_id) {
        $imagenes = ExpertoImagen::buscar_imagenes_directorio($libro_id);
        $filtros = array();
        $filtros[] = ".";
        $filtros[] = "..";
        foreach ($imagenes as $imagen) {
            $ext = explode(".", $imagen);
            if (count($ext) == 1) {
                $filtros[] = $imagen;
            } else {
                if (strpos($ext[1], IMAGEN_EXTENSIONES)) {
                    $filtros[] = $imagen;
                }
            }
        }
        if (!ExpertoSeguridad::verificar_permiso(PERMISO_ENLACE_MASIVO)) {
            $enlazadas = ExpertoEnlace::buscar_imagenes_enlazadas_por_id_libro($libro_id);
            if (count($enlazadas) > 0) {
                $filtros = array_merge($filtros, $enlazadas);
            }
        }
        $tmp = array_diff($imagenes, $filtros);
        $ret = array();
        foreach ($tmp as $t) {
            $ret[$t] = $t;
        }
        return $ret;
    }

    public static function buscar_imagenes_directorio_nombre($libro_id, $nombre) {
        $imagenes = ExpertoImagen::buscar_imagenes_directorio($libro_id);
        $filtros = array();
        $filtros[] = ".";
        $filtros[] = "..";
        foreach ($imagenes as $imagen) {
            $ext = explode(".", $imagen);
            if (count($ext) == 1) {
                $filtros[] = $imagen;
            } else {
                if (strpos($ext[1], IMAGEN_EXTENSIONES)) {
                    $filtros[] = $imagen;
                }
            }
        }
        if (!ExpertoSeguridad::verificar_permiso(PERMISO_ENLACE_MASIVO)) {
            $enlazadas = ExpertoEnlace::buscar_imagenes_enlazadas_por_id_libro($libro_id);
            if (count($enlazadas) > 0) {
                $filtros = array_merge($filtros, $enlazadas);
            }
        }
        $tmp = array_diff($imagenes, $filtros);
        $ret = array();
        //foreach ($tmp as $t) {
        $ret[$nombre] = $nombre;
        //}
        return $ret;
    }

    /**
     * busca las imagenes que se encuentran enlazadas de un determinado libro
     * @param type $libro_id
     * @return type
     */
    public static function buscar_imagenes_enlazadas_por_id_libro($libro_id) {
        $libro = ExpertoLibros::buscar_libro_segun_id($libro_id);
        $tipo = $libro->getBaseTipoLibro()->tipo;
        $nombre = "$tipo$libro->anio$libro->numero%";
        return Load::model("enlace_imagen")->find("nombre ilike '$nombre'");
    }

    /**
     * permite ubicar la imagen de un libro segun el id del acta
     * @param type $acta_id
     * @return type
     */
    public static function buscar_imagenes_por_acta_id($acta) {
        if(is_numeric($acta)){
            $acta = ExpertoActas::buscar_acta_por_id($acta);
        }
        if (isset($acta->id)) {
            return ExpertoEnlace::buscar_imagenes($acta);
        }
        return NULL;
    }

    public static function buscar_imagenes_por_acta($acta) {
        $imagenes = $acta->getImagenes();
        $ret = array();
        // $filtros[] = ".";
        // $filtros[] = "..";

        foreach ($imagenes as $imagen) {
            $ext = explode(".", $imagen);
            $ret[$imagen->nombre] = $imagen->nombre;
        }


        return $ret;
    }

    public static function buscar_imagen_por_libro_nombre($libro_id, $nombre) {
        $ruta = ExpertoEnlace::obtener_ruta_directorio_segun_libro_id($libro_id);
        $uri = "$ruta/$nombre";
        $dto = ExpertoImagen::convertir_imagen($uri, ESTAMPA_NINGUNA);
        $imagen = Load::model("enlace_imagen")->find_first("nombre = '$nombre'");
        $dto->enlazada = false;
        $dto->id = 0;
        if (isset($imagen->id)) {
            $actas = ExpertoActas::buscar_actas_segun_imagen_id($imagen->id);
            $dto->id = $imagen->id;
            $dto->enlazada = true;
            $dto->actas = json_encode($actas);
        }
        return $dto;
    }

    public static function buscar_imagenes_por_libro_id_y_numero_acta($libro_id, $acta_numero) {
        $acta = ExpertoActas::buscar_acta_por_libro_id_numero($libro_id, $acta_numero);
        if (isset($acta->id)) {
            return ExpertoEnlace::buscar_imagenes($acta, ESTAMPA_NINGUNA);
        }
        return NULL;
    }
    public static function buscar_imagen_id_por_acta($acta) {
        if(is_numeric($acta)){
            $acta = Load::model("acta_acta")->find_first($acta);
        }
        $imagenes = Load::model("enlace_imagen")->buscar_imagen_segun_acta_id($acta->id,ESTADO_ENLACE_IMAGEN_NORMAL);
       foreach ($imagenes as $enlace_imagen) {
            return $enlace_imagen->id;
        }
        return NULL;
    }
	
	public static function buscar_imagen_nombre_por_acta($acta) {
        if(is_numeric($acta)){
            $acta = Load::model("acta_acta")->find_first($acta);
        }
        $imagenes = Load::model("enlace_imagen")->buscar_imagen_segun_acta_id($acta->id,ESTADO_ENLACE_IMAGEN_NORMAL);
//        var_dump($imagenes);
		$ret = array();
        foreach ($imagenes as $enlace_imagen) {
        	$dto = new stdClass();
            $dto->id = $enlace_imagen->id;
            $dto->nombre = $enlace_imagen->nombre;
        	$ret[] = $dto;
        }
        return ret;
    }

    /**
     * Metodo que se encarga de buscar una imagen de acuerdo al acta pasada como parametro
     * @param ActaActa $acta
     * @return String listado de imagenes que pertenecen al enlace
     */
    public static function buscar_imagenes($acta, $estampa = ESTAMPA_CONSULTA) {
        if(is_numeric($acta)){
            $acta = Load::model("acta_acta")->find_first($acta);
        }
        try {
            $imagenes = Load::model("enlace_imagen")->buscar_imagen_segun_acta_id($acta->id,ESTADO_ENLACE_IMAGEN_NORMAL);
            
//            $imagenes = $acta->getImagenes();
            $ret = array();
            if (count($imagenes) > 0) {
                foreach ($imagenes as $enlace_imagen) {
                    $ubicacion = str_replace("-", "/", $enlace_imagen->ubicacion);
                    $ubicacion = str_replace("Q:-ActasEscaneadas", "", $ubicacion);
                    $ext = "png";
                    $tmp = str_replace("TIF", "", $enlace_imagen->img_nombre);
                    $ruta_temporal_crop_original = Config::get("config.application.carpeta_temporal_original") . "/crop/" . $tmp . $ext;
                    @unlink($ruta_temporal_crop_original);
                    $ruta = ExpertoImagen::obtener_ruta_completa($ubicacion . "/$enlace_imagen->nombre");
                    if (!$ruta) {
                        Flash::info("No existe la imagen de acta");
                    }
                    $dto = ExpertoImagen::convertir_imagen($ruta, $estampa);
                    $dto->id = $enlace_imagen->id;
                    $ret[] = $dto;
                }
            } else {
                return array();
            }
        } catch (Exception $ex) {
            Logger::error("NO SE HA PODIDO CONVERTIR LA IMAGEN" . $ex->getMessage());
        }
        return $ret;
    }

    public function buscar_imagen_por_nombre($nombre, $estampa = ESTAMPA_CONSULTA) {
        $enlace_imagen = new EnlaceImagen();
        $enlace_imagen->buscar_imagen_por_nombre($nombre);
        $ruta = ExpertoImagen::obtener_ruta_completa(str_replace("-", "/", $enlace_imagen->ubicacion));

        if (!$ruta) {
            Flash::info("No existe la imagen de acta");
        }
        $uri = "$ruta/$enlace_imagen->nombre";
        $dto = ExpertoImagen::convertir_imagen($uri, $estampa);
        $dto->id = $enlace_imagen->id;
        if (isset($enlace_imagen->id)) {
            return $dto;
        } else {
            return NULL;
        }
    }

    /**
     * permite encontrar la ruta que corresponde segun el id del libro
     * @param type $libro_id
     * @return string
     */
    public static function obtener_ruta_directorio_segun_libro_id($libro) {
        if(is_numeric($libro)){
            $libro = ExpertoLibros::buscar_libro_segun_id($libro);
        }
        $tipoLibro = $libro->getBaseTipoLibro();
        $tipo = $tipoLibro->tipo;
        $ubicacion = "/$tipo/$libro->anio/$libro->numero";
        Logger::info("buscando ruta libro: $ubicacion");
        return ExpertoImagen::obtener_ruta_completa($ubicacion,TRUE);
    }
    
    /**
     * permite encontrar la ruta que corresponde segun el id del libro
     * @param type 
     * @return string
     */
    public static function obtener_ruta_directorio_segun_tramite_id($tramite) {
        if(is_numeric($tramite)){
            $tramite = ExpertoTramite::buscar_tramite_por_id($tramite);
        }
        $tipo_tramite = ExpertoTramite::buscar_tipo_tramite_por_id($tramite->tramite_tipo_tramite_id);
        $categoria = $tipo_tramite->getCategoria();
        $anio = date("Y");
        $ubicacion = "-tramites-$anio-$categoria->letra-$tramite->id";
        return ExpertoImagen::obtener_ruta_completa($ubicacion);
    }

    /**
     * Genera el nombre de imagen que debe tener, se debe llamar una vez que ya se ha actualizado la base de datos
     * @param type $acta_id
     * @param type $nom_imagen
     */
    public static function generar_nombre_imagen($libro, $nro, $nom_imagen) {
        if (!$nro && !$nom_imagen) {
            throw new Exception("No se han especificado los parametros correctamente", "100", "");
        }
        $tipo = $libro->getBaseTipoLibro()->tipo;
        $nombre_nuevo = "$tipo$libro->anio$libro->numero";

        $acta = ExpertoActas::buscar_acta_por_libro_id_numero($libro->id, $nro);
        $nro = str_pad($nro, 6, "0", STR_PAD_LEFT);
        $imagenes = $acta->getImagenes();
        //ver cantidad de enlaces por imagen
        $actas = ExpertoActas::buscar_actas_segun_imagen_nombre($nombre_nuevo . $nro . ".TIF");
        //ver que actas tiene enlazada esta imagen
        if (count($actas) == 0) {

            $nombre_nuevo .= $nro . ".TIF";
        } else {
            if (count($imagenes) > 0) {
                $nuevo = count($imagenes) + 1;
                $nro = "$nro#" . $nuevo;
            }
            foreach ($actas as $acta) {
                if ($acta->numero < $nro) {
                    $nombre_nuevo .= "$acta->numero-";
                } else {
                    $nombre_nuevo .= "$nro-";
                }
            }
            $nombre_nuevo = trim($nombre_nuevo, '-');
            $nombre_nuevo .= ".TIF";
        }
        return $nombre_nuevo;
    }

    /**
     * guarda el enlace de una acta sin necesidad de hacerlo con el tramite
     * @param array $acta
     * @param string $nombre_img
     * @param array $ciudadanos
     * @return type
     * @throws NegocioExcepcion
     * @throws Exception
     */
    public static function guardar_enlace($acta, $nombre_img, $ciudadanos) {
        try {
            if ($nombre_img == "") {
                throw new NegocioExcepcion("No se ha seleccionado la imagen", 999);
            }
            Load::model("enlace_imagen")->begin();
            if (!array_key_exists("base_libro_id", $acta)) {
                Logger::info("NO SE HA SELECCIONADO EL LIBRO A TRABAJAR");
                throw new Exception("NO SE HA SELECCIONADO EL LIBRO A TRABAJAR", "100");
            }
            if (array_key_exists("id", $acta) && $acta["id"] !== "") {
                $a = ExpertoActas::buscar_acta_por_id($acta["id"]);
                $imagenes = $a->getImagenes();
                if (count($imagenes) > 0) {
                    foreach ($imagenes as $imagen) {
                        if ($imagen->nombre == $nombre_img) {
                            throw new NegocioExcepcion("EL ACTA YA SE ENCUENTRA ENLAZADA", "100");
                        }
                    }
                }
            }
            $acta_acta = ExpertoActas::guardar_acta($acta);
            $padres = 0;
            $ciud_ppal_id = NULL;
            foreach ($ciudadanos as $rol => $ciudadano) {

                $ciud = ExpertoCiudadanos::guardar_ciudadano($ciudadano);
                $ciudadano["ciud_ciudadano_id"] = $ciud->id;
                //$documento = ExpertoCiudadanos::guardar_documento($ciudadano);
                ///$ciud->ciud_ciudadano_documento_id = $documento->id;
                if ($rol == ROL_PRINCIPAL || $rol == ROL_ESPOSO_CONTRAYENTE) {
                    $ciud_ppal = $ciud;
                }
                /* if (!$ciud->update()) {
                  new Exception("no se ha podido actualizar el ultimo documento", "1001");
                  } */
                if ($ciud_ppal->id) {
                    if ($ciud) {
                        $padres+=1;
                        //Flash::info("principal  ".$ciud_ppal->id."    relacion   ".$ciud->id."   acta id   ".$acta_acta->id);
                        $existe = Load::model('acta_acta_ciudadano')->existe_relacion($ciud_ppal->ciud_ciudadano_dato_id, $ciud->ciud_ciudadano_dato_id, $acta_acta->id);
                        if (!$existe && $ciud_ppal->id != $ciud->id) {
                        	if ($rol==7){
                        		$rol = 6;
                        	}
                            ExpertoActas::guardar_relacion_ciudadano($ciud_ppal->ciud_ciudadano_dato_id, $acta_acta->id, $rol, $ciud->ciud_ciudadano_dato_id);
                        }
                    }
                }
            }
            if ($padres == 0) {
                ExpertoActas::guardar_relacion_ciudadano($ciud_ppal->id, $acta_acta->id, ROL_PRINCIPAL, $ciud_ppal->nombre, $ciud_ppal->apellido, $ciud_ppal->base_sexo_id, NULL);
            }

            $libro = ExpertoLibros::buscar_libro_segun_id($acta["base_libro_id"]);
            $tipo = $libro->getBaseTipoLibro()->tipo;

            $nombre_original = "$tipo$libro->anio$libro->numero";
            $ubicacion = "-$tipo-$libro->anio-$libro->numero";

            $nombre_nuevo = ExpertoEnlace::generar_nombre_imagen($libro, $acta["numero"], $nombre_img);

            $imagen = new EnlaceImagen();
            $imagen->fecha_trx = new date();
            $imagen->nombre = $nombre_nuevo;
            $imagen->ubicacion = $ubicacion;
            if (!$imagen->save()) {
                throw new NegocioExcepcion("NO SE HA PODIDO INSERTAR EL REGISTRO DE IMAGEN", "100", "");
            }
            $ruta = ExpertoEnlace::obtener_ruta_directorio_segun_libro_id($libro->id);

			$acta_id = $acta_acta->id;
            $enlace_acta_imagen = new EnlaceActaImagen();
            $enlace_acta_imagen->enlace_imagen_id = $imagen->id;
            $enlace_acta_imagen->acta_acta_id = $acta_acta->id;
            $enlace_acta_imagen->base_estado_id = ESTADO_ENLACE_IMAGEN_NORMAL;
            $enlace_acta_imagen->usuarios_id = Auth::get("id");
            if (!$enlace_acta_imagen->save()) {
                throw new NegocioExcepcion("NO SE HA PODIDO INSERTAR EL REGISTRO DE IMAGEN", "100", "");
            }
            $uri_viejo = "$ruta/$nombre_img";
            $uri_nuevo = "$ruta/$nombre_nuevo";
            $ok = ExpertoImagen::renombrar($uri_viejo, $uri_nuevo);
            $existe_ruta = Session::has('ruta');

            if ($existe_ruta) {
                $ruta = Session::get('ruta');
                if ($ruta == 'actas/actas/ver_actas_caducadas') {
                    $usuario_id = Auth::get('id');
                    $oficina_id = Auth::get('base_oficina_id');

                    if (Session::has('marginal')) {
                        if (Session::get('marginal')) {
                            $marginal_id = Session::get('marginal');
                            $marginal = ExpertoActas::buscar_marginal_por_id($marginal_id);
                            $marginal->oficina_enlace_id = $oficina_id;
                            if (!$marginal->update()) {
                                throw new NegocioExcepcion("NO SE HA PODIDO REGISTRAR ENLACE ACTA CADUCADA", "100");
                            }
                            Session::set('marginal', NULL);
                        }
                    }

                    Load::negocio("incapacidad/experto_incapacidad");
                    if (ExpertoLibros::actualiza_libro_original()) {
                        $log = LOG_ENLAZAR_LIBRO_ORIGINAL;
                        if ($acta_acta->estado_incapacidad_id == 2) {
                            $acta_acta->estado_incapacidad_id = ESTADO_CAPACIDAD_FIRMA_ORIGINAL;
                        } else {
                            $acta_acta->estado_incapacidad_id = 1;
                        }
                    } else {
                        if ($acta_acta->estado_incapacidad_id == 2) {
                            $acta_acta->estado_incapacidad_id = ESTADO_CAPACIDAD_FIRMA_ORIGINAL;
                        } else {
                            $acta_acta->estado_incapacidad_id = 1;
                        }
                        $log = LOG_ENLAZAR_LIBRO_DUPLICADO;
                    }
                    if (!$acta_acta->update()) {
                        throw new NegocioExcepcion("NO SE HA PODIDO ACTUALIZAR EL ESTADO", "100", "");
                    }
                    ExpertoSeguridad::registrar_log($acta_acta->id, $log);
                }
            }
            Load::model("enlace_imagen")->commit();
			if (Session::has("pedido_id")){
				$pedido_id = Session::get("pedido_id");
				$pedido = Load::model('pedido/pedido')->find_first($pedido_id);
				$pedido->base_estado_id = 3;
				if (!$pedido->update()) {
	                throw new NegocioExcepcion("NO SE HA PODIDO REGISTRAR RESOLUCION DE PEDIDO", "100");
	            }else{
	            	Load::models("pedido/pedido_histo_estado");
		            $pedidoestado = new PedidoHistoEstado();
		            $pedidoestado->pedido_id = $pedido_id;
		            $pedidoestado->pedido_estado_id = 3;
		            $pedidoestado->pedhisest_fch = date("d/m/y");
		            $usr_id = Auth::get("id");
		            $pedidoestado->usr_id = $usr_id;
		            if (!$pedidoestado->save()) {
		                throw new Exception("No Se ha podido guardado el pedido por el estado", 999, NULL);
		            }
	            	Session::delete('pedido_id');
	            }
			}
			if($tipo != LIBRO_TIPO_UNION_CONVIVENCIAL){
				$fab = new FabricaETLActas();
				$experto = $fab->obtenerExpertoInverso($tipo);
				$experto->verificarMarginal($acta_id);
				$experto->migrarRC($acta_id);
	        }
            return $nombre_nuevo;
        } catch (Exception $ex) {
            Load::model("enlace_imagen")->rollback();
            throw $ex;
        }
    }

    /**
     * enlaza el acta con la imagen correspondiente
     * @param integer $acta
     * @param string $nombre_img
     * @return type
     * @throws NegocioExcepcion
     * @throws Exception
     */
    public static function guardar_imagen_acta($acta, $nombre_img,$tipo_informacion=NULL) {
            if ($nombre_img == "") {
                throw new NegocioExcepcion("No se ha seleccionado la imagen", 999);
            }
            if (!is_object($acta) && is_numeric($acta)) {
                $acta = ExpertoActas::buscar_acta_por_id($acta);
            }

            $libro = $acta->getBaseLibro();
            $tipo = $libro->getBaseTipoLibro()->tipo;

            $ubicacion = "-$tipo-$libro->anio-$libro->numero";

            $nombre_nuevo = ExpertoEnlace::generar_nombre_imagen($libro, $acta->numero, $nombre_img);
            $imagen = new EnlaceImagen();
            $imagen->fecha_trx = new date();
            $imagen->nombre = $nombre_nuevo;
            $imagen->ubicacion = $ubicacion;
            $imagen->informacion_respaldatoria_id = $tipo_informacion;
            if (!$imagen->save()) {
                throw new NegocioExcepcion("NO SE HA PODIDO INSERTAR EL REGISTRO DE IMAGEN", "100", "");
            }
            $ruta = ExpertoEnlace::obtener_ruta_directorio_segun_libro_id($libro);

            $enlace_acta_imagen = new EnlaceActaImagen();
            $enlace_acta_imagen->enlace_imagen_id = $imagen->id;
            $enlace_acta_imagen->acta_acta_id = $acta->id;
            $enlace_acta_imagen->base_estado_id = ESTADO_ENLACE_IMAGEN_NORMAL;
            $enlace_acta_imagen->usuarios_id = Auth::get("id");
            if (!$enlace_acta_imagen->save()) {
                throw new NegocioExcepcion("NO SE HA PODIDO INSERTAR EL REGISTRO DE IMAGEN", "100", "");
            }
            $uri_viejo = "$ruta/$nombre_img";
            $uri_nuevo = "$ruta/$nombre_nuevo";
            ExpertoImagen::renombrar($uri_viejo, $uri_nuevo);
            return $imagen;

    }

    public static function guardar_acta($acta, $ciudadanos) {
        try {

            $acta_acta = ExpertoActas::guardar_acta($acta);
            $padres = 0;
            $ciud_ppal_id = NULL;
            foreach ($ciudadanos as $rol => $ciudadano) {

                $ciud = ExpertoCiudadanos::guardar_ciudadano($ciudadano);
                $ciudadano["ciud_ciudadano_id"] = $ciud->id;
                //$documento = ExpertoCiudadanos::guardar_documento($ciudadano);
                ///$ciud->ciud_ciudadano_documento_id = $documento->id;
                if ($rol == ROL_PRINCIPAL || $rol == ROL_ESPOSO_CONTRAYENTE) {
                    $ciud_ppal = $ciud;
                }
                /* if (!$ciud->update()) {
                  new Exception("no se ha podido actualizar el ultimo documento", "1001");
                  } */
                if ($ciud_ppal->id) {
                    if ($ciud) {
                        $padres+=1;
                        //Flash::info("principal  ".$ciud_ppal->id."    relacion   ".$ciud->id."   acta id   ".$acta_acta->id);
                        $existe = Load::model('acta_acta_ciudadano')->existe_relacion($ciud_ppal->id, $ciud->id, $acta_acta->id);
                        if (!$existe && $ciud_ppal->id != $ciud->id) {
                            ExpertoActas::guardar_relacion_ciudadano($ciud->id, $acta_acta->id, $rol, $ciud->nombre, $ciud->apellido, $ciud->base_sexo_id, $ciud_ppal->id);
                        }
                    }
                }
            }
            if ($padres == 0) {
                ExpertoActas::guardar_relacion_ciudadano($ciud_ppal->id, $acta_acta->id, ROL_PRINCIPAL, $ciud_ppal->nombre, $ciud_ppal->apellido, $ciud_ppal->base_sexo_id, NULL);
            }

            $libro = ExpertoLibros::buscar_libro_segun_id($acta["base_libro_id"]);
            $tipo = $libro->getBaseTipoLibro()->tipo;

            return 1;
        } catch (Exception $ex) {
            Load::model("enlace_imagen")->rollback();
            Flash::error($ex->getMessage());
        }
    }

    /**
     * 
     * @param ActaActa $acta
     * @param string $nombre_img
     * @return boolean
     * @throws NegocioExcepcion
     */
    public static function guardar_imagen_segun_acta($acta, $nombre_img) {
        try {
            Load::model("enlace_imagen")->begin();
            $imagenes = $acta->getImagenes();
            if (count($imagenes) > 0) {
                foreach ($imagenes as $imagen) {
                    if ($imagen->nombre == $nombre_img) {
                        throw new NegocioExcepcion("EL ACTA YA SE ENCUENTRA ENLAZADA", "100");
                    }
                }
            }
            $libro = $acta->getBaseLibro();
            $tipo = $libro->getBaseTipoLibro()->tipo;
            $nombre_original = "$tipo$libro->anio$libro->numero";
            $ubicacion = "-$tipo-$libro->anio-$libro->numero";
            $nombre_nuevo = ExpertoEnlace::generar_nombre_imagen($libro, $acta->numero, $nombre_img);
            $imagen = new EnlaceImagen();
            $imagen->fecha_trx = new date();
            $imagen->nombre = $nombre_nuevo;
            $imagen->ubicacion = $ubicacion;
            if (!$imagen->save()) {
                throw new NegocioExcepcion("NO SE HA PODIDO INSERTAR EL REGISTRO DE IMAGEN", "100", "");
            }
            $ruta = ExpertoEnlace::obtener_ruta_directorio_segun_libro_id($libro->id);
            $enlace_acta_imagen = new EnlaceActaImagen();
            $enlace_acta_imagen->enlace_imagen_id = $imagen->id;
            $enlace_acta_imagen->acta_acta_id = $acta->id;
            $enlace_acta_imagen->base_estado_id = ESTADO_ENLACE_IMAGEN_NORMAL;
            $enlace_acta_imagen->usuarios_id = Auth::get("id");
            if (!$enlace_acta_imagen->save()) {
                throw new NegocioExcepcion("NO SE HA PODIDO INSERTAR EL REGISTRO DE IMAGEN", "100", "");
            }
            $uri_viejo = "$ruta/$nombre_img";
            $uri_nuevo = "$ruta/$nombre_nuevo";
            $ok = ExpertoImagen::renombrar($uri_viejo, $uri_nuevo);
            Load::model("enlace_imagen")->commit();
            return $nombre_nuevo;
        } catch (Exception $ex) {
            Load::model("enlace_imagen")->rollback();
            Flash::error($ex->getMessage());
            return FALSE;
        }
    }

    /**
     * Se encarga de eliminar el enlace desde el punto de vista del acta, renombrando la imagen fisica a borrado_
     * @param type $acta_id
     */
    public static function eliminar_enlace_acta($acta_id, $nombre) {
        $enlace = new EnlaceActaImagen();
        try {
            $enlace->begin();

            $img = new EnlaceImagen();
            $img = $img->find_first("nombre = '$nombre'");
            $enlaces = $enlace->find("acta_acta_id = $acta_id and enlace_imagen_id = $img->id");
            if (count($enlaces) == 0) {
                return array();
            }
            $acta = ExpertoActas::buscar_acta_por_id($acta_id);
            $ruta = ExpertoEnlace::obtener_ruta_directorio_segun_libro_id($acta->base_libro_id);
            $enlace->delete_all("acta_acta_id = $acta_id and enlace_imagen_id = $img->id");
            foreach ($enlaces as $tmp_enlace) {
                $imagen = $tmp_enlace->getEnlaceImagen();
                $img_nom = $imagen->nombre;
                $nom = explode(".", $img_nom);
                $nombre = "borrado_" . str_pad($acta->numero, 6, "0", STR_PAD_LEFT) . ".$nom[1]";
                if (file_exists("$ruta/{$img_nom}")) {
                    ExpertoImagen::renombrar("$ruta/{$img_nom}", "$ruta/$nombre");
                } else {
                    Logger::info("Se ha intentado renombrar una imagen, pero esta no existe $ruta{$img_nom}", 9989);
                }
                $dto = new stdClass();
                $dto->old = $img_nom;
                $dto->new = $nombre;
                $ret[] = $dto;
                $imagen->delete();
            }
            $enlace->commit();
            return $ret;
        } catch (Exception $ex) {
            $enlace->rollback();
            Logger::error($ex->getMessage());
            return array();
        }
    }

    /**
     * Se encarga de eliminar el enlace desde el punto de vista del acta, renombrando la imagen fisica a borrado_
     * @param type $acta_id
     */
    public static function eliminar_enlace_acta_id($acta_id) {
        $acta = ExpertoActas::buscar_acta_por_id($acta_id);
        $imagenes = $acta->getImagenes();
        if (count($imagenes) > 0) {
            foreach ($imagenes as $imagen) {
                ExpertoEnlace::eliminar_enlace_acta($acta_id, $imagen->nombre);
            }
        }
    }

    /**
     * Modifica todos los enlaces normales a caducada del enlace, para que se pueda visualizar en determinadas ocaciones
     * @param integer|object $acta
     */
    public static function modificar_estado_enlace_segun_acta_id($acta) {
        if (!is_object($acta) && is_numeric($acta)) {
            $acta = ExpertoActas::buscar_acta_por_id($acta);
        }
        $enlace = new EnlaceActaImagen();
        $enlaces = $enlace->find("acta_acta_id = $acta->id and base_estado_id = " . ESTADO_ENLACE_IMAGEN_NORMAL);
//        throw new NegocioExcepcion("Cant Enlaces:".count($enlaces));
        if (count($enlaces) == 0) {
            return array();
        }
        $ret = NULL;
        $ruta = ExpertoEnlace::obtener_ruta_directorio_segun_libro_id($acta->base_libro_id);
        foreach ($enlaces as $tmp_enlace) {
            $tmp_enlace->base_estado_id = ESTADO_ENLACE_CADUCADA;
            $tmp_enlace->update();

            $imagen = $tmp_enlace->getEnlaceImagen();
            $img_nom = $imagen->nombre;
            $nom = explode(".", $img_nom);
            $nombre = "viejo_" . str_pad($acta->numero, 6, "0", STR_PAD_LEFT) . ".$nom[1]";
            for($i = 0 ; $i < 10 ; $i++){
                if (file_exists($nombre)) {
                    $nombre = "viejo_".$i."_" . str_pad($acta->numero, 6, "0", STR_PAD_LEFT) . ".$nom[1]";
                }
            }
            $imagen->nombre = $nombre;
            $imagen->update();
//            echo "nombre viejo: $ruta{$img_nom}";
//            echo "nombre nuevo: $ruta$nombre";
            if(file_exists("$ruta/{$img_nom}")){
                ExpertoImagen::renombrar("$ruta/{$img_nom}", "$ruta/$nombre");
            }
            $dto = new stdClass();
            $dto->old = $img_nom;
            $dto->new = $nombre;
            $ret[] = $dto;
        }
        return $ret;
    }
    /**
     * Modifica solo  el enlace normales a caducada de la imagen con la informacion respaldatoria
     * @param integer|object $acta
     */
    public static function modificar_estado_enlace_segun_acta_id_e_informacion_respaldatoria_id($acta,$informacion_id) {
        if (!is_object($acta) && is_numeric($acta)) {
            $acta = ExpertoActas::buscar_acta_por_id($acta);
        }
        $enlaces = Load::model("enlace_acta_imagen")->buscar_enlace_segun_acta_id_e_informacion_respaldatoria($acta->id,$informacion_id);
        if (count($enlaces) == 0) {
            return array();
        }
        $ret = NULL;
        $ahora = new DateTime("now");
        $ruta = ExpertoEnlace::obtener_ruta_directorio_segun_libro_id($acta->base_libro_id);
        foreach ($enlaces as $tmp_enlace) {
//            echo "id enlace: ",$tmp_enlace->id;
            $tmp_enlace->base_estado_id = ESTADO_ENLACE_CADUCADA;
            $tmp_enlace->update();
            $imagen = $tmp_enlace->getEnlaceImagen();
            $img_nom = $imagen->nombre;
            $nombre = $ahora->format(("YmdHis"))."." . $img_nom;
            $imagen->nombre = $nombre;
            $imagen->update();
            if(file_exists("$ruta/{$img_nom}")){
                ExpertoImagen::renombrar("$ruta/{$img_nom}", "$ruta/$nombre");
                $dto = new stdClass();
                $dto->old = $img_nom;
                $dto->new = $nombre;
                $ret[] = $dto;
            }
        }
        return $ret;
    }
    /**
     * Modifica todos los enlaces normales a caducada del enlace del tramite, para que se pueda visualizar en determinadas ocaciones
     * @param integer|object $acta
     */
    public static function modificar_estado_enlace_tramite_segun_tramite_id_e_imagen_id($tramite,$tipo_informacion_id) {
        
        $ret = NULL;
        $anio = date("Y");
        if (is_numeric($tramite)) {
            $tramite = ExpertoTramite::buscar_tramite_por_id($tramite);
        }
        $enlace = Load::model("enlace_tramite_informacion_respaldatoria");
        $enlaces = $enlace->buscar_existencia_informacion_respaldatoria_en_tramite_id($tramite->id,$tipo_informacion_id);
        if (count($enlaces) == 0) {
            return array();
        }
        $tipo_tramite = ExpertoTramite::buscar_tipo_tramite_por_id($tramite->tramite_tipo_tramite_id);
        $categoria = $tipo_tramite->getCategoria();
        $ubicacion = "-tramites-$anio-$categoria->letra-$tramite->id";
        $imagenes = array();
        $ruta_vieja = $_SERVER['DOCUMENT_ROOT'] . PUBLIC_PATH . 'default/public/files/actas/';
        $ruta = Config::get("config.application.ruta_imagenes") . str_replace("-", "/", $ubicacion);
        foreach ($enlaces as $tmp_enlace) {
            $tmp_enlace->base_estado_id = ESTADO_ENLACE_CADUCADA;
            $tmp_enlace->update();

            $imagen = $tmp_enlace->getEnlaceImagen();
            $img_nom = $imagen->nombre;
            $nombre = "viejo_" . $img_nom;
            for($i = 0 ; $i < 10 ; $i++){
                if (file_exists($nombre)) {
                    $nombre = "viejo_$i_" . $img_nom;
                }
            }
            if(file_exists("$ruta/{$img_nom}")){
                ExpertoImagen::renombrar("$ruta/{$img_nom}", "$ruta/$nombre");
            }
            $dto = new stdClass();
            $dto->old = $img_nom;
            $dto->new = $nombre;
            $ret[] = $dto;
        }
    }

}
