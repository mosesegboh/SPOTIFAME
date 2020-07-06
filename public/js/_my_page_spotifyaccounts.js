$( document ).ready(function() {

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });


    function deletemanager(current)
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
        url:base+'admin/ajax/removemanager',
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

    function deleteartist(current)
    {
      
        var itemid=current.closest("[id^='parent_']").data('itemid');
        var artistid=current.closest("[id^='parent_']").data('artistid');
        var parentelement=current.closest("[id^='parent_']");


        if($('#sendingdata').data('sending')=='1')
        return;
        $('#sendingdata').data('sending','1');

        $('#defaultloading').removeClass('d-none');


        $.ajax({
        type:"POST",
        dataType: 'json',
        url:base+'admin/ajax/removeartist',
        data: {
            _token:csrf_token,
            itemid:itemid,
            artistid:artistid
        },
        success:function(data){

            $('#defaultloading').addClass('d-none');
            $('#sendingdata').data('sending','0');
            
            if(data.status=='success')
            {
                
                parentelement.find('.connectartist').html('Add Artist');

                parentelement.find('.theartistnamewrap').html('');

                parentelement.find('.changeartistpick').html('Add Artist\'s Pick');
        
                
                
            }
            else {
                
            }
        }
        });

    }

    $( ".removemanagerclick" ).click(function(event) {
        event.preventDefault();

        var current=$(this);

        showSwal_are_you_sure_to_delete(current,'Are you sure you\'d like to remove the manager account?',deletemanager);

        
    });

    $( ".removeartistclick" ).click(function(event) {
        event.preventDefault();

        var current=$(this);

        showSwal_are_you_sure_to_delete(current,'Are you sure you\'d like to remove the artist?',deleteartist);

        
    });


    $('.turnonoffartist').change(function(event) {
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
      
      var table='spotify_accounts_auth_realartists';
      var row='active';
      var change='update';
      var sentvalue=state;
      var itemid=current.closest("[id^='parent_']").data('realartistid');
      
      
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

    if(parseFloat(item_count.replace(/,/g, ''))>0 && searchset=='1')
    {
        $('html, body').animate({
            scrollTop: $("#accounts").offset().top-20
        }, 500);
    }

    $(document).on("click",".addstraightclick",function(event) {
        event.preventDefault();
    
        var current=$(this);

var successdiv=current.closest('.currentchangerwrap').find('.successdiv');
var warningdiv=current.closest('.currentchangerwrap').find('.warningdiv');

var addpassinput=current.closest('.currentchangerwrap').find('.addpassinput').val();
var manageremail=current.closest('.currentchangerwrap').find('.manageremail').val();

if($('#sendingdata').data('sending')=='1')
       return;
    $('#sendingdata').data('sending','1');


    successdiv.html('');
    warningdiv.html('');
    $('#defaultloading').removeClass('d-none');

    successdiv.addClass('d-none');
    warningdiv.addClass('d-none');


    $.ajax({
        type:"POST",
         dataType: 'json',
         url:base+'admin/ajax/addstraightaccount',
         data: {
            _token:csrf_token,
            addpassinput:addpassinput,
            manageremail:manageremail

        },
         success:function(data){

            $('#defaultloading').addClass('d-none');
            $('#sendingdata').data('sending','0');
            
            if(data.status=='success')
            {
                
                if(typeof data.msg!=="undefined")
                {
                    successdiv.html(data.msg);
                    successdiv.removeClass('d-none');
                    window.location.reload(false);
                }
                
            }
            else if(data.status=='failed')
            {  
                
                if(typeof data.msg!=="undefined")
                    {
                        warningdiv.html(data.msg);
                        warningdiv.removeClass('d-none');
                    }
                    else{
                       
                        
                    }
                    
            }
            else
            {
                
              
                  
                   
            }
        }
        });
});

    $(document).on("click",".showpasswordclick",function(event) {
        event.preventDefault();
    
        var current=$(this);
        var itemid=current.data('itemid');

var showpassdiv=current.closest('.currentchangerwrap').find('.showpassdiv');
var warningdiv=current.closest('.currentchangerwrap').find('.warningdiv');

if($('#sendingdata').data('sending')=='1')
       return;
    $('#sendingdata').data('sending','1');


    showpassdiv.html('');
    warningdiv.html('');
    $('#defaultloading').removeClass('d-none');

    showpassdiv.addClass('d-none');
    warningdiv.addClass('d-none');


    $.ajax({
        type:"POST",
         dataType: 'json',
         url:base+'admin/ajax/showpassword',
         data: {
            _token:csrf_token,
            itemid:itemid

        },
         success:function(data){

            $('#defaultloading').addClass('d-none');
            $('#sendingdata').data('sending','0');
            
            if(data.status=='success')
            {
                
                if(typeof data.passwd!=="undefined")
                {
                    showpassdiv.html(data.passwd);
                    showpassdiv.removeClass('d-none');
                }
                
            }
            else if(data.status=='failed')
            {  
                
                if(typeof data.msg!=="undefined")
                    {
                        warningdiv.html(data.msg);
                        warningdiv.removeClass('d-none');
                    }
                    else{
                       
                        
                    }
                    
            }
            else
            {
                
              
                  
                   
            }
        }
        });
});

    $(document).on("click",".getartistpickclick",function(event) {
        event.preventDefault();

        
var current=$(this);
var itemid=current.data('itemid');

var warningdiv=current.closest('.getcurrentpickwrap').find('.warningdiv');


   if($('#sendingdata').data('sending')=='1')
       return;
    $('#sendingdata').data('sending','1');

    warningdiv.html('');
    $('#defaultloading').removeClass('d-none');

    warningdiv.addClass('d-none');

        $.ajax({
            type:"POST",
             dataType: 'json',
             url:base+'admin/ajax/getartistpicksimple',
             data: {
                _token:csrf_token,
                itemid:itemid
    
            },
             success:function(data){
    
                $('#defaultloading').addClass('d-none');
                $('#sendingdata').data('sending','0');
                
                if(data.status=='success')
                {
                    
                    window.location.reload(false);
                    
                }
                else if(data.status=='failed')
                {  
                    
                        if(typeof data.msg!=="undefined")
                        {
                            warningdiv.html(data.msg);
                            warningdiv.removeClass('d-none');
                        }
                        else{
                            Swal.close();
                            
                        }
                        
                }
                else
                {
                    
                    Swal.close();
                      
                       
                }
            }
            });


});

    $(document).on("click",".changepasswordclick",function(event) {
        event.preventDefault();
    
var current=$(this);
var itemid=current.data('itemid');

 var thingstr=current.closest('.currentchangerwrap').find('.thingstrinput').val();
 
 var warningdiv=current.closest('.currentchangerwrap').find('.warningdiv');



    if($('#sendingdata').data('sending')=='1')
        return;
     $('#sendingdata').data('sending','1');

     warningdiv.html('');
     $('#defaultloading').removeClass('d-none');

     warningdiv.addClass('d-none');

    $.ajax({
        type:"POST",
         dataType: 'json',
         url:base+'admin/ajax/changething',
         data: {
            _token:csrf_token,
            itemid:itemid,
            thingstr:thingstr,

        },
         success:function(data){

            $('#defaultloading').addClass('d-none');
            $('#sendingdata').data('sending','0');
            
            if(data.status=='success')
            {
                
                window.location.reload(false);
                
            }
            else if(data.status=='failed')
            {  
                
                    if(typeof data.msg!=="undefined")
                    {
                        warningdiv.html(data.msg);
                        warningdiv.removeClass('d-none');
                    }
                    else{
                        Swal.close();
                        
                    }
                    
            }
            else
            {
                
                Swal.close();
                  
                   
            }
        }
        });


});


$(document).on("click",".changeartistpickclick",function(event) {
    event.preventDefault();
    var current=$(this);
    var itemid=current.data('itemid');
    
     var artistpick=current.closest('.currentchangerwrap').find('.artistpick').val();

     var requestchange='0';
if (current.closest('.currentchangerwrap').find('.requestchange').is(':checked')) {

     var requestchange=current.closest('.currentchangerwrap').find('.requestchange').val();
}
  

     var warningdiv=current.closest('.currentchangerwrap').find('.warningdiv');
    
     if($('#sendingdata').data('sending')=='1')
     return;
  $('#sendingdata').data('sending','1');

  warningdiv.html('');
  $('#defaultloading').removeClass('d-none');

  warningdiv.addClass('d-none');

  $.ajax({
    type:"POST",
     dataType: 'json',
     url:base+'admin/ajax/changeartistpick',
     data: {
        _token:csrf_token,
        itemid:itemid,
        artistpick:artistpick,
        requestchange:requestchange

    },
     success:function(data){

        $('#defaultloading').addClass('d-none');
        $('#sendingdata').data('sending','0');
        
        if(data.status=='success')
        {
            
            window.location.reload(false);
            
        }
        else if(data.status=='failed')
        {  
            
                if(typeof data.msg!=="undefined")
                {
                    warningdiv.html(data.msg);
                    warningdiv.removeClass('d-none');
                }
                else{
                    Swal.close();
                    
                }
                
        }
        else
        {
            
            Swal.close();
              
               
        }
    }
    });



});

$(document).on("click",".changeartistclick",function(event) {
        event.preventDefault();
    
var current=$(this);
var itemid=current.data('itemid');

var oldartistid=$("#parent_"+itemid).data('artistid');

 var artistid=current.closest('.artistchangewrap').find('.artistchangeinput').val();

 var warningdiv=current.closest('.artistchangewrap').find('.warningdiv');

 if (artistid=='' || artistid==oldartistid)
{
    Swal.close();
    return;
}

    if($('#sendingdata').data('sending')=='1')
        return;
     $('#sendingdata').data('sending','1');

     warningdiv.html('');
     $('#defaultloading').removeClass('d-none');

     warningdiv.addClass('d-none');

    $.ajax({
        type:"POST",
         dataType: 'json',
         url:base+'admin/ajax/addsimpleartist',
         data: {
            _token:csrf_token,
            itemid:itemid,
            artistid:artistid
        },
         success:function(data){

            $('#defaultloading').addClass('d-none');
            $('#sendingdata').data('sending','0');
            
            if(data.status=='success')
            {
                
                window.location.reload(false);
                
            }
            else if(data.status=='failed')
            {  
                
                    if(typeof data.msg!=="undefined")
                    {
                        warningdiv.html(data.msg);
                        warningdiv.removeClass('d-none');
                    }
                    else{
                        Swal.close();
                        
                    }
                    
            }
            else
            {
                
                Swal.close();
                  
                   
            }
        }
        });


});

$(document).on("click",".generateartistcode",function(event) {
    event.preventDefault();

    var current=$(this);
    var itemid=current.data('itemid');

    if($('#sendingdata').data('sending')=='1')
return;
$('#sendingdata').data('sending','1');

$('#defaultloading').removeClass('d-none');


            $.ajax({
                type:"POST",
                 dataType: 'json',
                 url:base+'admin/ajax/generateartistcode',
                 data: {
                    _token:csrf_token,
                    itemid:itemid
                },
                 success:function(data){

                    $('#defaultloading').addClass('d-none');
                    $('#sendingdata').data('sending','0');
                    
                    if(data.status=='success')
                    {
                        
                        var thelinkhtml='<div>Send this link to the artist to connect:</div><br><u><div class="selectall">'+base+'connectspotify?hash='+data.generatedlink+'</div></u>';
                        current.closest('.generatelinkwrap').find('.generatedlinkdivwrap').html(thelinkhtml);

                       
                            
                        
                        
                    }
                    else
                    {
                        
                        
                    }
                }
                });


});



$(document).on("click",".selectall",function() {

        var sel, range;
        var el = $(this)[0];
        if (window.getSelection && document.createRange) { //Browser compatibility
          sel = window.getSelection();
          if(sel.toString() == ''){ //no text selection
             window.setTimeout(function(){
                range = document.createRange(); //range object
                range.selectNodeContents(el); //sets Range
                sel.removeAllRanges(); //remove all ranges from selection
                sel.addRange(range);//add Range to a Selection.
            },1);
          }
        }else if (document.selection) { //older ie
            sel = document.selection.createRange();
            if(sel.text == ''){ //no text selection
                range = document.body.createTextRange();//Creates TextRange object
                range.moveToElementText(el);//sets Range
                range.select(); //make selection.
            }
        }
       
    });

 showArtistGenerator = function(generatedlink='',itemid,artistid='',istokensfine=0) {
/*
var topdescription=`<p class="text-muted">Here you can have 2 choices:</p>
<ul class="text-left"><li>1.) Generate a link to send to your artist. 
Through the link the artist should complete the steps to connect 
his/her account to your account You need this to manage his/her account for example
"Artist\'s Picks" etc.</li><li>2.) Or just add the artist without
 permissions, and request them later.</li></ul>`;
*/
 var topdescription=`<p class="text-muted">Add the artist below.</p>`;

var newtext='';
var generatedlinkdiv='';

if(generatedlink!='')
{
    var newtext=' New';
    var generatedlinkdiv='<div>Send this link to the artist to connect:</div><br><u><div class="selectall">'+base+'connectspotify?hash='+generatedlink+'</div></u>';

}




if (artistid!='')
{
    /*
    var addartistsecondchoice=`<div class="artistchangewrap position-relative p-4 mt-4 border border-secondary text-left">
    <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Change Artist (without permissions)</span>

    <div class="form-group">
                      <label for="artistchangeinput">Artist ID:</label>
              <input type="text" class="form-control artistchangeinput" name="artistchangeinput" placeholder="" value="`+artistid+`" >
			</div>

            <div class="text-danger warningdiv d-none mb-2"></div>

            <div class="text-center">
    <a class="btn btn-primary btn-sm changeartistclick" href="#" data-itemid=`+itemid+`>
                      <span>Change artist</span>
                  </a>
                  </div>

    </div>`;
    */
   var addartistsecondchoice=`<div class="artistchangewrap position-relative p-4 mt-4 border border-secondary text-left">
    <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Change Artist</span>

    <div class="form-group">
                      <label for="artistchangeinput">Artist ID:</label>
              <input type="text" class="form-control artistchangeinput" name="artistchangeinput" placeholder="" value="`+artistid+`" >
			</div>

            <div class="text-danger warningdiv d-none mb-2"></div>

            <div class="text-center">
    <a class="btn btn-primary btn-sm changeartistclick" href="#" data-itemid=`+itemid+`>
                      <span>Change artist</span>
                  </a>
                  </div>

    </div>`;
}
else
{
    /*
    var addartistsecondchoice=`<div class="artistchangewrap position-relative p-4 mt-4 border border-secondary text-left">
    <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Add Artist (without permissions)</span>
    
    <div class="form-group">
                          <label for="artistchangeinput">Artist ID:</label>
                  <input type="text" class="form-control artistchangeinput" name="artistchangeinput" placeholder="" value="" >
                </div>
                
                <div class="text-danger warningdiv d-none mb-2"></div>

                <div class="text-center">
    <a class="btn btn-primary btn-sm changeartistclick" href="#" data-itemid=`+itemid+`>
                          <span>Add artist</span>
                      </a>
                      </div>
    
    </div>`;
    */
   var addartistsecondchoice=`<div class="artistchangewrap position-relative p-4 mt-4 border border-secondary text-left">
    <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Add Artist</span>
    
    <div class="form-group">
                          <label for="artistchangeinput">Artist ID:</label>
                  <input type="text" class="form-control artistchangeinput" name="artistchangeinput" placeholder="" value="" >
                </div>
                
                <div class="text-danger warningdiv d-none mb-2"></div>

                <div class="text-center">
    <a class="btn btn-primary btn-sm changeartistclick" href="#" data-itemid=`+itemid+`>
                          <span>Add artist</span>
                      </a>
                      </div>
    
    </div>`;
    

 }

 var tokenaddedtext='';
if(istokensfine)
var tokenaddedtext='<p class="text-danger">Seems like you already added the artist with tokens, but you can generate another code and send it to the artist to refresh the tokens.</p>';
       

/*
var addartistfirstchoice=`<div class="generatelinkwrap position-relative p-4 border border-secondary text-left">
    
            <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Generate Link For User Permissions</span>
            `+tokenaddedtext+`
            <div class="text-center">
            <a class="btn btn-primary btn-sm generateartistcode" href="#" data-itemid=`+itemid+`>
                      <span>Generate`+newtext+` Link</span>
                  </a>
            </div>
                  <div class="generatedlinkdivwrap text-left mt-3">
                  `+generatedlinkdiv+`
                  </div>


                                       
                    </div>
*/
var addartistfirstchoice=``;
        
        Swal.fire({
            html: `<div class="generatelinkwrapwrap">`+topdescription+
    
            `<div class="card-body">
            
    
            `+addartistfirstchoice+addartistsecondchoice+`
    
            </div>
                
            </div>`,
            
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            showCancelButton: true,
            showConfirmButton: false,
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
    
          
                    resolve({
                        
                    });
    
                    // maybe also reject() on some condition
                });
            }
        }).then((data) => {
            // your input data object will be usable from here
            
            if(data.value)
            {
    
    
            }
            else
            {
                return;
            }
            
            
    
    
        });
    
    }


    showPasswordChanger = function(itemid,exist) {
       
        if(exist)
         var topdescription=`<p class="text-muted">Change password for the manager. This password is the password you use, to log in to spotify. <br><span class="text-danger">ONLY CHANGE IT IF YOU ARE SURE THAT THE NEW PASSWORD IS THE ONE WHICH YOU CAN LOG IN TO SPOTIFY WITH!</span></p>`;
         else
         var topdescription=`<p class="text-muted">Add password for the manager. This password is the password you use, to log in to spotify.</p>`;
        


        if (exist)
        {
            
            var showbutton=`<div class="text-center mt-4">
            <a class="btn btn-primary btn-sm showpasswordclick" href="#" data-itemid=`+itemid+`>
                              <span>Show password</span>
                          </a>
                          </div>
                          <div class="text-success text-center showpassdiv d-none mb-2 mt-3"></div>
                          `;
           
           var choicecontent=`<div class="currentchangerwrap position-relative p-4 mt-4 border border-secondary text-left">
            <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Change password</span>
        
            <div class="form-group">
                              <label for="thingstrinput">Password:</label>
                      <input type="password" class="form-control thingstrinput" name="thingstrinput" placeholder="" value="" autocomplete="off">
                    </div>
        
                    <div class="text-danger warningdiv d-none mb-2"></div>
        
                    <div class="text-center">
            <a class="btn btn-primary btn-sm changepasswordclick" href="#" data-itemid=`+itemid+`>
                              <span>Change password</span>
                          </a>
                          </div>

                          `+showbutton+`
        
            </div>`;
        }
        else
        {
            
           var choicecontent=`<div class="currentchangerwrap position-relative p-4 mt-4 border border-secondary text-left">
            <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Add password</span>
            
            <div class="form-group">
                                  <label for="thingstrinput">Password:</label>
                          <input type="password" class="form-control thingstrinput" name="thingstrinput" placeholder="" value="" autocomplete="off">
                        </div>
                        
                        <div class="text-danger warningdiv d-none mb-2"></div>
        
                        <div class="text-center">
            <a class="btn btn-primary btn-sm changepasswordclick" href="#" data-itemid=`+itemid+`>
                                  <span>Add password</span>
                              </a>
                              </div>
            
            </div>`;
            
        
         }
        
    
                
                Swal.fire({
                    html: `<div class="currentchangerwrapwrap">`+topdescription+
            
                    `<div class="card-body">
            
  
                    `+choicecontent+`
            
                    </div>
                        
                    </div>`,
                    
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Close',
                    showCancelButton: true,
                    showConfirmButton: false,
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
            
                            resolve({
                                
                            });
            
                            // maybe also reject() on some condition
                        });
                    }
                }).then((data) => {
                    // your input data object will be usable from here
                    
                    if(data.value)
                    {
            
                    }
                    else
                    {
                        return;
                    }
                });
            
            }

      showArtistPickChanger = function(itemid,exist,oldartistpick)
            {
                if(exist)
                var topdescription=`<p class="text-muted">Change artistpick for the artist.</p>`;
                else
                var topdescription=`<p class="text-muted">Add artistpick for the artist.</p>`;
               

        var refreshrequestcheckbox=`<div class="requestchangewrap">
        <div class="form-check d-inline-block">
          <label class="form-check-label">
            <input name="requestchange" type="checkbox" class="form-check-input requestchange" autocomplete="off" value="1">
           Initiate change on spotify
          <i class="input-helper"></i><i class="input-helper"></i></label>

          <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="If you tick this box, the artist's pick will be changed on spotify very shortly, otherwise it will be changed with normal schedule."></i>

      </div>
    </div>`;

               if (exist)
               {
                  
                  var choicecontent=`<div class="currentchangerwrap position-relative p-4 mt-4 border border-secondary text-left">
                   <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Change Artist's Pick</span>
               
                   `+refreshrequestcheckbox+`

                   <div class="form-group">
                                     <label for="artistpick">Artist's pick:</label>
                                     <p class="text-muted">(If field is empty, artist pick will be removed)</p>
                             <input type="text" class="form-control artistpick" name="artistpick" placeholder="Paste in link" value="`+oldartistpick+`" autocomplete="off">
                           </div>
               
                           <div class="text-danger warningdiv d-none mb-2"></div>
               
                           <div class="text-center">
                   <a class="btn btn-primary btn-sm changeartistpickclick" href="#" data-itemid=`+itemid+`>
                                     <span>Change/delete artist's pick</span>
                                 </a>
                                 </div>
               
                   </div>`;
               }
               else
               {
                   
                  var choicecontent=`<div class="currentchangerwrap position-relative p-4 mt-4 border border-secondary text-left">
                   <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Add Artist's Pick</span>
                   

                   `+refreshrequestcheckbox+`

                   <div class="form-group">
                                         <label for="artistpick">Artist's Pick:</label>
                                         <p class="text-muted">(If field is empty, artist pick will be removed)</p>
                                 <input type="text" class="form-control artistpick" name="artistpick" placeholder="" value="" autocomplete="off">
                               </div>
                               
                               <div class="text-danger warningdiv d-none mb-2"></div>
               
                               <div class="text-center">
                   <a class="btn btn-primary btn-sm changeartistpickclick" href="#" data-itemid=`+itemid+`>
                                         <span>Add/delete artist's pick</span>
                                     </a>
                                     </div>
                   
                   </div>`;
                   
               
                }
               

                


           
                       
                       Swal.fire({
                           html: `<div class="currentchangerwrapwrap">`+topdescription+
                   
                           `<div class="card-body">
                   
                           `+choicecontent+`
                           


                           <div class="text-center mt-4">OR</div>

                                        <div class="getcurrentpickwrap position-relative p-4 mt-4 border border-secondary text-left">
                                <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Get Current Artist's Pick</span>
                                        
                                <div class="text-danger warningdiv d-none mb-2"></div>
                                                        <div class="text-center">
                                                <a class="btn btn-primary btn-sm getartistpickclick" href="#" data-itemid=`+itemid+`>
                                                        <span>Get artist's pick from Spotify</span>
                                                 </a>
                                                        </div>
                                           

                                        </div>

                   
                           </div>
                               
                           </div>`,
                           
                           confirmButtonText: 'OK',
                           cancelButtonText: 'Close',
                           showCancelButton: true,
                           showConfirmButton: false,
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
                   
                                   resolve({
                                       
                                   });
                   
                                   // maybe also reject() on some condition
                               });
                           }
                       }).then((data) => {
                           // your input data object will be usable from here
                           
                           if(data.value)
                           {
                   
                           }
                           else
                           {
                               return;
                           }
                       });
                   
            }

     showAddStraight = function()
            {
    
      var topdescription=`<p class="text-muted">Add Your Spotify Accounts.</p>`;
               

               
                  var choicecontent=`<div class="currentchangerwrap position-relative p-4 mt-4 border border-secondary text-left">
                   <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Add Your Account</span>
                   

                   <div class="form-group">
                                         <label for="manageremail">Email:</label>
                        <input type="text" class="form-control manageremail" name="manageremail" placeholder="" value="" autocomplete="off">
                     </div>
                     
                     <div class="form-group">
                              <label for="addpassinput">Password:</label>
                      <input type="password" class="form-control addpassinput" name="addpassinput" placeholder="" value="" autocomplete="off">
                    </div>
                               
                               <div class="text-danger warningdiv d-none mb-2"></div>
                               <div class="text-success successdiv d-none mb-2"></div>
               
                               <div class="text-center">
                   <a class="btn btn-primary btn-sm addstraightclick" href="#">
                                         <span>Add</span>
                                     </a>
                                     </div>
                   
                   </div>`;
                   
               
                       
                       Swal.fire({
                           html: `<div class="currentchangerwrapwrap">`+topdescription+
                   
                           `<div class="card-body">
                   
                           `+choicecontent+`
                           

                           </div>
                           </div>`,
                           
                           confirmButtonText: 'OK',
                           cancelButtonText: 'Close',
                           showCancelButton: true,
                           showConfirmButton: false,
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
                   
                                   resolve({
                                       
                                   });
                   
                                   // maybe also reject() on some condition
                               });
                           }
                       }).then((data) => {
                           // your input data object will be usable from here
                           
                           if(data.value)
                           {
                   
                           }
                           else
                           {
                               return;
                           }
                       });
                   
            }

    $( ".changeartistpassword" ).click(function(event) {
                event.preventDefault();
        
        var current=$(this);
        var itemid=current.closest("[id^='parent_']").data('itemid');
        var exist=current.data('exist');
        
        
        showPasswordChanger(itemid,exist);
        
           
                    
});



