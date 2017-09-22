<?php

Load::negocio('experto_enlace');
Load::negocio('experto_imagen');

class ImagenController extends AdminController {

    public function before_filter() {
        if (Input::isAjax()) {
            View::select(NULL, NULL);
        }
    }

    public function buscar_imagen_por_nombre() {
        $config = Config::read('config');
        $rcivil = FALSE; //$config['application']['rcivil'];
        if ($rcivil) {
            Load::negocio("etl/experto_etl_enlace");
            if (Input::hasPost("nombre") && Input::hasPost("libro")) {
                $dto = ExpertoETLEnlace::buscar_imagen_por_libro_nombre(Input::post("libro"), Input::post("nombre"));
                echo json_encode($dto);
            }
        } else {
            if (Input::hasPost("nombre") && Input::hasPost("libro")) {
                $dto = ExpertoEnlace::buscar_imagen_por_libro_nombre(Input::post("libro"), Input::post("nombre"));
                echo json_encode($dto);
            }
        }
    }

    private function buscar_datos_para_enlazar_rcivil($libro_id, $numero_acta = NULL) {
        Load::negocio("etl/experto_etl_libro");
        Load::negocio("etl/experto_etl_enlace");
        $this->seleccionar_imagen = ExpertoSeguridad::verificar_permiso(PERMISO_ENLACE_MASIVO);
        $this->libro = ExpertoETLLibro::buscar_libro_segun_id($libro_id);
        $this->acta = ExpertoETLActas::buscar_acta_por_libro_id_numero($libro_id, $numero_acta);
        $ciudadanos = ExpertoETLActas::buscar_ciudadanos_rol_segun_acta($this->acta, $this->libro->lib_tipolibro);
        foreach ($ciudadanos as $ciudadano) {
            switch ($ciudadano->base_tipo_rol_id) {
                case ROL_PRINCIPAL: $this->ppal = $ciudadano;
                    $this->acta->fecha = $ciudadano->fecha_nacimiento;
                    break;
                case ROL_MADRE: $this->madre = $ciudadano;
                    break;
                case ROL_PADRE: $this->padre = $ciudadano;
                    break;
                case ROL_ESPOSO_CONTRAYENTE: $this->esposo = $ciudadano;
                    break;
                case ROL_ESPOSA_CONTRAYENTE: $this->esposa = $ciudadano;
                    break;
            }
        }
        if (!$this->seleccionar_imagen) {
            $this->imagenes = ExpertoImagen::buscar_imagen_ficticia();
            return;
        }
        $nombres_imagenes = ExpertoETLEnlace::buscar_imagenes_directorio($libro_id);
        $this->nombres_imagenes = $nombres_imagenes;
        if ($numero_acta) {
            $imagenes = ExpertoETLEnlace::buscar_imagenes_por_libro_id_y_numero_acta($libro_id, $numero_acta);
        } else {
            if (count($nombres_imagenes) > 0) {
                $imagenes[] = ExpertoETLEnlace::buscar_imagen_por_libro_nombre($libro_id, reset($nombres_imagenes));
            } else {
                $imagenes = ExpertoImagen::buscar_imagen_ficticia();
            }
        }
        $this->imagenes = $imagenes;
    }

