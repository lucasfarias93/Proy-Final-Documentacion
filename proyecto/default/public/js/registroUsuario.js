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
$("#provincia").change(function() {
    console.log($(this).find(':selected').text());
    console.log($(this).find(':selected').val());
    $.ajax({
            data: { 
                'provincia': $(this).find(':selected').val()
            },
            type: "POST",
            url: $.KumbiaPHP.publicPath+"departamento/Depto_segun_provincia",
            success: function(response) {
                var jsonObj = JSON.parse(response);
                alert(jsonObj.items.length);

                $('#select_dpto')
                    .find('option')
                    .remove()
                    .end()
                    .append('<option disabled value="option">Seleccione</option>')
                    .val('whatever')
                ;

                $.each(jsonObj.items, function(id,value) {
                    $("#select_dpto").append('<option value="'+id+'">'+value.nombredepartamento+'</option>');
                })
                $("#select_dpto").material_select();
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
