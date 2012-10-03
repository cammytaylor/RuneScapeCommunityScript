$(document).ready(function(){
    $("select").change(function () {
          id = null;
          $("select option:selected").each(function () {
                id = $(this).val();
              });
          
          $.get("_getPos.php", { cat: id },
                function(data){
                $('#pos').val(data);
            });
            
        }).change();
});