    private function buscar_datos_para_enlazar($libro_id, $numero_acta = NULL) {
        $this->nombre_nuevo = '';
        if ($numero_acta) {
            $this->acta = ExpertoActas::buscar_acta_por_libro_id_numero($libro_id, $numero_acta);
            $ciudadanos = ExpertoCiudadanos::buscar_ciudadanos_rol_segun_acta($this->acta);

            foreach ($ciudadanos as $ciudadano) {
                switch ($ciudadano->base_tipo_rol_id) {
                    case ROL_PRINCIPAL: $this->ppal = $ciudadano;
                        $this->acta->fecha = $ciudadano->fecha_nacimiento;
                        break;
                    case ROL_MADRE: $this->madre = $ciudadano;
                        break;
                    case ROL_PADRE: $this->padre = $ciudadano;
                        break;
                    case ROL_ESPOSO_CONTRAYENTE: $this->esposo = $ciudadano;
                        break;
                    case ROL_ESPOSA_CONTRAYENTE: $this->esposa = $ciudadano;
                        break;
                }
            }
            $imagenes = ExpertoEnlace::buscar_imagenes_por_libro_id_y_numero_acta($libro_id, $numero_acta);
        } else {
            $acta = new stdClass();
            $acta->numero = "";
            $acta->bloqueada = "";
            $this->acta = $acta;
        }
        $this->libro = ExpertoLibros::buscar_libro_segun_id($libro_id);
        $this->tipo_libro = ExpertoLibros::buscar_tipo_libro_por_id($this->libro->base_tipo_libro_id);
        $this->seleccionar_imagen = ExpertoSeguridad::verificar_permiso(PERMISO_ENLACE_MASIVO);
        if (!$this->seleccionar_imagen) {
            $this->imagenes = ExpertoImagen::buscar_imagen_ficticia();
            return;
        }

        $ruta = Session::get('ruta');
        $nombres_imagenes = ExpertoEnlace::buscar_imagenes_directorio($libro_id);
        if ($ruta) {
            if ($ruta == 'actas/actas/ver_actas_caducadas') {
                $nombres_imagenes = ExpertoEnlace::buscar_imagenes_por_acta($this->acta);
            }
        }

        if ($this->nombre_nuevo == '') {
            $this->nombres_imagenes = $nombres_imagenes;
        } else {
            $this->nombres_imagenes = $this->nombre_nuevo;
        }


        if (!isset($imagenes)) {
            if (count($nombres_imagenes) > 0) {
                $imagenes[] = ExpertoEnlace::buscar_imagen_por_libro_nombre($libro_id, reset($nombres_imagenes));
            } else {
                $imagenes = ExpertoImagen::buscar_imagen_ficticia();
            }
        }
        $this->imagenes = $imagenes;
    }

