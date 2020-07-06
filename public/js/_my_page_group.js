
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

      if ($(".color-picker").length) {

        $Picker = [];
        var pickerobj = {};
        

        $(".color-picker").each(function() {
            
        $this = $(this);
        $ID = "picker" + $this.closest("[id^='parent_']").data('itemid');
        $Picker.push($ID);
        $this.attr('id',$ID);
        $currentcolour=$this.closest('.pickerfield').data('value');

        // Simple example, see optional options for more configuration.
        pickerobj[$ID]= new Pickr({
                el: '#'+$ID,
                theme: 'classic', // or 'monolith', or 'nano'
                default: $currentcolour,
                swatches: [
                    'rgba(244, 67, 54, 1)',
                    'rgba(233, 30, 99, 1)',
                    'rgba(156, 39, 176, 1)',
                    'rgba(103, 58, 183, 1)',
                    'rgba(63, 81, 181, 1)',
                    'rgba(33, 150, 243, 1)',
                    'rgba(3, 169, 244, 1)',
                    'rgba(0, 188, 212, 1)',
                    'rgba(0, 150, 136, 1)',
                    'rgba(76, 175, 80, 1)',
                    'rgba(139, 195, 74, 1)',
                    'rgba(205, 220, 57, 1)',
                    'rgba(255, 235, 59, 1)',
                    'rgba(255, 193, 7, 1)'
                ],

                components: {

                    // Main components
                    preview: true,
                    opacity: true,
                    hue: true,

                    // Input / output Options
                    interaction: {
                        hex: true,
                        rgba: false,
                        hsla: false,
                        hsva: false,
                        cmyk: false,
                        input: true,
                        clear: false,
                        save: true,
                        cancel:true,
                    }
                }
                
            });
            $this.on('focus click',function () {
                console.log('clicked');
            })
           
          });

          $Picker.forEach(function (index) {
            
            pickerobj[index].on('save',function (color, instance) {

                index.replace('picker','');
                //console.log('save', color.toHEXA(), index,instance,);

                var itemid=$("#parent_"+index.replace('picker','')).data('itemid');
                var tablename=$("#parent_"+index.replace('picker','')).data('db-table');
                var dbrow=$("#parent_"+index.replace('picker','')).find('.pickerfield').data('db-row');
                var sentvalue=pickerobj[index].getSelectedColor().toHEXA().toString();

                sendchangecolour(sentvalue,itemid,dbrow,tablename,'update');

                pickerobj[index].hide();

               

                
            });

            pickerobj[index].on('cancel',function (color, instance) {


                pickerobj[index].hide();

            });
    
        });

        function sendchangecolour(sentvalue,itemid,dbrow,tablename,change)
        {

            
            if($('#sendingdata').data('sending')=='1')
            return;
            $('#sendingdata').data('sending','1');

            $('#defaultloading').removeClass('d-none');


            $.ajax({
                type:"POST",
                 dataType: 'json',
                 url:base+'admin/ajax/simpleupdatefield',
                 data: {
                    _token:csrf_token,
                    row:dbrow,
                    table:tablename,
                    change:change,
                    sentvalue:sentvalue,
                    itemid:itemid,
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


        }


      }
      

      if ($(".claimfield").length) {

        
      $('.claimfield').editable({
        source: [{
            value: 1,
            text: 'Claimed'
          },
          {
            value: 2,
            text: 'Not Claimed'
          },
          {
            value: 3,
            text: 'Claimed (changed)'
          },
          {
            value: 0,
            text: 'Unknown'
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
                $(this).closest("[id^='parent_']").removeClass('successful-bg waiting-bg wesetitclaimed-bg problem-bg');
                if(newValue=='1')
                $(this).closest("[id^='parent_']").addClass('successful-bg');
                else if(newValue=='2')
                $(this).closest("[id^='parent_']").addClass('problem-bg');
                else if(newValue=='3')
                $(this).closest("[id^='parent_']").addClass('wesetitclaimed-bg');
                else if(newValue=='0')
                $(this).closest("[id^='parent_']").addClass('waiting-bg');


                $(this).data('value',newValue);
                //return response.msg;
            }
            else if(response.status == 'failed') 
            {
                return response.msg;
            }
        }
      });
    }
    

 

    if(parseFloat(item_count.replace(/,/g, ''))>0 && searchset=='1')
{
    $('html, body').animate({
        scrollTop: $("#searchresults").offset().top-20
    }, 500);
}




});