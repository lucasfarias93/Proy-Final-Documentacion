<?php

Load::models('usuarios');

class UsuariosController extends AppController {

    public function index($pagina = 1, $migrar = False) {
        try {
            $usr = new Usuarios();
            if (Input::hasPost("nombre_usuario")) {
                $this->usuarios = $usr->filtrar_por_usuario(Input::post("nombre_usuario"), $pagina);
            } else {
                $this->usuarios = $usr->paginar($pagina);
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    /**
     * Crea un usuario desde el backend.
     */
    public function crear() {
        view::select(null, null);
        try {
            if (Input::hasPost('usuario')) {
                //esto es para tener atributos que no son campos de la tabla
                $usr = new Usuarios(Input::post('usuario'));
                $clave = $usr->clave;
                $usrbd = new Usuarios();

                $usrbd->filtrar_por_login($usr->login);
                if ($usrbd && $usr->login == $usrbd->login) {
                    throw new NegocioExcepcion("El usuario ingresado ya existe");
                }

                $usrbd->filtrar_por_id($usr->idtramite);
                if ($usrbd && $usr->idtramite == $usrbd->idtramite) {
                    throw new NegocioExcepcion("El idtramite ingresado ya existe");
                }

                $usrbd->filtrar_por_dni($usr->dni);
                if ($usrbd && $usr->dni == $usrbd->dni) {
                    throw new NegocioExcepcion("El dni ingresado ya existe");
                }

                //Verifico si el dni del usuario o el idtramite ya existe
                //guarda los datos del usuario, y le asigna los roles 
                //seleccionados en el formulario.
                if ($usr->guardarCiudadano($usr, 3)) {
                    Logger::info("Usuario registrado");
                    //////mandar mail de notificacion
                    load::lib("phpmailer/class.phpmailer");
                    view::template(NULL);
                    try {
                        $usrbd->filtrar_por_email($usr->email);
                        if (!$usrbd) {
                            throw new NegocioExcepcion("El mail ingresado no existe");
                        }
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
                        $mail->AddAddress($usr->email); // Esta es la dirección a donde enviamos
                        //$mail->AddAddress("dggomez@mendoza.gov.ar"); // Esta es la dirección a donde enviamos
                        $mail->IsHTML(true); // El correo se envía como HTML
                        $link = '<a href="http://190.15.213.87:81">Aqui</a>';
                        $mail->Subject = "Cuenta habilitada"; // Este es el titulo del email.
                        $body = "Tu usuario es: " . $usrbd->login . " y tu clave es: " . $clave. " Gracias por registrarte!. Para acceder hace click " . $link;
                        $mail->Body = $body; // Mensaje a enviar
                        $exito = $mail->Send(); // Envía el correo.
//También podríamos agregar simples verificaciones para saber si se envió:
                        if ($exito) {
                            Flash::info("El correo fue enviado correctamente");
                            input::delete();
                            Router::redirect('login');
                        } else {
                            $mail->ErrorInfo;
                            throw new NegocioExcepcion($mail->ErrorInfo);
                            input::delete();
                        }
                    } catch (NegocioExcepcion $e) {
                        Logger::error($e->getMessage());
                        Flash::info($e->getMessage());
                    }
                } else {
                    throw new NegocioExcepcion("Verifique los datos ingresasdos");
                }
            }
        } catch (Exception $e) {
            Logger::error("Excepcion en el try");
            Flash::error($e->getMessage());
            Logger::error($e->getMessage());
            Logger::error($e->getTraceAsString());
        }
    }

    public function crear_mobile($login, $idtramite, $dni, $clave, $clave2, $nombres, $apellido, $email) {
        view::select(null, null);
        //crear usuario nuevo y usuario de la bd segun el nombre del login
        $usr = new Usuarios();
        $usr->login = $login;
        $usr->idtramite = $idtramite;
        $usr->dni = $dni;
        $usr->clave = $clave;
        $usr->clave2 = $clave2;
        $usr->nombres = $nombres;
        $usr->apellido = $apellido;
        $usr->email = $email;

        $usrbd = new Usuarios();
        $usrbd->filtrar_por_login($usr->login);

        //Buscar coincidencias segun el nombre de login
        if ($usrbd && $usr->login == $usrbd->login) {
            view::json("El usuario ingresado ya existe");
        } else {

            //Buscar coincidencias segun el id tramite
            $usrbd->filtrar_por_id($usr->idtramite);
            if ($usrbd && $usr->idtramite == $usrbd->idtramite) {
                view::json("El idtramite ingresado ya existe");
            } else {

                //Buscar coincidencias segun el DNI
                $usrbd->filtrar_por_dni($usr->dni);
                if ($usrbd && $usr->dni == $usrbd->dni) {
                    view::json("El dni ingresado ya existe");
                } else {
                    $usr->guardarCiudadano($usr, 3);
                    try {
                        ////Mandar mail
                        load::lib("phpmailer/class.phpmailer");
                        $mail = new PHPMailer();
//Luego tenemos que iniciar la validación por SMTP:
                        $mail->IsSMTP();
                        $mail->SMTPDebug = false;
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
                        $link = '<a href="http://190.15.213.87:81">Aqui</a>';
                        $mail->Subject = "Cuenta habilitada"; // Este es el titulo del email.
                        $body = "Tu usuario es: " . $login . " y tu clave es: " .$clave. " Gracias por registrarte!. Para acceder hace click " . $link;
                        $mail->Body = $body; // Mensaje a enviar
                        $exito = $mail->Send(); // Envía el correo.
//También podríamos agregar simples verificaciones para saber si se envió:
                    } catch (NegocioExcepcion $e) {
                        echo "El mail ingresado no existe en la Base de datos ";
                        Flash::error($e->getMessage());
                        view::json(FALSE);
                    }
                    view::json(TRUE);
                }
            }
        }
    }

    public function usuario($usuario) {
        $usr = new Usuarios();
        view::select(null, null);
        $user = $usr->filtrar_por_usuario($usuario, $pagina = 1);
        view::json($user);
    }

}
