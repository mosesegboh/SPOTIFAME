
$( document ).ready(function() {

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });


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

    function deletegroup(current)
    {
      
        var itemid=current.closest("[id^='parent_']").data('itemid');
        var parentelement=current.closest("[id^='parent_']");


        if($('#sendingdata').data('sending')=='1')
        return;
        $('#sendingdata').data('sending','1');

        $('#defaultloading').removeClass('d-none');


        $.ajax({
        type:"POST",
        dataType: 'json',
        url:base+'admin/ajax/removegroup',
        data: {
            _token:csrf_token,
            itemid:itemid,
        },
        success:function(data){

            $('#defaultloading').addClass('d-none');
            $('#sendingdata').data('sending','0');
            
            if(data.status=='success')
            {
                
                parentelement.fadeOut(500, function(){
                
                $(this).remove();
                });
                
            }
            else {
                
            }
        }
        });

    }

    $( ".removegroupclick" ).click(function(event) {
        event.preventDefault();

        var current=$(this);

        showSwal_are_you_sure_to_delete(current,'Are you sure you\'d like to remove the group?',deletegroup);

          
    });


    if(parseFloat(item_count.replace(/,/g, ''))>0)
    {
        $('html, body').animate({
            scrollTop: $("#groups").offset().top-20
        }, 500);
    }


    $( "#currentform" ).submit(function( event ) {
    
        $('#defaultloading').removeClass('d-none');
    
        });

    //important cause of cache!!
$(window).bind("pageshow", function(event) {
    $('#defaultloading').addClass('d-none');
});
//important cause of cache!!








});