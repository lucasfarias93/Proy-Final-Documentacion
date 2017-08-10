var meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
function fechaActual() {
    var ahora = new Date();
    var mes = ahora.getMonth();
    
    var day = ahora.getDate();

    return day + " de " + meses[mes] + " del " + ahora.getFullYear();
}
$('form').on("keyup keypress", function (e) {
    var code = e.keyCode || e.which;
    if (code == 13) {
        e.preventDefault();
        return false;

    }
});
function arregla_nombres(nombre) {
    nombre = nombre.toLowerCase().trim().split(" ")
    nuevo_nombre = '';
    if (nombre != '') {
        $.each(nombre, function (index, value) {
            if (index == 0) {
                nuevo_nombre += nombre[index][0].toUpperCase() + nombre[index].slice(1)
            } else {
                nuevo_nombre += " " + nombre[index][0].toUpperCase() + nombre[index].slice(1)
            }
        });
    }
    return nuevo_nombre;
}
function arregla_apellido(apellido) {
    apellido = apellido.toUpperCase().trim().split(" ")
    nuevo_apellido = '';
    if (apellido != '') {
        $.each(apellido, function (index, value) {
            if (index == 0) {
                nuevo_apellido += apellido[index][0].toUpperCase() + apellido[index].slice(1)
            } else {
                nuevo_apellido += " " + apellido[index][0].toUpperCase() + apellido[index].slice(1)
            }
        });
    }
    return nuevo_apellido;
}
//$(function ($) {
    /*$.fn.datepicker.dates['es'] = {
        days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
        daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"],
        daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
        months: meses,
        monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
        today: "Hoy"
    }*/
//    $('.date-picker').datepicker({
//        autoclose: true,
//        format: "dd/mm/yyyy",
//        todayHighlight: true,
//        language: "es"
//    })
//});
