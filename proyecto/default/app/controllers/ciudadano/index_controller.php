<?php

Load::models('parentesco');
Load::models('cupondepago');
Load::models('solicitudacta');
Load::models('estadosolicitud');
Load::models('solicitudestado');
Load::negocio('experto_imagen');
Load::negocio('experto_actas');

class IndexController extends AdminController {

    protected function before_filter() {
        view::select(NULL, 'menu');
    }

    public function index() {
        
    }

    public function buscar_imagen() {
        try {
            if (Input::hasPost('tipolibro') && Input::hasPost('parentesco')) {
                $tipo = Input::post('tipolibro');
                $parentesco = Input::post('parentesco');
                session::set("tipolibro", $tipo);
                session::set("parentesco", $parentesco);
                $servicio = "http://localhost:8000/RCWebService.asmx/nacimiento_propia?wsdl"; //url del servicio
                $parametros['dni'] = Auth::get("dni");
                $parametros['tipo'] = $tipo; //es lo mismo con comillas simples que dobles
                $parametros['parentesco'] = $parentesco; //es lo mismo con comillas simples que dobles
                $client = new SoapClient($servicio);
                $result = $client->nacimiento_propia($parametros); //llamamos al métdo que nos interesa con los parámetros 
                $datos = $result->nacimiento_propiaResult->Objetos;
                if (!isset($result->nacimiento_propiaResult->Objetos)) {
                    $ruta = "/home/imagenes_produccion/no_disponible.gif";
                } else {
                    $ubicacion = str_replace("-", "/", $datos->ubicacion);
                    $ubicacion = str_replace("Q:-ActasEscaneadas", "", $ubicacion);
                    $ext = "png";
                    $tmp = str_replace("TIF", "", $datos->nombre);
                    $ruta_temporal_crop_original = Config::get("config.application.carpeta_temporal_original") . "crop/" . $tmp . $ext;
                    $ruta = ExpertoImagen::obtener_ruta_completa($ubicacion . "/$datos->nombre");
                    if (!file_exists($ruta)) {
                        Flash::error("No existe el acta");
                        throw new NegocioExcepcion("No existe el acta");
                    }
                }
                $dto = ExpertoImagen::convertir_imagen($ruta, ESTAMPA_CONSULTA);
                $dto->persona = $datos->persona;
                $dto->apellido = $datos->apellido;
                $dto->dni = $datos->dni;
                $dto->nroacta = $datos->nroacta;
                $dto->nrolibro = $datos->nrolibro;
                $dto->fecha_nacimiento = $datos->fecha_nacimiento;
                session::set("imagen", $dto);
                $ret[] = $dto;
                View::json($ret);
            } else {
                throw new NegocioExcepcion("no se han pasado los parametros");
            }
        } catch (NegocioExcepcion $ex) {
            view::select(null, null);
        }
    }

    public function buscar_parentesco_tipolibro() {

        if (Input::hasPost('tipolibro')) {
            $tr = new Parentesco();
            $listParentesco = $tr->filtrar_parentesco_por_tipolibro(input::post('tipolibro'));
            view::json($listParentesco);
        }
    }

    public function buscar_parentesco_tipolibro_mobile($tipolibro) {
        view::select(null, null);
        $tr = new Parentesco();
        $listParentesco = $tr->filtrar_parentesco_por_tipolibro($tipolibro);
        view::json($listParentesco);
    }

    public function buscar_imagen_mobile($tipolibro, $parentesco) {
        try {
            if ($tipolibro != null && $parentesco != null) {
                $servicio = "http://localhost:8000/RCWebService.asmx/nacimiento_propia?wsdl"; //url del servicio
                $parametros['dni'] = Auth::get("dni");
                $parametros['tipo'] = $tipolibro;
                $parametros['parentesco'] = $parentesco;
                $client = new SoapClient($servicio);
                $result = $client->nacimiento_propia($parametros); //llamamos al método que nos interesa con los parámetros 
                $datos = $result->nacimiento_propiaResult->Objetos;
                if (!isset($result->nacimiento_propiaResult->Objetos)) {
                    $ruta = "/home/imagenes_produccion/no_disponible.gif";
                } else {
                    $ubicacion = str_replace("-", "/", $datos->ubicacion);
                    $ubicacion = str_replace("Q:-ActasEscaneadas", "", $ubicacion);
                    $ext = "png";
                    $tmp = str_replace("TIF", "", $datos->nombre);
                    $ruta_temporal_crop_original = Config::get("config.application.carpeta_temporal_original") . "crop/" . $tmp . $ext;
                    $ruta = ExpertoImagen::obtener_ruta_completa($ubicacion . "/$datos->nombre");
                    if (!file_exists($ruta)) {
                        Flash::error("No existe el acta");
                        throw new NegocioExcepcion("No existe el acta");
                    }
                }
                $dto = ExpertoImagen::convertir_imagen_mobile($ruta, ESTAMPA_CONSULTA);
                $dto->persona = $datos->persona;
                $dto->apellido = $datos->apellido;
                $dto->dni = $datos->dni;
                $dto->nroacta = $datos->nroacta;
                $dto->nrolibro = $datos->nrolibro;
                $dto->fecha_nacimiento = $datos->fecha_nacimiento;
                session::set("datosmobile", $datos);
                View::json($dto);
            } else {
                throw new NegocioExcepcion("no se han pasado los parametros");
            }
        } catch (NegocioExcepcion $ex) {
            view::select(null, null);
        }
    }

