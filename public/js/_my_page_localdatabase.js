$( document ).ready(function() {

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });


    var searchtype=$("#searchtype").val();


        if(searchtype=='artist')
            {
                $("#artistclaimwrap").show();

                $("#artistswithoutgenreswrap").show();
                $("#artistswithoutgenres").removeAttr("disabled");

                $("#distributor").removeAttr("disabled");
                $("#distributorwrap").show();
                $("#genres").removeAttr("disabled");
                $("#genreswrap").show();
            }
            else
            {
                $("#artistclaimwrap").hide();

                $("#artistswithoutgenreswrap").hide();
                $("#artistswithoutgenres").attr("disabled", "disabled");

                $("#distributor").attr("disabled", "disabled");
                $("#distributorwrap").hide();
                $("#genres").attr("disabled", "disabled");
                $("#genreswrap").hide();
            }

        if(searchtype=='playlist')
            {
                $("#hidespotifyowned").removeAttr("disabled");
                $( "#hidespotifyownedwrap" ).show();
            }
            else
            {
                $("#hidespotifyowned").attr("disabled", "disabled");
                $( "#hidespotifyownedwrap" ).hide();
            }


if(parseFloat(item_count.replace(/,/g, ''))>0 && searchset=='1')
{
    $('html, body').animate({
        scrollTop: $("#searchresults").offset().top-20
    }, 500);
}



if ($("#followers").length) {
    
    $( "#fromfollowers" ).val($( "#fromfollowers" ).val().toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ","));
    $( "#tofollowers" ).val($( "#tofollowers" ).val().toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ","));

var startmin=minrangevalue;
var startmax=maxrangevalue;
var breakpoint=rangebreakpoint;

var inputfolmin='';
var inputfolmax='';

 if( followersinput!='')
{
let inputfolstrsplit=followersinput.split(";");

var inputfolmin=inputfolstrsplit[0];
var inputfolmax=inputfolstrsplit[1];

}


if(inputfolmin!='' && inputfolmax!='')
{
var startfrom=inputfolmin;
var startto=inputfolmax;

//var startfrom=rangevalues.indexOf(parseInt(inputfolmin));
//var startto=rangevalues.indexOf(parseInt(inputfolmax));
}
 else
{
var startfrom=startmin;
var startto=startmax;

//var startfrom=rangevalues.indexOf(parseInt(startmin));
//var startto=rangevalues.indexOf(parseInt(startmax));
}



function changeNumber (num) {
    var limit_1 = startmax/2; //percent
    var limit_1_max = breakpoint;

    var limit_2 = startmax; //percent
    var limit_2_min = limit_1_max;
    var limit_2_max = startmax;

    if (num < limit_1) {
        var pers = num / limit_1 * 100;
        return Math.round(limit_1_max / 100 * pers).toString().replace(/(\d{1,3}(?=(?:\d\d\d)+(?!\d)))/g, "$1" + ",");
    } else if (num >= limit_1) {
        var pers =(num - limit_1) / (limit_2 - limit_1) * 100;
        //return Math.round(limit_2_min + ((limit_2_max - limit_2_min) / 100 * pers)).toString().replace(/(\d{1,3}(?=(?:\d\d\d)+(?!\d)))/g, "$1" + ",");
        return Math.round(limit_2_min + ((limit_2_max - limit_2_min) / 100 * pers)).toString().replace(/(\d{1,3}(?=(?:\d\d\d)+(?!\d)))/g, "$1" + ",");
    }

}