    public function enlazar() {
        try {
            if (Input::hasPost("o")) {
                $o = Input::post("o");
                $this->sub_tipo_libro = $o["sub_tipo_libro"];
                if (array_key_exists("marginal", $o)) {
                    Session::set('marginal', $o["marginal"]);
                } else {
                    Session::set('marginal', NULL);
                }
                $config = Config::read('config');
                $rcivil = FALSE; //$config['application']['rcivil'];
                if ($rcivil) {

                    $numero_acta = NULL;
                    if (array_key_exists("acta", $o)) {
                        $numero_acta = $o["acta"];
                    }
                    $this->buscar_datos_para_enlazar_rcivil($o["libro"], $numero_acta);
                } else {
                    if (array_key_exists("acta", $o)) {
                        $this->buscar_datos_para_enlazar($o["libro"], $o["acta"]);
                    } else {
                        $this->buscar_datos_para_enlazar($o["libro"]);
                    }
                }
            } else {
                Router::redirect("actas/actas");
            }
        } catch (Exception $ex) {
            Logger::error($ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
    }

    public function eliminar_enlace_acta() {
        if (Input::hasPost("acta_id") && Input::hasPost("nombre")) {
            $config = Config::read('config');
            $rcivil = $config['application']['rcivil'];
            $rcivil = False;
            if ($rcivil) {
                Load::negocio("etl/experto_etl_enlace");
                $respuesta = ExpertoETLEnlace::eliminar_enlace_acta(Input::post("acta_id"), Input::post("nombre"));
                echo json_encode($respuesta);
            } else {
                $respuesta = ExpertoEnlace::eliminar_enlace_acta(Input::post("acta_id"), Input::post("nombre"));
                echo json_encode($respuesta);
            }
        } else {
            
        }
    }

    /**
     * coloca la imagen en el libro correspondiente
     */
    public function subir_imagen_libro() {
        if (Input::hasPost("libro")) {
            $libro = Input::post("libro");
            if (isset($_FILES['userfile'])) {
                try {
                    $nombre_nuevo = ExpertoImagen::subir_imagen('userfile', $libro);
                } catch (NegocioExcepcion $ex) {
                    Flash::error("No se ha podido guardar la imagen. Motivo: " . $ex->getMessage());
                }
            }
        }
    }

    /**
     * permite subir una imagen dentro del sistema enlazar
     */
    public function subir_imagen() {
        $config = Config::read('config');
        $nombre_nuevo = '';
        $rcivil = FALSE; // $config['application']['rcivil'];

        if ($rcivil) {
            Load::negocio('etl/experto_etl_libro');
            Load::negocio('etl/experto_etl_enlace');
            if (Input::hasPost("libro")) {
                $libro = Input::post("libro");
                if (isset($_FILES['userfile'])) {
                    try {
                        $this->imagenes = ExpertoETLEnlace::subir_imagen('userfile', $libro["id"]);
                    } catch (Exception $ex) {
                        $this->imagenes = ExpertoImagen::buscar_imagen_ficticia();
                    }
                }
                $this->buscar_datos_para_enlazar_rcivil($libro["id"]);
            } else {
                Flash::error("Debe seleccionar primero el libro para poder enlazar subir la imagen del acta");
            }
        } else {
            Load::negocio('experto_libros');
            if (Input::hasPost("libro")) {

                $libro = Input::post("libro");
                if (Input::hasPost("acta")) {
                    $acta = Input::post("acta");
                    $numero = $acta['numero'];
                } else {
                    $numero = '';
                }

                if (Input::hasPost("o")) {
                    $o = Input::post("o");
                    $numero = $o['acta'];
                    $this->sub_tipo_libro = $o["sub_tipo_libro"];
                } else {
                    $this->sub_tipo_libro = 0;
                }

                if (!$numero) {
                    if (Input::hasPost("o")) {
                        $o = Input::post("o");
                        $numero = $o['acta'];
                        $this->sub_tipo_libro = $o["sub_tipo_libro"];
                    }
                }
//                $this->libro = ExpertoLibros::buscar_libro_segun_id($libro["id"]);
//                $this->acta = ExpertoActas::buscar_acta_por_libro_id_numero($libro["id"], $numero);
                $this->buscar_datos_para_enlazar($libro["id"], $numero);
                if (isset($_FILES['userfile'])) {
                    try {
                        $nombre_nuevo = ExpertoImagen::subir_imagen_enlace('userfile', $libro["id"]);
                    } catch (Exception $ex) {
                        $this->imagenes = ExpertoImagen::buscar_imagen_ficticia();
                    }
                }
                if (!ExpertoSeguridad::verificar_permiso(PERMISO_ENLACE_MASIVO)) {
                    $this->nombres_imagenes = ExpertoEnlace::buscar_imagenes_directorio_nombre($libro["id"], $nombre_nuevo);
                } else {
                    $this->nombres_imagenes = ExpertoEnlace::buscar_imagenes_directorio($libro["id"]);
                }
                if ($nombre_nuevo != '') {
                    $imagenes[] = ExpertoEnlace::buscar_imagen_por_libro_nombre($libro["id"], $nombre_nuevo);
                } else {
                    $imagenes = new ArrayIterator;
                }

                $this->imagenes = $imagenes;
                $this->seleccionar_imagen = true;
                $this->nombre_nuevo = $nombre_nuevo;
            } else {
                Flash::error("Debe seleccionar primero el libro para poder enlazar la partida");
            }
        }
        View::select("enlazar");
    }

    public function guardar_enlace() {
        View::select("enlazar");
        $nombre_nuevo = '';
        if (Input::hasPost("acta")) {
            Load::model("base_tipo_rol");
            if (Input::hasPost("ppal")) {
                $ciudadanos[ROL_PRINCIPAL] = Input::post("ppal");
            }
            if (Input::hasPost("padre")) {
                $ciudadanos[ROL_PADRE] = Input::post("padre");
            }
            if (Input::hasPost("madre")) {
                $ciudadanos[ROL_MADRE] = Input::post("madre");
            }
            if (Input::hasPost("esposo")) {
                $ciudadanos[ROL_ESPOSO_CONTRAYENTE] = Input::post("esposo");
            }
            if (Input::hasPost("esposa")) {
                $ciudadanos[ROL_ESPOSA_CONTRAYENTE] = Input::post("esposa");
            }
            $config = Config::read('config');
            if (Session::has("pedido_id")) {
                $ruta = 'actas/pedido/listado';
            } else {
                $ruta = Session::get('ruta');
            }
            $rcivil = FALSE; //$config['application']['rcivil'];
            if ($rcivil) {
                Load::negocio("etl/experto_etl_enlace");
                $acta = Input::post("acta");
                ExpertoETLEnlace::guardar_enlace($acta, Input::post("imagen"), $ciudadanos);
                $this->buscar_datos_para_enlazar_rcivil($acta["base_libro_id"], $acta["numero"]);
            } else {
                $nombre_nuevo = ExpertoEnlace::guardar_enlace(Input::post("acta"), Input::post("imagen"), $ciudadanos);
            }
            $acta = Input::post("acta");
            $this->buscar_datos_para_enlazar($acta["base_libro_id"], $acta["numero"]);
            $this->nombre_nuevo = $nombre_nuevo;

            if ($ruta) {
                Router::toAction('../../' . $ruta);
            }
        } else {

            Router::toAction("enlazar");
        }
    }

}
