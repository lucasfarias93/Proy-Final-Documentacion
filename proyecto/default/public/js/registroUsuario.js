//Evitar introducir letras en numero de documento
$(function() {
    $("#numero_documento").keypress(function(event) {
        if (event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) {
            alert("Solo puede ingresar numeros!");
            return false;
        }
    });
});

//Evitar introducir letras en numero de telefono
$(function() {
    $("#numero_telefono").keypress(function(event) {
        if (event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) {
            alert("Solo puede ingresar numeros!");
            return false;
        }
    });
});

//Evitar introducir letras en numero de telefono
$(function() {
    $("#numero_tramite").keypress(function(event) {
        if (event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) {
            alert("Solo puede ingresar numeros!");
            return false;
        }
    });
});

//habilitar o deshabilitar boton submit hasta que los campos esten completos
(function() {
    $('form input').keyup(function() {

        var empty = false;
        $('form input').each(function() {
            if ($(this).val() == '') {
                empty = true;
            }
        });

        if (empty) {
            $('#submitButton').attr('disabled', 'disabled'); // updated according to http://stackoverflow.com/questions/7637790/how-to-remove-disabled-attribute-with-jquery-ie
        } else {
            $('#submitButton').removeAttr('disabled'); // updated according to http://stackoverflow.com/questions/7637790/how-to-remove-disabled-attribute-with-jquery-ie
        }
    });
})()

//auto carga del nombre de la persona segun el DNI y el numero tramite introducido
$("#numero_tramite").change(function () {
    if (($("#numero_tramite").val() != "") && ($("#numero_documento").val() != "")) {
        $("#nombres").val("");
        $("#nombres").removeAttr("readonly");
        $("#nombres").removeAttr("disabled");
        
        $("#apellido").val("");
        $("#apellido").removeAttr("readonly");
        $("#apellido").removeAttr("disabled");

        $.ajax({
            data: {
                'idtramite': $("#numero_tramite").val(),
                'dni': $("#numero_documento").val()
            },
            url: $.KumbiaPHP.publicPath+"tramitedni/buscar_ciudadano_por_id_dni",
            type: 'post',
            dataType: "json",
            success: function (response) {
                if (response) {
                    $("#nombres").val(response.nombres);
                    $("#nombres").attr("readonly", "readonly");

                    $("#apellido").val(response.apellido);
                    $("#apellido").attr("readonly", "readonly")
                } else {
                    alert("Revisar DNI y numero de tramite");
                }
            }
        });
    }
});

$("#numero_documento").change(function () {
    if (($("#numero_tramite").val() != "") && ($("#numero_documento").val() != "")) {
        $("#nombres").val("");
        $("#nombres").removeAttr("readonly");
        $("#nombres").removeAttr("disabled");
        
        $("#apellido").val("");
        $("#apellido").removeAttr("readonly");
        $("#apellido").removeAttr("disabled");

        $.ajax({
            data: {
                'idtramite': $("#numero_tramite").val(),
                'dni': $("#numero_documento").val()
            },
            url: $.KumbiaPHP.publicPath+"tramitedni/buscar_ciudadano_por_id_dni",
            type: 'post',
            dataType: "json",
            success: function (response) {
                if (response) {
                    $("#nombres").val(response.nombres);
                    $("#nombres").attr("readonly", "readonly");

                    $("#apellido").val(response.apellido);
                    $("#apellido").attr("readonly", "readonly")
                } else {
                    alert("Revisar DNI y numero de tramite");
                }
            }
        });
    }
});

$("#repetir_contraseña").change(function () {
    var contraseña = $("#contraseña").val();
    var repetida = $("#repetir_contraseña").val();

    if(contraseña != repetida) {
        $("#repetir_contraseña").removeClass("valid");
        $("#repetir_contraseña").addClass("invalid");
        $("#repetir_contraseña").prop("aria-invalid", "false");
    } else {
        $("#repetir_contraseña").removeClass("invalid");
        $("#repetir_contraseña").addClass("valid");
        $("#repetir_contraseña").prop("aria-valid", "false"); 
    }
});


//Poblar select departamento segun la provincia seleccionada
$("#provincia").change(function() {
    $.ajax({
            data: { 
                'provincia': $(this).find(':selected').val()
            },
            type: "POST",
            url: $.KumbiaPHP.publicPath+"departamento/Depto_segun_provincia",
            success: function(response) {
                var jsonObj = JSON.parse(response);
                
                //Alert utilizada para ver la cantidad de elementos que devuelve el server
                //alert(jsonObj.items.length);

                $('#select_dpto')
                    .find('option')
                    .remove()
                    .end()
                    .append('<option disabled value="option">Seleccione</option>')
                    .val('whatever')
                ;

                $('#select_localidad')
                    .find('option')
                    .remove()
                    .end()
                    .append('<option disabled value="option">Seleccione</option>')
                    .val('whatever')
                ;

                $.each(jsonObj.items, function(id,value) {
                    $("#select_dpto").append('<option value="'+value.id+'">'+value.nombredepartamento+'</option>');
                })
                $("#select_dpto").material_select();
                $("#select_localidad").material_select();
            }
    });
});

//Poblar select localidad segun el departamento seleccionado
$("#departamento").change(function() {
    console.log($(this).find(':selected').text());
    console.log($(this).find(':selected').val());
    $.ajax({
            data: { 
                'departamento': $(this).find(':selected').val()
            },
            type: "POST",
            url: $.KumbiaPHP.publicPath+"localidad/localidad_segun_dpto",
            success: function(response) {
                var jsonObj = JSON.parse(response);

                //Alert utilizada para ver la cantidad de elementos que devuelve el server
                //alert(jsonObj.items.length);

                $('#select_localidad')
                    .find('option')
                    .remove()
                    .end()
                    .append('<option disabled value="option">Seleccione</option>')
                    .val('whatever')
                ;

                $.each(jsonObj.items, function(id,value) {
                    $("#select_localidad").append('<option value="'+value.id+'">'+value.nombrelocalidad+'</option>');
                })
                $("#select_localidad").material_select();
            }
    });
});

//para definir que funcionalidad tiene el boton submit (crear usuario)
$("#submitButton").click(function () {

     var usuarioForm = {
            'login' : $("#nombre_usuario").val(),
            'clave' : $("#contraseña").val(),
            'clave2' : $("#repetir_contraseña").val(),
            'idtramite': $("#numero_tramite").val(),
            'dni' : $("#numero_documento").val(),
            'nombres' : $("#nombres").val(),
            'apellido' : $("#apellido").val(),
            'email' : $("#email").val(),
        };

        var rolData = {
            'ciudadano' : 3,
        }

    $.ajax({
            data: {
                'usuario': usuarioForm
            },
            url: $.KumbiaPHP.publicPath+"usuarios/crear",
            type: 'post',
            success: function (response) {
                alert("exito");
                window.location.replace($.KumbiaPHP.publicPath);
            }, 
            error: function (response) {
                alert("fallo" + response.text);
                window.location.replace("http://localhost/proyecto/admin");
            }
        });
});
