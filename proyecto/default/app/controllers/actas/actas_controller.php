<?php

Load::negocio('experto_actas');
Load::negocio('experto_enlace');
Load::negocio('experto_imagen');

class ActasController extends AdminController {

    public function before_filter() {
        if (Input::isAjax()) {
            View::select(NULL, NULL);
        }
    }

    public function index($pagina = 1) {
        if(!$pagina){
            Session::delete("criterio_consulta");
        }
        try {
            if (Input::hasPost('criterio') || Session::get("criterio_consulta")) {

                if (Input::hasPost('criterio')) {
                    $criterio = Input::post('criterio');
                    $criterio["pagina"] = $pagina;
                    Session::set("criterio_consulta", $criterio);
                    Session::set("acta_tpo", $criterio["tipo_libro"]);
                } else {//el criterio lo toma por la session
                    $criterio = Session::get("criterio_consulta");
                    $sessionPag = $criterio["pagina"];
                    if ($pagina != $sessionPag) {
                        Router::redirect("actas/actas/index/" . $sessionPag);
                    }
                }
                $actas = ExpertoActas::buscar_acta_segun_criterio($criterio, $pagina);
                View::partial("listado_actas", FALSE, array("tipo_libro" => $criterio["tipo_libro"], "actas" => $actas));
            }
            if ($criterio) {
                $this->criterio = $criterio;
            }
        } catch (NegocioExcepcion $ex) {
            Flash::warning($ex->getMessage());
        } catch (Exception $ex) {
            Logger::error($ex->getMessage());
            Flash::error($ex->getMessage());
        }
    }

    public function visualizar() {
        try {
            if (Input::hasPost("acta") || Session::has("acta_id")) {
                $criterio = Input::post('criterio');
                try {
                    if (Input::hasPost("acta")) {
                        $acta = Input::post("acta");
                        if(!$criterio["tipo_libro"]){
                            $tipo = Session::get("acta_tpo");
                        }else{
                            $tipo = $criterio["tipo_libro"];
                        }
                    } else {
                        $tipo = Session::get("acta_tpo");
                        $acta = Session::get("acta_id");
                    }
                    $dto = ExpertoActas::buscar_datos_acta($acta, $tipo);
                    $this->dto = $dto;
                    $this->acta = $acta;
                    $imagenes = ExpertoEnlace::buscar_imagenes(Load::model("acta_acta")->find_first($dto->acta_id)); //Input::post("acta")));
                    $cantidad_marginales = ExpertoActas::buscar_cantidad_marginal_segun_acta($dto->acta_id);
                    if ($cantidad_marginales > 0) {
                        Flash::warning("El acta '$dto->numero_acta' del tomo '$dto->numero_libro' que desea visualizar ha sido caducada");
                        return Router::toAction();
                    }
                    if (count($imagenes) == 0) {
                        Flash::error("La imagen del acta no se encuentra disponible");
                        return Router::toAction();
                    }
                    $this->imagenes = $imagenes;
                    $this->criterio = Input::post('criterio');
                } catch (PermisoDenegadoExcepcion $ex) {
                    if (Input::post("pedido_id") == 0 || Input::post("bloqueada") == 't') {
                        Flash::error($ex->getMessage());
                        Router::toAction();
                        return;
                    }
                }
            } else {
                Router::toAction();
            }
        } catch (NegocioExcepcion $ex) {
            Flash::info($ex->getMessage());
        } catch (Exception $ex) {
            Logger::error($ex->getMessage());
        }
    }

    public function crop() {
        ExpertoImagen::crop(Input::post("img"), Input::post("imageW"), Input::post("imageH"), Input::post("selectorX"), Input::post("selectorY"), Input::post("selectorH"), Input::post("selectorW"), Input::post("viewPortH"), Input::post("viewPortW"));
    }
	
	public function crop_enlace() {
        ExpertoImagen::crop_enlace(Input::post("libro_id"), Input::post("archivo"), Input::post("imageW"), Input::post("imageH"), Input::post("selectorX"), Input::post("selectorY"), Input::post("selectorH"), Input::post("selectorW"), Input::post("viewPortH"), Input::post("viewPortW"), Input::post("rotate"));
    }
	
    public function print_acta() {
        if (Input::hasPost("data")) {
            try {
                $config = Config::read('config');
                echo ExpertoActas::generar_pdf(Input::post('data'));
                $solicitante = Input::post('solicitante');
                ExpertoSeguridad::registrar_solicitud(Input::post('acta_id'), $solicitante);
            } catch (PermisoDenegadoExcepcion $ex) {
                echo '{"codigo":0,"mensaje":"' . $ex->getMessage() . '" }';
                return;
            }
        } else {
            echo '{"codigo":0,"mensaje":"' . "Debe seleccionar una imagen" . '" }';
        }
    }

