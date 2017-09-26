//Segun el tipo de libro hace la busqueda de difrentes parentescos
$("#tipolibro_id").change(function () {
    if (($("#tipolibro_id option:selected").val() != "")) {

        $.ajax({
            data: {
                'tipolibro': $("#tipolibro_id option:selected").val()
            },
            url: $.KumbiaPHP.publicPath + "ciudadano/index/buscar_parentesco_tipolibro",
            type: 'post',
            dataType: "json",
            success: function (response) {
                html = "";
                $.each(response, function (i, row) {
                    html += '<p>'
                    html += '<input class="with-gap" name="group3" value="' + row.id + '" type="radio" id="'+ row.nombreparentesco +'_radio">'
                    html += '<label for="'+ row.nombreparentesco +'_radio">' + row.nombreparentesco + '</label>'
                    html += '</p>'
                })
                $("#parentescos").html(html);
            }
        });
    }
});