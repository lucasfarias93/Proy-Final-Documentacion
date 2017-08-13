/*!
 * Materialize v0.99.0 (http://materializecss.com)
 * Copyright 2014-2017 Materialize
 * MIT License (https://raw.githubusercontent.com/Dogfalo/materialize/master/LICENSE)
 */
 function this_is_a_link(){
  console.log("Link clicked");
 }

$('#card-element').on('click',function(){
    $(this).hide();
});

$('#TAB-1').on('click',function(){
    console.log("First button clicked");
    $('#card-element').show();
})

$('.button_submit').on('click',function(){
    alert("HOLIIIIIIIIIIIIIIIIIIIIS");
});

$(".button-collapse").sideNav();