    public function buscar_acta_por_libro_acta() {
        if (Input::hasPost("libro") && Input::hasPost("acta_numero")) {
            if (Input::post("acta_numero") == "") {
                echo '{"codigo":0,"mensaje":"' . Flash::info("No se ha ingresado el numero de acta") . '" }';
            }
            $acta = ExpertoActas::buscar_acta_por_libro_id_numero(Input::post("libro"), Input::post("acta_numero"));
            if ($acta) {
                if (isset($acta->id)) {
                    //$libro = Load::model('base_libro')->find_first(Input::post("libro"));
                    // $dto = ExpertoActas::buscar_datos_acta_migrada($acta->id, $libro->base_tipo_libro_id, $libro->numero, Input::post("acta_numero"));
                    $dto = ExpertoCiudadanos::buscar_progenitores_por_acta_id($acta->id);
                } else {
                    $dto = $acta;
                }
                echo json_encode($dto);
            } else {
                $libro = Load::model('base_libro')->find_first(Input::post("libro"));
                $dto = ExpertoActas::buscar_datos_acta_migrada(0, $libro->base_tipo_libro_id, $libro->numero, Input::post("acta_numero"));
                echo json_encode($dto);
            }
        } else {
            echo '{"codigo":0,"mensaje":"' . Flash::info("No se pasaron los parametros correctos") . '" }';
        }
    }

    public function buscar_acta_por_nro_libro_acta() {
        try {


            if (Input::hasPost("libro_numero") && Input::hasPost("acta_numero") && Input::hasPost("tipo_libro")) {
                if (Input::post("acta_numero") == "" || Input::post("libro_numero") == "") {
                    echo '{"codigo":0,"mensaje":"No se ha ingresado el numero de acta y numero de libro"}';
                }
                
                $acta = ExpertoActas::buscar_actas_segun_tipo_numero_libro_y_numero_acta(Input::post("tipo_libro"), Input::post("libro_numero"), Input::post("acta_numero"));
                if (isset($acta->id)) {
                    $dto = ExpertoCiudadanos::buscar_acta_acta_ciudadanos_roles_principales($acta->id);
                    echo json_encode($dto);
                } else {
                    echo '{"codigo":0,"mensaje":"No se ha encontrado los datos del acta"}';
                }
            } else {
                echo '{"codigo":0,"mensaje":"No se pasaron los parametros correctos"}';
            }
        } catch (Exception $ex) {
            echo '{"codigo":0,"mensaje":"' . $ex->getMessage() . '"}';
        }
    }

    public function buscar_detalle_acta_por_nro_libro_tipo_acta() {
        try {


            if (Input::hasPost("libro_numero") && Input::hasPost("acta_numero") && Input::hasPost("libro_tipo")) {
                if (Input::post("acta_numero") == "" || Input::post("libro_numero") == "" || Input::post("libro_tipo") == "") {
                    throw new NegocioExcepcion("Debe seleccionar Tipo de libro, numero libro y numero de acta");
                }
                $acta = ExpertoActas::buscar_detalle_acta_por_nro_libro_tipo_acta(Input::post("libro_tipo"), Input::post("libro_numero"), Input::post("acta_numero"));
                View::json($acta);
            } else {
                throw new NegocioExcepcion("No se pasaron los parametros correctos");
            }
        } catch (Exception $ex) {
            View::json($ex->getMessage());
        }
    }

    public function buscar_acta() {
        if (Input::hasPost("acta")) {
            $dto = ExpertoActas::buscar_detalle_completo_por_acta_id(Input::post("acta"));
            echo json_encode($dto);
        } else {
            echo '{"codigo":0,"mensaje":"No se pasaron los parametros correctos"}';
        }
    }

    public function buscar_acta_por_documento() {
//        if(Input::hasPost("rol") && )
    }