$( ".addstraight" ).click(function(event) {
    event.preventDefault();


    showAddStraight();


        
});

    $( ".changeartistpick" ).click(function(event) {
    event.preventDefault();

var current=$(this);
var itemid=current.closest("[id^='parent_']").data('itemid');
var exist=current.data('exist');
var artistpick=current.data('artistpick');

    showArtistPickChanger(itemid,exist,artistpick);


        
});

    $(document).on("click",".connectartist",function(event) {

        event.preventDefault();

var current=$(this);
var itemid=current.closest("[id^='parent_']").data('itemid');

var artistid=current.closest("[id^='parent_']").data('artistid');
var istokensfine=current.closest("[id^='parent_']").data('istokensfine');

var generatedlink=current.data('generatedlink');

showArtistGenerator(generatedlink,itemid,artistid,istokensfine);

   
            
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
        url: base+'admin/ajax/updateownfield',
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

/*
    $.ajax({
        url: 'https://generic.wg.spotify.com/artist-identity-view/v1/profile/2O8QAJmRrwkFXq2aWZnHYB/pinned?organizationUri=spotify:artist:2O8QAJmRrwkFXq2aWZnHYB',
        type: 'PUT',
        data: '{"uri":"spotify:user:9fsyz2t3gffvgjczbh5xqkie4:playlist:6Gv0h0g2UZ2NAfBlBeZTR9","type":"playlist","backgroundImageUrl":null}',
        headers: {
            'authorization' : 'Bearer ' + accessToken,
            "Content-Type": "application/json"
        },
        dataType: 'json',
        success: function(data) {
            console.log(data);
        }
    });
    
*/

}); 