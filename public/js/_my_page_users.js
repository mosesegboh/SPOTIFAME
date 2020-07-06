$( document ).ready(function() {

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });


    var maintype=$("#maintype").val();


    if(maintype=='user')
        {

            $("#subtype").removeAttr("disabled");
            $("#subtypewrap").show();
            
        }
        else
        {
           
            $("#subtype").attr("disabled", "disabled");
            $("#subtypewrap").hide();
        }


        $( "#maintype" ).change(function(event) {
            event.preventDefault();
    
            var currentval=$(this).val();
    
    
            if(currentval=='user')
            {
    
                $("#subtype").removeAttr("disabled");
                $("#subtypewrap").show();
              
            }
            else
            {
               
                $("#subtype").attr("disabled", "disabled");
                $("#subtypewrap").hide();
            }

        }); 



        if(parseFloat(item_count.replace(/,/g, ''))>0 && searchset=='1')
{
    $('html, body').animate({
        scrollTop: $("#users").offset().top-20
    }, 500);
}



$.fn.editable.defaults.mode = 'inline';
    $.fn.editableform.buttons =
      '<button type="submit" class="btn btn-primary btn-sm editable-submit">' +
      '<i class="fa fa-fw fa-check"></i>' +
      '</button>' +
      '<button type="button" class="btn btn-default btn-sm editable-cancel">' +
      '<i class="fa fa-fw fa-times"></i>' +
      '</button>';
      
    $('.notefield').editable({
        showbuttons: 'bottom',
        url: base+'admin/ajax/simpleupdatefield',
        params: function(params) {
            //originally params contain pk, name and value
            params._token = csrf_token;
            params.row=$(this).data('db-row');
            params.table=$(this).closest("[id^='parent_']").data('db-table');
            params.change='update';
            params.sentvalue=params.value;
            params.itemid=$(this).closest("[id^='parent_']").data('itemid');
            params.oldvalue=$(this).data('value');
            return params;
        },
        title: 'Enter notes',
        success: function(response, newValue) {
            if(response.status == 'success') 
            {
                
            }
            else if(response.status == 'failed') 
            {
                return response.msg;
            }
        }
      });




}); 