/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

       var cropzoom = $('#crop_container').cropzoom({
            width:400,
            height:300,
            bgColor: '#CCC',
            enableRotation:true,
            enableZoom:true,
            zoomSteps:10,
            rotationSteps:10,
            selector:{        
              centered:true,
              borderColor:'blue',
              borderColorHover:'red'
            },
            image:{
                source:$("#target").attr("src"),
                width:1024,
                height:768,
                minZoom:10,
                maxZoom:150
            }
        });
        cropzoom.setSelector(45,45,200,150,true);
        $('#crop').click(function(){ 
            cropzoom.send('crop','POST',{},function(rta){
                alert(rta);
                $('.result').find('img').remove();
                var img = $('<img />').attr('src',rta);
                $('.result').find('.txt').hide().end().append(img);
                console.log($('.result'));
            });
        });
        $('#restore').click(function(){
            $('.result').find('img').remove();
            $('.result').find('.txt').show()
            cropzoom.restore();
        });