    public function ver_imagen() {
        if (Input::hasPost("acta_c")) {
            $acta_c = Input::post("acta_c");

            $ac = ExpertoActas::buscar_acta_ciudadano_por_id($acta_c);
            $obj_acta = ExpertoActas::buscar_acta_por_id($ac->acta_acta_id);
            $imagenes = ExpertoEnlace::buscar_imagenes($obj_acta);
            echo View::partial("visualizar_imagen", FALSE, array("imagenes" => $imagenes));
        }
    }
    public function ver_imagen_simple() {
        if (Input::hasPost("acta_c")) {
            $acta_c = Input::post("acta_c");

            //$ac = ExpertoActas::buscar_acta_por_id($acta_c);
            $obj_acta = ExpertoActas::buscar_acta_por_id($acta_c);
            $imagenes = ExpertoEnlace::buscar_imagenes($obj_acta);
            echo View::partial("visualizar_imagen_simple", FALSE, array("imagenes" => $imagenes));
        }
    }
	public function ver_imagen_respaldo() {
        if (Input::hasPost("acta_c")) {
            $inf_id = Input::post("acta_c");

            //$ac = ExpertoActas::buscar_acta_por_id($acta_c);
            //$obj_acta = ExpertoActas::buscar_acta_por_id($acta_c);
            $imagenes = ExpertoImagen::buscar_informacion_respaldatoria_por_id($inf_id);
			$file =Config::get("config.application.ruta_imagenes").str_replace('-', '/', $imagenes->ubicacion).'/'. $imagenes->nombre;
			
			header('Content-Type: application/pdf');
			//echo "filesize ".filesize($datos);
			$filename = $imagenes->nombre;
			header('Content-type: application/pdf');// esta linea fue mi dolor de cabeza
			header('Content-Disposition: inline; filename="' . $filename . '"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . filesize($file));
			header('Accept-Ranges: bytes');
			echo @readfile($file);
            //echo View::partial("visualizar_imagen_simple", FALSE, array("imagenes" => $imagenes));
            //echo $data;
            
        }
    }

    public function ver_imagen_nombre() {
        if (Input::hasGet("nombre_imagen")) {

            $nombre_imagen = Input::get("nombre_imagen");
            $imagenes = ExpertoEnlace::buscar_imagen_por_nombre($nombre_imagen);
            echo View::partial("visualizar_imagen", FALSE, array("imagenes" => array($imagenes)));
        } else {
            echo "No enviado se ha reconocido la imagen";
        }
    }

    public function verificar_actas() {

        try {
//            	$usuario=Auth::get('id');
            $oficina = Auth::get('base_oficina_id');
//				$oficina = 167;
            $cantidad = ExpertoActas::controlar_capacidad($oficina);
            if ($cantidad > 0) {
                echo '{"codigo":1,"mensaje":"USTED POSEE ' . $cantidad . ' ACTAS CADUCADAS" }';
                return;
            } else {
                echo '{"codigo":"0","mensaje":""}';
                return;
            }
        } catch (PermisoDenegadoExcepcion $ex) {
            echo '{"codigo":0,"mensaje":"' . $ex->getMessage() . '" }';
            return;
        }
    }

    public function ver_actas_caducadas($pagina = 1) {
        try {
            $bread = new stdClass();
            $bread->url = "";
            $bread->nombre = "Inicio";
            $breadcumbs[] = $bread;
            $bread = new stdClass();
            $bread->url = "actas/actas";
            $bread->nombre = "Actas";
            $breadcumbs[] = $bread;
            $bread = new stdClass();
            $bread->nombre = "Actas Caducadas";
            $bread->active = true;
            $breadcumbs[] = $bread;
            $this->breadcumbs = $breadcumbs;
            $this->actas = ExpertoActas::buscar_actas_caducadas(Auth::get("base_oficina_id"), $pagina);
        } catch (Exception $ex) {
            Flash::error($ex->getMessage());
        }
    }

    /**
     * muestra las actas que se encuentran labrado el marginal
     * @param type $pagina
     */
    public function incapacidad($pagina = 1) {
        try {
            $bread = new stdClass();
            $bread->url = "";
            $bread->nombre = "Inicio";
            $breadcumbs[] = $bread;
            $bread = new stdClass();
            $bread->url = "actas/actas";
            $bread->nombre = "Actas";
            $breadcumbs[] = $bread;
            $bread = new stdClass();
            $bread->nombre = "Actas Caducadas";
            $bread->active = true;
            $breadcumbs[] = $bread;
            $this->breadcumbs = $breadcumbs;
            $this->es_administrador = ExpertoSeguridad::es_administrador();
            $criterio = Input::post('criterio');
            if (!$criterio) {
                $criterio = array();
            }
            $this->actas = ExpertoActas::buscar_actas_con_marginal($criterio, $pagina);
        } catch (Exception $ex) {
            Flash::error($ex->getMessage());
        }
        Session::set('ruta', 'actas/actas/ver_actas_caducadas');
    }

    public function actualizo_libro() {
        if (Input::hasPost("m_id")) {
            try {
                ExpertoActas::actualizo_libro(Input::post("m_id"));
                Flash::info("Se ha actualizado correctamente el libro");
            } catch (NegocioExcepcion $ex) {
                Flash::error($ex->getMessage());
            }
        }
    }

    public function confirmar_acta() {
        if (Input::hasPost("acta_id") && Input::hasPost("valor")) {
            $resultado = ExpertoActas::confirmar_datos_acta(Input::post("acta_id"), Input::post("valor"));
            View::template(NULL);
            echo $resultado;
        } else {
            Flash::error("No se han enviado los parametro para resolver el pedido");
        }
    }

    public function anuladas() {
        try {
            $this->accion = "crear";
            if (Input::hasPost('acta')) {
                try {
                    $informacion = Input::post("informacion_respaldatoria");
                    $acta = Input::post("acta");
                    ExpertoActas::guardar_anuladas($acta, $informacion);
                    Flash::info("Se ha guardado el nacimiento");
                } catch (NegocioExcepcion $ex) {
                    Flash::error($ex->getMessage());
                } catch (Excepcion $ex) {
                    Flash::error($ex->getMessage());
                    Logger::error($ex->getTraceAsString());
                }
            } else {
                $this->libro = ExpertoLibros::buscar_libro_activo_segun_tipo_y_oficina(LIBRO_TIPO_NACIMIENTO);
//                $this->usuarios = ExpertoUsuario::buscar_usuario_por_oficina();
                $this->oficina = ExpertoOficina::buscar_oficina_por_id(Auth::get("base_oficina_id"));
            }
        } catch (Exception $ex) {
            Flash::error($ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
    }

}
