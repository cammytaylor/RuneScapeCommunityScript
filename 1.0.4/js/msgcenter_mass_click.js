$(document).ready(function(){
    var x = 0;
    var rec = $('#receiver');
    $('#mass').change(function(){
        
        if(x == 0){
            rec.attr('disabled', 'disabled');
            rec.css('background-color', 'red');
            rec.css('color', 'black');
            x = 1;
        }else{
            rec.removeAttr('disabled');
            rec.css('background-color', 'black');
            rec.css('color', 'white');
            x = 0;
        }
        rec.attr('value', '');
    });
});