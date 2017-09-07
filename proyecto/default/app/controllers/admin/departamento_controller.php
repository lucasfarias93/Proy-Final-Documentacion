<?php

/**
 * Carga del modelo Menus...
 */
Load::models('departamento');

class DepartamentoController extends AdminController {

    /**
     * Obtiene una lista para paginar las provincias
     */
    public function index($page = 1) {
        $r = new Departamento();
        $this->listDepartamento = $r->getDepartamento($page);
        ////Prueba mail
//        require("class.phpmailer.php");
        $mail = new PHPMailer();

//Luego tenemos que iniciar la validación por SMTP:
        $mail->IsSMTP();
        $mail->SMTPDebug = 2;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'smtp.gmail.com'; // SMTP a utilizar. Por ej. smtp.elserver.com
        $mail->Username = "diegocosas@gmail.com"; // Correo completo a utilizar
        $mail->Password = "gringodiego"; // Contraseña
        $mail->Port = 587; // Puerto a utilizar
        var_dump($mail->Port);
//Con estas pocas líneas iniciamos una conexión con el SMTP. Lo que ahora deberíamos hacer, es configurar el mensaje a enviar, el //From, etc.
        $mail->From = 'diegocosas@gmail.com'; // Desde donde enviamos (Para mostrar)
        $mail->FromName = "Soporte";
//Estas dos líneas, cumplirían la función de encabezado (En mail() usado de esta forma: “From: Nombre <correo@dominio.com>”) de //correo.
        $mail->AddAddress("diegocosas@gmail.com"); // Esta es la dirección a donde enviamos
//        $mail->AddAddress("dggomez@mendoza.gov.ar"); // Esta es la dirección a donde enviamos
        $mail->IsHTML(true); // El correo se envía como HTML
        $mail->Subject = "Recuperacion de contraseña"; // Este es el titulo del email.
        $body = "Para recuperar tu contraseña hace click en el link de abajo<br />";
        $mail->Body = $body; // Mensaje a enviar
        $exito = $mail->Send(); // Envía el correo.
//También podríamos agregar simples verificaciones para saber si se envió:
        if ($exito) {
            echo "El correo fue enviado correctamente";
        } else {
            echo "Hubo un inconveniente. Contacta a un administrador.";
        }
        var_dump($exito);
    }

    /**
     * Crea un Registro
     */
    public function crear() {
        /**
         * Se verifica si el usuario envio el form (submit) y si ademas 
         * dentro del array POST existe uno llamado "menus"
         * el cual aplica la autocarga de objeto para guardar los 
         * datos enviado por POST utilizando autocarga de objeto
         */
        if (Input::hasPost('departamento')) {
            /**
             * se le pasa al modelo por constructor los datos del form y ActiveRecord recoge esos datos
             * y los asocia al campo correspondiente siempre y cuando se utilice la convención
             * model.campo
             */
            $r = new Departamento(Input::post('departamento'));
            //En caso que falle la operación de guardar
            if ($r->create()) {
                Flash::valid('Operación exitosa');
                //Eliminamos el POST, si no queremos que se vean en el form
                Input::delete();
                return;
            } else {
                Flash::error('Falló Operación');
            }
        }
    }

    /**
     * Edita un Registro
     *
     * @param int $id (requerido)
     */
    public function edit($id) {
        $r = new Departamento();

        //se verifica si se ha enviado el formulario (submit)
        if (Input::hasPost('departamento')) {

            if ($r->update(Input::post('departamento'))) {
                Flash::valid('Operación exitosa');
                //enrutando por defecto al index del controller
                return Redirect::to();
            } else {
                Flash::error('Falló Operación');
            }
        } else {
            //Aplicando la autocarga de objeto, para comenzar la edición
            $this->departamento = $r->find_by_id((int) $id);
        }
    }

    /**
     * Eliminar un menu
     * 
     * @param int $id (requerido)
     */
    public function del($id) {
        $r = new Departamento();
        if ($r->delete((int) $id)) {
            Flash::valid('Operación exitosa');
        } else {
            Flash::error('Falló Operación');
        }

        //enrutando por defecto al index del controller
        return Redirect::to();
    }

}