function changeNumberBack (num) {
    var limit_1 = startmax/2; //percent
    var limit_1_max = breakpoint;

    var limit_2 = startmax; //percent
    var limit_2_min = limit_1_max;
    var limit_2_max = startmax;

    if (num < limit_1_max) {
        return Math.round((num/limit_1_max)*limit_1);


    } else if (num >= limit_1_max) {

        return Math.round(((num-limit_1_max)*(limit_2-limit_1))/(limit_2-breakpoint))+limit_1;


    }

}


    $("#followers").ionRangeSlider({
      type: "double",
      min: startmin,
      max: startmax,
      from: startfrom,
      to: startto,
      grid: true,
      //values: rangevalues,
      prettify: changeNumber,
      onChange: function (data) {
        // Called every time handle position is changed

        $('#fromfollowers').val(data.from_pretty);
        $('#tofollowers').val(data.to_pretty);
    },
    });

    var followerslider = $("#followers").data("ionRangeSlider");


    $('#tofollowers').on('focusin', function(){
        
        $(this).data('val', $(this).val());
    });

    $('#fromfollowers').on('focusin', function(){
        
        $(this).data('val', $(this).val());
    });

    

    $('#fromfollowers').on('focusout', function(){
        
        var currentval=$(this).val();
        var currentvalInt=parseFloat(currentval.replace(/,/g, ''));
        var toValInt=parseFloat($( "#tofollowers" ).val().replace(/,/g, ''));
     
        if(currentvalInt>toValInt)
        {
            $(this).val($(this).data('val'));
        return;
        }
        
            $(this).data('val', currentval);
    });

    $('#tofollowers').on('focusout', function(){
        
        var currentval=$(this).val();
        var currentvalInt=parseFloat(currentval.replace(/,/g, ''));
        var fromValInt=parseFloat($( "#fromfollowers" ).val().replace(/,/g, ''));
     
        if(currentvalInt<fromValInt)
        {
            $(this).val($(this).data('val'));
        return;
        }
        
            $(this).data('val', currentval);

    });


    $( "#fromfollowers" ).on('input propertychange',function(event) {
        event.preventDefault();
        
        var currentval=$(this).val();
        var currentvalInt=parseFloat(currentval.replace(/,/g, ''));
        var toValInt=parseFloat($( "#tofollowers" ).val().replace(/,/g, ''));
        var inputval=changeNumberBack(parseFloat(currentval.replace(/,/g, '')));

        if(isNaN(inputval) || inputval<startmin || inputval>startmax || currentvalInt>toValInt)
        {
        return;
        }
        
        $(this).data('val', currentval);
            followerslider.update({
                from: inputval
            });

        
    });

    

    $( "#tofollowers" ).on('input propertychange',function(event) {
        event.preventDefault();

        var currentval=$(this).val();
        var currentvalInt=parseFloat(currentval.replace(/,/g, ''));
        var fromValInt=parseFloat($( "#fromfollowers" ).val().replace(/,/g, ''));
        var inputval=changeNumberBack(parseFloat(currentval.replace(/,/g, '')));
     
        if(isNaN(inputval) || inputval<startmin || inputval>startmax || currentvalInt<fromValInt)
        {
        return;
        }
        

        $(this).data('val', currentval);
            followerslider.update({
                to: inputval
            });

        
    });

    

    $( "#fromfollowers" ).inputmask({
        'alias': 'decimal',
        rightAlign: false,
        'groupSeparator': ',',
        'autoGroup': true
      });

    $( "#tofollowers" ).inputmask({
        'alias': 'decimal',
        rightAlign: false,
        'groupSeparator': ',',
        'autoGroup': true
      });

  }

    // Jquery Tag Input Starts
    $('#genres').tagsInput({
        'width': '100%',
        'height': '75%',
        'interactive': true,
        'defaultText': 'Add Genres...',
        'removeWithBackspace': true,
        'minChars': 0,
        'maxChars': 40, // if not provided there is no limit
        'placeholderColor': '#666666',
        onAddTag: function () {
            
            if ($('#artistswithoutgenres').prop('checked')) {
                $('#artistswithoutgenres').prop('checked', false);
            }
            
        }
      });

    $( "#searchform" ).submit(function( event ) {
    
    
        $('#defaultloading').removeClass('d-none');
    
    
        });
