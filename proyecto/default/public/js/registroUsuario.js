$(function() {
    $("#numero_documento").keypress(function(event) {
        if (event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) {
            $(".alert").html("Enter only digits!").show().fadeOut(2000);
            return false;
        }
    });
});

$(function() {
    $("#numero_telefono").keypress(function(event) {
        if (event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) {
            $(".alert").html("Enter only digits!").show().fadeOut(2000);
            return false;
        }
    });
});

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

$("#numero_tramite").change(function () {
        $("#nombres").val("");

        $("#nombres").removeAttr("readonly")
        $("#nombres").removeAttr("disabled")
        
        $.ajax({
            data: {
                'idtramite': $("#numero_tramite").val(),
                'dni': $("#numero_documento").val()
            },
            url: $.KumbiaPHP.publicPath+"tramitedni/buscar_ciudadano_por_id_dni",
            type: 'post',
            dataType: "json",
            success: function (response) {
                $("#nombres").val(response.nombres);
                $("#nombres").attr("readonly", "readonly")

            }
        });
});

//para poblar los selects al cargar la pagina
$(document).ready(function() {
                    $.ajax({
                            type: "POST",
                            url: "getPaises.php",
                            success: function(response)
                            {
                                $('.selector-pais select').html(response).fadeIn();
                            }
                    });

                });

//para definir que funcionalidad tiene el boton submit (crear usuario)
$("#submitButton").click(function () {
    $.ajax({
            data: {
                'usuario': $("#nombre_usuario").val(),
                'rolesUser': "administrador del sistema"
            },
            url: $.KumbiaPHP.publicPath+"admin/usuarios/crear",
            type: 'post',
            dataType: "json"
        });
});
