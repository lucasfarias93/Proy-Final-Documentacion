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

$("#numero_documento").change(function () {
        $("#nombre_usuario").val("");

        $("#nombre_usuario").removeAttr("readonly")
        $("#nombre_usuario").removeAttr("disabled")

        $.ajax({
            data: {
                'dni': $("#numero_documento").val()
            },
            url: $.KumbiaPHP.publicPath+"tramitedni/buscar_ciudadano_por_documento",
            type: 'post',
            dataType: "json",
            success: function (response) {
                $("#nombre_usuario").val(response.nombres);

                $("#nombre_usuario").attr("readonly", "readonly")

            }
        });
});