//important cause of cache!!
$(window).bind("pageshow", function(event) {
    $('#defaultloading').addClass('d-none');
});
//important cause of cache!!

        


    $( "#searchtype" ).change(function(event) {
        event.preventDefault();

        var currentval=$(this).val();


    if(currentval=='artist')
            {
                $("#artistclaimwrap").show();

                $("#artistswithoutgenreswrap").show();
                $("#artistswithoutgenres").removeAttr("disabled");

                $("#distributor").removeAttr("disabled");
                $("#distributorwrap").show();
                $("#genres").removeAttr("disabled");
                $("#genreswrap").show();
            }
            else
            {
                $("#artistclaimwrap").hide();

                $("#artistswithoutgenreswrap").hide();
                $("#artistswithoutgenres").attr("disabled", "disabled");

                $("#distributor").attr("disabled", "disabled");
                $("#distributorwrap").hide();
                $("#genres").attr("disabled", "disabled");
                $("#genreswrap").hide();
            }


    if(currentval=='playlist')
            {
                $("#hidespotifyowned").removeAttr("disabled");
                $( "#hidespotifyownedwrap" ).show();
            }
            else
            {
                $("#hidespotifyowned").attr("disabled", "disabled");
                $( "#hidespotifyownedwrap" ).hide();
            }       



        });



        if(searchtype=='artist')
            {

    
showSwal_confirm_morethanone_refresh = function(firstpageelements) {


    var warningrefresh='';
        if(parseFloat(item_count.replace(/,/g, ''))>1000)
        {
            var warningrefresh='<p class="warn">WARNING: if you want to refresh ALL results then it might take very long, since it is more than 1000 results!</p>';
        }

     var topdescription='<p class="text-muted">Here you can set how you would like to refresh the resultset.</p>'+warningrefresh;
     
     var adduglyselectclass="";
     if (navigator.userAgent.match('iPad|iPhone|iPod')) {
        var adduglyselectclass=" uglyselect";
        
    }
    
    Swal.fire({
        html: `<div class="refreshallartistswrap">`+topdescription+`

        <div class="card-body">
        <div class="form-group row">
            <label for="refreshtype" class="lineheight24rem">Refresh amount:</label>
                            <div class="col-sm-9">
                                <select class="form-control border-secondary refreshtype`+adduglyselectclass+`" name="refreshtype" autocomplete="off">
                                    <option value="firstpage" selected="selected">First page only</option>
                                    <option value="allpages">All results</option>
                                </select>
                            </div>
        </div>

                <div id="refreshbuttonswrap" class="position-relative p-4 border border-secondary text-left">

                 <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Refresh types</span>
                        
                                <div>
                                <div class="form-check d-inline-block" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Already found claimed artists.">
                                <label class="form-check-label successful-bg">
                                <input name="claimedrefresh" type="checkbox" class="form-check-input claimedrefresh" autocomplete="off">
                                              Claimed
                                      <i class="input-helper"></i><i class="input-helper"></i></label>
                                </div>
                        
                            </div>
                        
                                <div>
                                <div class="form-check d-inline-block" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists which are set &quot;claimed&quot; by us.">
                                    <label class="form-check-label wesetitclaimed-bg">
                                    <input name="claimed2refresh" type="checkbox" class="form-check-input claimed2refresh" autocomplete="off">
                                               Claimed (changed)
                                          <i class="input-helper"></i><i class="input-helper"></i></label>
                                    </div>
                        
                                </div>
                        
                                <div>
                                    <div class="form-check d-inline-block" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Already found unclaimed artists.">
                                        <label class="form-check-label problem-bg">
                                        <input name="notclaimedrefresh" type="checkbox" class="form-check-input notclaimedrefresh" autocomplete="off">
                                                      Not claimed
                                              <i class="input-helper"></i><i class="input-helper"></i></label>
                                        </div>
                                    
                                    </div>
                        
                        
                                    <div>
                                    <div class="form-check d-inline-block" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Artists which haven't been checked by our script and thus we don't know whether they are claimed or not.">
                                            <label class="form-check-label waiting-bg">
                                            <input name="unknownrefresh" type="checkbox" class="form-check-input unknownrefresh" checked="checked" autocomplete="off">
                                                       Unknown
                                                  <i class="input-helper"></i><i class="input-helper"></i></label>
                                            </div>
                        
                                        </div>
                        
                </div>

        </div>
            
        </div>
        `,
        
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        showCancelButton: true,
        confirmButtonColor: '#3f51b5',
        cancelButtonColor: '#ff4081',
        customClass: {
            popup:'card',
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-danger',
          },
        preConfirm: function() {
            return new Promise((resolve, reject) => {
                // get your inputs using their placeholder or maybe add IDs to them

                if ($('input[name="claimedrefresh"]').is(":checked")) 
                claimedrefresh=1;
                    else
                    claimedrefresh=0;

                    if ($('input[name="claimed2refresh"]').is(":checked")) 
                    claimed2refresh=1;
                    else
                    claimed2refresh=0;

                    if ($('input[name="notclaimedrefresh"]').is(":checked")) 
                    notclaimedrefresh=1;
                    else
                    notclaimedrefresh=0;

                    if ($('input[name="unknownrefresh"]').is(":checked")) 
                    unknownrefresh=1;
                    else
                    unknownrefresh=0;

                    if ($('input[name="artistswithoutgenres"]').is(":checked")) 
                    artistswithoutgenres=1;
                    else
                    artistswithoutgenres=0;


                resolve({
                    refreshtype: $('.refreshtype').find('option:selected').val(),
                    claimedrefresh: claimedrefresh,
                    claimed2refresh: claimed2refresh,
                    notclaimedrefresh: notclaimedrefresh,
                    unknownrefresh: unknownrefresh,
                    artistswithoutgenres: artistswithoutgenres,
                });

                // maybe also reject() on some condition
            });
        }
    }).then((data) => {
        // your input data object will be usable from here
        
        if(data.value)
        {

            //console.log(data,firstpageelements);
            sendMultipleClaimRequest(data,firstpageelements)



        }
        else
        {
            return;
        }
        
        


    });

}

        function sendMultipleClaimRequest(inputdata,firstpageelements)
        {

if($('#sendingdata').data('sending')=='1')
return;
$('#sendingdata').data('sending','1');

$('#defaultloading').removeClass('d-none');


            $.ajax({
                type:"POST",
                 dataType: 'json',
                 url:base+'admin/ajax/getmultipleclaimstate',
                 data: {
                    _token:csrf_token,
                    inputdata:inputdata,
                    firstpageelements:firstpageelements,
                    search_string:search_string
                },
                 success:function(data){

                    $('#defaultloading').addClass('d-none');
                    $('#sendingdata').data('sending','0');
                    
                    if(data.status=='success')
                    {
                    
                        if(inputdata.value.refreshtype=='firstpage')
                        {
                            var returnarray=data.returnarray;
                            for(var x=0; x < returnarray.length; x++){

                                $('#parent_'+returnarray[x].itemid).data('claimed',returnarray[x].claimed)
                                $('#parent_'+returnarray[x].itemid).removeClass('waiting-bg');
                                $('#parent_'+returnarray[x].itemid).removeClass('successful-bg');
                                $('#parent_'+returnarray[x].itemid).removeClass('problem-bg');
                                $('#parent_'+returnarray[x].itemid).removeClass('wesetitclaimed-bg');

                                if(returnarray[x].claimed=='0')
                                $('#parent_'+returnarray[x].itemid).addClass('waiting-bg');
                                else if(returnarray[x].claimed=='1')
                                $('#parent_'+returnarray[x].itemid).addClass('successful-bg');
                                else if(returnarray[x].claimed=='2')
                                $('#parent_'+returnarray[x].itemid).addClass('problem-bg');
                                else if(returnarray[x].claimed=='3')
                                $('#parent_'+returnarray[x].itemid).addClass('wesetitclaimed-bg');
                              

                            }

                            
                              $('html, body').animate({
                                 scrollTop: $('#searchresults').find('.resulttable').offset().top-20
                                }, 500);
                                
                        }
                        else
                        {
                       window.location.reload(false);
                        }
                      
                        
                    }
                    else
                    {
                        
                        
                    }
                }
                });
        }


        $( ".refreshmorethanoneclaimstate" ).click(function(event) {
            event.preventDefault();

var current=$(this);


var firstpageelements = {};
firstpageelements.itemids = [];

$('.resulttable').find("[id^='parent_']").each(function(index, value) {
    firstpageelements.itemids.push($(this).data('itemid'));
});


showSwal_confirm_morethanone_refresh(firstpageelements);
   
            
        });

        
          

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


$( ".downloadartistresultset" ).click(function(event) {
        event.preventDefault();

    if($('#sendingdata').data('sending')=='1')
return;
$('#sendingdata').data('sending','1');

$('#defaultloading').removeClass('d-none');


                
            $.ajax({
                type:"POST",
                 dataType: 'json',
                 url:base+'admin/ajax/downloadartistresultset',
                 data: {
                    _token:csrf_token,
                    search_string_more:search_string_more,
                },
                 success:function(data){

                    $('#defaultloading').addClass('d-none');
                    $('#sendingdata').data('sending','0');
                    
                    if(data.status=='success')
                    {
                    
                        window.location.href = data.dlurl;
                      
                        
                    }
                    else
                    {
                        
                        
                    }
                }
                });
});


$("#artistswithoutgenres").click(function(){
    
    var current=$(this);
    if(current.is(':checked')){

        $('#genres').importTags('');
        
    } else {

        
    }
});

});