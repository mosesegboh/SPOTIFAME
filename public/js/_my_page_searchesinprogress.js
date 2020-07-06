$( document ).ready(function() {


  $('body').tooltip({
    selector: '[data-toggle="tooltip"]'
});

    
$('#turnmainonoff').change(function() {
  event.preventDefault();

  var current=$(this);
 if (current.prop('checked'))
 var state='1';
 else
 var state='0';

 
if($('#sendingdata').data('sending')=='1')
return;
$('#sendingdata').data('sending','1');

$('#defaultloading').removeClass('d-none');

var table=current.closest("[id^='parent_']").data('db-table');
var row=current.closest("[id^='parent_']").data('db-row');
var change='update';
var sentvalue=state;
var itemid=current.closest("[id^='parent_']").data('itemid');


          $.ajax({
              type:"POST",
               dataType: 'json',
               url:base+'admin/ajax/simpleupdatefield',
               data: {
                  _token:csrf_token,
                  table:table,
                  row:row,
                  change:change,
                  sentvalue:sentvalue,
                  itemid:itemid
              },
               success:function(data){

                  $('#defaultloading').addClass('d-none');
                  $('#sendingdata').data('sending','0');
                  
                  if(data.status=='success')
                  {
                  
                     
                  }
                  else
                  {
                      
                  }
              }
              });



});

    $( "#processedshow,#waitingshow,#processingshow,#problematicshow" ).change(function(event) {
        event.preventDefault();

        $('#defaultloading').removeClass('d-none');
        $('#inprogressform').submit();


    });
    
//important cause of cache!!
    $(window).bind("pageshow", function(event) {
      $('#defaultloading').addClass('d-none');
  });
//important cause of cache!!


    $.fn.editable.defaults.mode = 'inline';
      $.fn.editableform.buttons =
        '<button type="submit" class="btn btn-primary btn-sm editable-submit">' +
        '<i class="fa fa-fw fa-check"></i>' +
        '</button>' +
        '<button type="button" class="btn btn-default btn-sm editable-cancel">' +
        '<i class="fa fa-fw fa-times"></i>' +
        '</button>';

    $('.inprogress').editable({
        source: [{
            value: 0,
            text: 'Processed!'
          },
          {
            value: 1,
            text: 'Waiting (queued)...'
          },
          /*{
            value: 2,
            text: 'Now Processing...'
          },*/
          {
            value: 10,
            text: 'Paused!'
          }
        ],
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
        title: 'Change state',
        success: function(response, newValue) {
            if(response.status == 'success') 
            {
                $(this).closest("[id^='parent_']").removeClass('successful-bg waiting-bg processing-bg problem-bg');
                if(newValue=='0')
                $(this).closest("[id^='parent_']").addClass('successful-bg');
                else if(newValue=='1')
                $(this).closest("[id^='parent_']").addClass('waiting-bg');
                else if(newValue=='2')
                $(this).closest("[id^='parent_']").addClass('processing-bg');
                else if(newValue=='10')
                $(this).closest("[id^='parent_']").addClass('problem-bg');


                $(this).data('value',newValue);
                //return response.msg;
            }
            else if(response.status == 'failed') 
            {
                return response.msg;
            }
        }
      });




}); 