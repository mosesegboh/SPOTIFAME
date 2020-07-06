$( document ).ready(function() {



    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });


    function deleteplaylist(current)
    {
      
        var itemid=current.closest("[id^='parent_']").data('itemid');
        var playlistid=current.closest("[id^='parent_']").data('playlistid');
        var parentelement=current.closest("[id^='parent_']");


        if($('#sendingdata').data('sending')=='1')
        return;
        $('#sendingdata').data('sending','1');

        $('#defaultloading').removeClass('d-none');


        $.ajax({
        type:"POST",
        dataType: 'json',
        url:base+'admin/ajax/removeplaylist',
        data: {
            _token:csrf_token,
            itemid:itemid,
            playlistid:playlistid,
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

    

    $( ".removeplaylistclick" ).click(function(event) {
        event.preventDefault();

        var current=$(this);

        showSwal_are_you_sure_to_delete(current,'Are you sure you\'d like to remove the playlist?',deleteplaylist);

        

        
    });


if(parseFloat(item_count.replace(/,/g, ''))>0 && searchset=='1')
{
    $('html, body').animate({
        scrollTop: $("#playlists").offset().top-20
    }, 500);
}

$(document).on("click",".addallplaylistsnow",function() {
        event.preventDefault();
    
var current=$(this);
var managerid=current.data('managerid');
var refreshall=current.closest('.importplaylistwrap').find('#refreshallplaylists').val();

 var warningdiv=current.closest('.importplaylistwrap').find('.warningdiv');

 var userid=current.closest('.importplaylistwrap').find('.theuserid').val();

    if($('#sendingdata').data('sending')=='1')
        return;
     $('#sendingdata').data('sending','1');

     warningdiv.html('');
     $('#defaultloading').removeClass('d-none');

     warningdiv.addClass('d-none');

    $.ajax({
        type:"POST",
         dataType: 'json',
         url:base+'admin/ajax/addplaylists',
         data: {
            _token:csrf_token,
            managerid:managerid,
            refreshall:refreshall,
            userid:userid
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


 showPlaylistAllAdd = function(managerid='',managername='') {

    if(managerid!='')
var topdescription=`<p class="text-muted">Import your playlists for <u><b>`+managername+`</b></u>.</p>`;
    else
var topdescription=`<p class="text-muted">Import your playlists for <u><b>ALL</b></u> of your manager accounts.</p>`;

if(isadmin=='1')
{
var useridfield=`<div class="form-group">
            <label for="theuserid">Add Users Id If You Want:</label>
          <input type="text" class="form-control theuserid" name="theuserid" placeholder="" value="" >
   </div>`;
 }
else
{
var useridfield='';
 }

        Swal.fire({
            html: `<div class="importplaylistwrapwrap">`+topdescription+`
    
            <div class="card-body">
            
    
           <div class="importplaylistwrap position-relative p-4 border border-secondary text-left">
    
            <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Import Playlists</span>
            
   <div id="refreshallplaylistswrap">

   
   `+useridfield+`

            <div class="form-check d-inline-block">
              <label class="form-check-label">
                <input name="refreshallplaylists" id="refreshallplaylists" type="checkbox" class="form-check-input" autocomplete="off">
               Also refresh old playlists
              <i class="input-helper"></i><i class="input-helper"></i></label>

              <i class="mdi mdi mdi-help-circle text-primary position-absolute mytooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="If you tick this box, already added playlists data will be refetched and refreshed, otherwise only new playlists will be added."></i>

          </div>
        </div>


            <div class="text-danger warningdiv d-none mb-2"></div>

            <div class="text-center">
            <a class="btn btn-primary btn-sm addallplaylistsnow" href="#" data-managerid=`+managerid+`>
                      <span>Import</span>
                  </a>
            </div>
                
        
                    </div>

    
            </div>
                
            </div>
            `,
            
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


   


    $( ".openplaylistadd" ).click(function(event) {
        event.preventDefault();

var current=$(this);

showPlaylistAllAdd();

   
            
        });


   $( ".refreshsinglemanager" ).click(function(event) {
            event.preventDefault();
    
    var current=$(this);
    
    var managerid=current.data('managerid');
    
    var managername=current.data('name');
    
    showPlaylistAllAdd(managerid,managername);
    
       
                
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