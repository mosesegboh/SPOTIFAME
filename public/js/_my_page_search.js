$( document ).ready(function() {

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
    
    

var searchtype=$("#searchtype").val();

            if(searchtype=='artist')
            {
                $("#hideclaimed").removeAttr("disabled");
                $( "#hideclaimedwrap" ).show();
            }
            else
            {
                $("#hideclaimed").attr("disabled", "disabled");
                $( "#hideclaimedwrap" ).hide();
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

    if(searchtype=='artist' || searchtype=='playlist')
    {
        
        $("#requestwoutcache").removeAttr("disabled");
        $( "#requestwoutcachewrap" ).show();

        $("#followers").removeAttr("disabled");
        $( "#followerwrap" ).show();
        $( ".oursearchpart" ).show();
    }
    else
    {
        $("#requestwoutcache").attr("disabled", "disabled");
        $( "#requestwoutcachewrap" ).hide();

        $("#followers").attr("disabled", "disabled");
        $( "#followerwrap" ).hide();
        $( ".oursearchpart" ).hide();
    }

        if(searchtype=='artist' || searchtype=='track')
            {
                $("#genres").removeAttr("disabled");
                $("#genreswrap").show();
            }
            else
            {
                $("#genres").attr("disabled", "disabled");
                $("#genreswrap").hide();
            }


        if(searchtype=='artist' || searchtype=='track' || searchtype=='album')
            {
                $("#yearfrom").removeAttr("disabled");
                $("#yearto").removeAttr("disabled");
                $("#yearfromtowrap").show();
            }
            else
            {
                $("#yearfrom").attr("disabled", "disabled");
                $("#yearto").attr("disabled", "disabled");
                $("#yearfromtowrap").hide();
            }

            if(searchtype=='album')
            {
                $("#isnew").removeAttr("disabled");
                $("#isnewwrap").show();
            }
            else
            {
                $("#isnew").attr("disabled", "disabled");
                $("#isnewwrap").hide();
            }
           
          
if(parseFloat(item_count.replace(/,/g, ''))>0)
{
    $('html, body').animate({
        scrollTop: $("#searchresults").offset().top-20
    }, 500);
}

    $('#openadvsearch').on('click', function(event){
      event.preventDefault();
    
      let clicks=$(this).data('clicks');
    
      if(clicks){
          //odd
          $('#advopen').val('0');
      }
      else
      {
          //even
          $('#advopen').val('1');
      }
      $(this).data("clicks",!clicks);
    
    
    });
    
    
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
        'placeholderColor': '#666666'
      });
    
    
      $( "#searchform" ).submit(function( event ) {
    
        
        $('#defaultloading').removeClass('d-none');
        
    
        });
//important cause of cache!!
        $(window).bind("pageshow", function(event) {
            $('#defaultloading').addClass('d-none');
        });
//important cause of cache!!


        if ($('#yearfromtowrap input').length) {
                    $('#yearfrom').datepicker({
                autoclose: true,
                format: " yyyy",
                viewMode: "years",
                minViewMode: "years",
                startDate: new Date(1900, 01, 01),
                endDate:yeartostart, 
            }).on('changeDate', function (selected) {
                var startDate = new Date(selected.date.valueOf());
                $('#yearto').datepicker('setStartDate', startDate);
            }).on('clearDate', function (selected) {
                $('#yearto').datepicker('setStartDate', null);
            });
            
                    $('#yearto').datepicker({
                autoclose: true,
                format: " yyyy",
                viewMode: "years",
                minViewMode: "years",
                startDate:yearfromstart,
                endDate:new Date(), 
            }).on('changeDate', function (selected) {
                var endDate = new Date(selected.date.valueOf());
                $('#yearfrom').datepicker('setEndDate', endDate);
            }).on('clearDate', function (selected) {
                $('#yearfrom').datepicker('setEndDate', null);
            });
           
        }
          
          
        $( "#searchtype" ).change(function(event) {
            event.preventDefault();

            var currentval=$(this).val();


            if(currentval=='artist')
            {
                $("#hideclaimed").removeAttr("disabled");
                $( "#hideclaimedwrap" ).show();
            }
            else
            {
                $("#hideclaimed").attr("disabled", "disabled");
                $( "#hideclaimedwrap" ).hide();
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


            if(currentval=='artist' || currentval=='playlist')
            {
                
                $("#requestwoutcache").removeAttr("disabled");
                $( "#requestwoutcachewrap" ).show();

                $("#followers").removeAttr("disabled");
                $( "#followerwrap" ).show();
                $( ".oursearchpart" ).show();
            }
            else
            {
                $("#requestwoutcache").attr("disabled", "disabled");
                $( "#requestwoutcachewrap" ).hide();

                $("#followers").attr("disabled", "disabled");
                $( "#followerwrap" ).hide();
                $( ".oursearchpart" ).hide();
            }



            if(currentval=='artist' || currentval=='track')
            {
                $("#genres").removeAttr("disabled");
                $("#genreswrap").show();
            }
            else
            {
                $("#genres").attr("disabled", "disabled");
                $("#genreswrap").hide();
            }

            if(currentval=='artist' || currentval=='track' || currentval=='album')
            {
                $("#yearfrom").removeAttr("disabled");
                $("#yearto").removeAttr("disabled");
                $("#yearfromtowrap").show();
            }
            else
            {
                $("#yearfrom").attr("disabled", "disabled");
                $("#yearto").attr("disabled", "disabled");
                $("#yearfromtowrap").hide();
            }

            if(currentval=='album')
            {
                $("#isnew").removeAttr("disabled");
                $("#isnewwrap").show();
            }
            else
            {
                $("#isnew").attr("disabled", "disabled");
                $("#isnewwrap").hide();
            }

            
          });


          
    
    
    });