    public function buscar_datos_mobile() {
        if (session::get("datosmobile")) {
            view::json(session::get("datosmobile"));
        } else
            view::json("No hay datos");
    }

    public function generar_pdf_firmar_mail() {
///Verifico que entro un estado de pago
        if (input::hasPost('estado')) {
            $estadopago = input::post('estado');
            Logger::info($estadopago);
            if (input::hasPost('nrocupon')) {
                $nrocupon = input::post('nrocupon');
                session::set("nrocupon", $nrocupon);
                Logger::info($nrocupon);
                if ($estadopago == 'pending') { //no mando el mail ni genero el pdf
                    $cp = new Cupondepago();
                    $cp->codigodepago = $nrocupon;
                    $cp->estadocupondepago = "Pendiente de pago";
                    $cp->fechaemisionpago = UtilApp::fecha_actual();
                    $cp->montototal = 100;
                    $cp->idcodigoprovincial = 6;
                    $cp->create();
                    try {
                        $se2 = new Solicitudestado();
                        $sa = session::get("solicitudid");
                        $se2->idsolicitudacta = $sa; ///le asigno el id del acta a la solicitud estado
                        Logger::info("Solicitud " . $sa);
                        $se2->idestadosolicitud = 3; //Pendiente de pago
                        $se2->fechacambioestado = UtilApp::fecha_actual();
                        $se2->create();
                    } catch (NegocioExcepcion $e) {
                        Logger::info("Error al crear el estado de la solicitud  " . $e);
                    }
                    try {
                        $sa2 = new Solicitudacta();
                        Logger::info("Solicitud estado " . $se2->id);
                        $sa2->ultimosolicitudestado = $se2->id;
                        $sa2->idcupondepago = $cp->id;
                        Logger::info("Cupon de pago " . $cp->id);
                        $sa2->id = $sa;
                        $sa2->update();
                    } catch (NegocioExcepcion $e) {
                        Logger::info("Error al actualizar la solicitud  " . $e);
                    }
                }
                if ($estadopago == 'approved') { //mando el mail con el pdf firmado
                    $cp = new Cupondepago();
                    $cp->codigodepago = $nrocupon;
                    $cp->estadocupondepago = "Pagada";
                    $cp->fechaemisionpago = UtilApp::fecha_actual();
                    $cp->montototal = 100;
                    $cp->idcodigoprovincial = 6;
                    $cp->create();
                    try {
                        $se2 = new Solicitudestado();
                        $sa = session::get("solicitudid");
                        $se2->idsolicitudacta = $sa; ///le asigno el id del acta a la solicitud estado
                        $se2->idestadosolicitud = 2; //Pagada
                        $se2->fechacambioestado = UtilApp::fecha_actual();
                        $se2->create();
                    } catch (NegocioExcepcion $e) {
                        Logger::info("Error al crear el estado de la solicitud  " . $e);
                    }
                    try {
                        $sa2 = new Solicitudacta();
                        $sa2->ultimosolicitudestado = $se2->id;
                        $sa2->idcupondepago = $cp->id;
                        Logger::info("Cupon de pago " . $cp->id);
                        $sa2->id = $sa;
                        $sa2->update();
                    } catch (NegocioExcepcion $e) {
                        Logger::info("Error al actualizar la solicitud  " . $e);
                    }
                    $this->urlacta = ExpertoActas::generar_pdf(session::get("imagen"));
                    $url = $this->urlacta;
                    $url = str_replace('proyecto', 'public', $url);
                    ExpertoActas::enviar_mail($_SERVER['DOCUMENT_ROOT'] . PUBLIC_PATH . "default" . $url);
                    $this->mail_aprobado($nrocupon, $sa);
                }
            } else {
                Logger::info("No se genero el cupon de pago");
            }
        } else {
            Logger::info("No ingreso ningun estado de pago");
        }
    }

    public function generar_pdf_mobile() {
        view::select(NULL, NULL);
        try {
            $this->urlacta = ExpertoActas::generar_pdf(session::get("imagen"));
            $url = $this->urlacta;
            $url = str_replace('proyecto', 'public', $url);
            var_dump($url);
            ExpertoActas::enviar_mail($_SERVER['DOCUMENT_ROOT'] . PUBLIC_PATH . "default" . $url);
            view::json(TRUE);
        } catch (NegocioExcepcion $e) {
            view::json($e);
        }
    }

    public function getCurrentId() {
        view::select(NULL, NULL);
        if (Auth::get('id') != NULL) {
            view::json(Auth::get('id'));
        } else {
            view::json(FALSE);
        }
    }

    public function mail_aprobado($nrocupon, $sa) {
        load::lib("phpmailer/class.phpmailer");
        view::template(NULL);
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
            $mail->AddAddress(Auth::get('email')); // Esta es la dirección a donde enviamos
            //$mail->AddAddress("dggomez@mendoza.gov.ar"); // Esta es la dirección a donde enviamos
            $mail->IsHTML(true); // El correo se envía como HTML
            $mail->Subject = "Pago acreditado"; // Este es el titulo del email.
            $body = "El pago de tu acta n°" . $sa . " se acredito en nuestro sistema. El nro de cupon es: " . $nrocupon;
            $mail->Body = $body; // Mensaje a enviar
            $exito = $mail->Send(); // Envía el correo.
//También podríamos agregar simples verificaciones para saber si se envió:
            if ($exito) {
                Flash::info("El correo fue enviado correctamente");
                Router::redirect();
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
