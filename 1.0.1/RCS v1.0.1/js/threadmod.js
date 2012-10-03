$(document).ready(function(){
    isEditing = false;
    
    $(document).on('dblclick', 'img[id|="thread"]', function (){
        
        if(isEditing == false){
            //set editing mode to true, so they can't use this feature until they finish the edit they're on
            isEditing = true;

            parent = $(this).parent();
            data = parent.html();
            id = $(this).attr('id').split('-')[1];

            //editing mode is true
            isEditing = true;

            parent.html(
                '<input type="text" id="'+ id +'" value="'+ parent.attr('title') +'" size="85">&nbsp;&nbsp;<img src="../img/forum/cancel.png" id="deny">&nbsp;<img src="../img/forum/ok.png" id="accept">'
            );
        }
        
    });
    
    
    //accept
    $(document).on('click', 'img#accept', function (){
        //get new title
        var title = $('input#'+ id).attr('value');
        
        //restore the parent
        parent.html(data);
        
        //anchor tag
        var a = $('td#td-'+ id +' a').eq(0);
        
        //now let's change the link to the newly created title
        a.text(title);
        
        $(a).ajaxSuccess(function(){
            $('mes#mes-' + id).css('color', 'yellow');
            $('mes#mes-' + id).fadeToggle(2500).delay(2500).fadeToggle(2500);
        });
        
        /*
         * send the request to threadmod.php to update the title
         * then display the success message if successfully changed
         */
        $.ajax({
            url : 'actions/threadmod.php',
            type : 'post',
            data : { id : id, title : title }
        });
        
        //set editing mode to false
        isEditing = false;
    });

    //cancel
    $(document).on('click', 'img#deny', function(){
        
        //restore the parent
        parent.html(data);
        
        //set editing mode to false
        isEditing = false;
    });
    
    
});