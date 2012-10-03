$(document).ready(function(){
    search = $('div[class="optthree"]');
    
    $(search).each(function(index){
        //get the category type
        var cat = search.eq(index).attr('id');
        
        $(document).on('click', 'div#'+ cat +'_title', function(){
                //show all the options
                $('div[id|="'+ cat +'"]').each(function(){
                    $(this).fadeTo('slow', 1);
                });
        });
    });
    
});