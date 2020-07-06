function sendSingleClaimRequest(itemid,parentelement)
        {

if($('#sendingdata').data('sending')=='1')
return;
$('#sendingdata').data('sending','1');

$('#defaultloading').removeClass('d-none');


            $.ajax({
                type:"POST",
                 dataType: 'json',
                 url:base+'admin/ajax/getsingleclaimstate',
                 data: {
                    _token:csrf_token,
                    itemid:itemid
                },
                 success:function(data){

                    $('#defaultloading').addClass('d-none');
                    $('#sendingdata').data('sending','0');
                    
                    if(data.status=='success')
                    {
                    
                        parentelement.removeClass('successful-bg waiting-bg wesetitclaimed-bg problem-bg');

                        if(data.claimstate=='1')
                        {
                            parentelement.addClass('successful-bg');
                            parentelement.find('.claimfield').html('Claimed');

                        }
                        else if(data.claimstate =='2')
                        {
                            parentelement.addClass('problem-bg');
                            parentelement.find('.claimfield').html('Not Claimed');
                        }
                        else{

                            parentelement.addClass('waiting-bg');
                            parentelement.find('.claimfield').html('Unknown');
                        }

                        parentelement.data('claimed',data.claimstate);
                        parentelement.find('.claimfield').editable('setValue',data.claimstate);
                        
                        
                    }
                    else
                    {
                        
                        
                    }
                }
                });
        }


        showSwal_confirm_single_refresh = function(itemid,parentelement) {

            Swal.fire({
                            title: 'Are you sure?',
                            text: "Current artist has been set to \"claimed\" by you, refreshing it might unset it!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3f51b5',
                            cancelButtonColor: '#ff4081',
                            confirmButtonText: 'OK ',
                            customClass: {
                                popup:'card',
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-danger',
                              }
                }).then((result) => {
                    
                    if (result.value)
                    sendSingleClaimRequest(itemid,parentelement)
                    else
                    return;
        
                })
                }

$( document ).ready(function() {



$(document).on("click",".addtogroupnowclick",function(event) {
    event.preventDefault();


    var current=$(this);

        var itemid=current.closest('.popupwrap').find('.itemidaddtogroup').val();

        var groupid=current.closest('.popupwrap').find('.groupid').val();

        if($('#sendingdata').data('sending')=='1')
        return;
        $('#sendingdata').data('sending','1');

        $('.successfulgroupadd').html('');
        $('.failedgroupadd').html('');

        $('#defaultloading').removeClass('d-none');
        $('.successfulgroupadd').addClass('d-none');
        $('.failedgroupadd').addClass('d-none');

        $.ajax({
            type:"POST",
             dataType: 'json',
             url:base+'admin/ajax/addtogroup',
             data: {
                _token:csrf_token,
                itemid:itemid,
                groupid:groupid
            },
             success:function(data){

                $('#defaultloading').addClass('d-none');
                $('#sendingdata').data('sending','0');
                
                if(data.status=='success')
                {
                    let addedid = Math.random().toString(36).substring(7);
                    current.closest('.popupwrap').find('.editgroupinnerwrap').prepend('<p style="display:none" data-groupid="'+data.responsearray.groupid+'" data-grouptype="'+data.responsearray.grouptype+'" class="insertedgroup'+addedid+' groupr pr-4 maxwidth200 border-bottom border-secondary position-relative pb-2" title="'+data.responsearray.grouptitle+'"><a class="text-warning" href="https://spotifame.com/admin/group/'+data.responsearray.groupid+'" target="_blank">'+data.responsearray.grouptitle+'</a><span title="Remove from group" class="removebutton position-absolute right0px removeitemfromgroupclick"><i class="mdi mdi-close"></i></span></p>');
                
                    $('.insertedgroup'+addedid).fadeIn(600);

                    if(typeof data.msg!=="undefined")
                    {
                        $('.successfulgroupadd').html(data.msg);
                        $('.successfulgroupadd').removeClass('d-none');

                    }

                    current.closest('.popupwrap').find('.groupid').val('');
                    current.closest('.popupwrap').find('.selectedwrap').html('');

                  
                }
                else
                {
                    if(typeof data.msg!=="undefined")
                    {
                        $('.failedgroupadd').html(data.msg);
                        $('.failedgroupadd').removeClass('d-none');
                    }
                }
            }
    });



    
});


$(document).on("click",".addtomultiplegroupnowclick",function(event) {
    event.preventDefault();


    var current=$(this);

    var searchstring='';
    searchstring=current.closest('.popupwrap').find('.searchstring').val();

        var groupid=current.closest('.popupwrap').find('.groupid').val();

        if($('#sendingdata').data('sending')=='1')
        return;
        $('#sendingdata').data('sending','1');

        $('.successfulgroupadd').html('');
        $('.failedgroupadd').html('');

        $('#defaultloading').removeClass('d-none');
        $('.successfulgroupadd').addClass('d-none');
        $('.failedgroupadd').addClass('d-none');

        $.ajax({
            type:"POST",
             dataType: 'json',
             url:base+'admin/ajax/addmultipletogroup',
             data: {
                _token:csrf_token,
                searchstring:searchstring,
                groupid:groupid
            },
             success:function(data){

                $('#defaultloading').addClass('d-none');
                $('#sendingdata').data('sending','0');
                
                if(data.status=='success')
                {
                    let addedid = Math.random().toString(36).substring(7);
                    current.closest('.popupwrap').find('.editgroupinnerwrap').prepend('<p style="display:none" data-groupid="'+data.responsearray.groupid+'" data-grouptype="'+data.responsearray.grouptype+'" class="insertedgroup'+addedid+' groupr pr-4 maxwidth200 border-bottom border-secondary position-relative pb-2" title="'+data.responsearray.grouptitle+'"><a class="text-warning" href="https://spotifame.com/admin/group/'+data.responsearray.groupid+'" target="_blank">'+data.responsearray.grouptitle+'</a><span title="Remove from group" class="removebutton position-absolute right0px removeitemfrommultiplegroupclick"><i class="mdi mdi-close"></i></span></p>');
                
                    $('.insertedgroup'+addedid).fadeIn(600);

                    if(typeof data.msg!=="undefined")
                    {
                        $('.successfulgroupadd').html(data.msg);
                        $('.successfulgroupadd').removeClass('d-none');

                    }

                    current.closest('.popupwrap').find('.groupid').val('');
                    current.closest('.popupwrap').find('.selectedwrap').html('');

                  
                }
                else
                {
                    if(typeof data.msg!=="undefined")
                    {
                        $('.failedgroupadd').html(data.msg);
                        $('.failedgroupadd').removeClass('d-none');
                    }
                }
            }
    });



    
});



$(document).on('focus', '.suggestgroup' ,function(){

    var current= $(this);

    $( this).each(function(i, el) {

      $(el).autocomplete({
    
      
      minLength: 2,
      delay:5,
      source: function (request, response) {

          request._token=csrf_token;
          request.grouptype=current.data('type');
          request.itemid=current.closest('.popupwrap').find('.itemidaddtogroup').val();

          request.searchstring=current.closest('.popupwrap').find('.searchstring').val();

          $.ajax({
        type: "POST",
        url:base+'admin/ajax/suggestgroup',
        data: request,
        success: response,
        dataType: 'json'
      });
        },
      /*focus: function( event, ui ) {
          $(this).val( ui.item.value );
          return false;
      },*/
      select: function( event, ui ) {
          
        current.val('');
        current.closest('.popupwrap').find('.selectedwrap').html(ui.item.label);
        current.closest('.popupwrap').find('.groupid').val(ui.item.value);
        current.closest('.popupwrap').find('.selectedwrap').parent().removeClass('d-none');
          
          return false;
      }
  }).on('focus', function() { $(this).keydown(); })
  .data('ui-autocomplete')._renderItem = function (ul, item) {
      
      ul.addClass('suggestionsearch-table'); //Ul custom class here
      
      
      return $("<li class='brandsearchwrap'></li>")
          .data( "item.autocomplete", item )
          .append( "<a><div class='infowrap infowrap2'><span>Group: </span><span class='title brandtitle'>" + item.label + "</span>" + "</div></a>" )
          .appendTo( ul );
      
  };

});
});	

$(document).on("click",".quickremoveitemfromgroupclick",function(event) {
    event.preventDefault();


    var current=$(this);

    var itemid=current.data('itemid');

    var groupid=$('#searchresults').data('groupid');

    var itemid=current.closest("[id^='parent_']").data('realitemid');
        if(itemid=='' || itemid===undefined)
     itemid=current.closest("[id^='parent_']").data('itemid');

    var grouptype=current.data('type');

    
    var parentelement=current.closest("[id^='parent_']");

    if($('#sendingdata').data('sending')=='1')
    return;
    $('#sendingdata').data('sending','1');
    $('#defaultloading').removeClass('d-none');

    $.ajax({
        type:"POST",
         dataType: 'json',
         url:base+'admin/ajax/removefromgroup',
         data: {
            _token:csrf_token,
            itemid:itemid,
            groupid:groupid,
            grouptype:grouptype
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
            else
            {
                
            }
        }
        });





});

    $(document).on("click",".removeitemfromgroupclick",function(event) {
        event.preventDefault();
    

        var current=$(this);

        var itemid=current.closest('.popupwrap').find('.itemidaddtogroup').val();

        var groupid=current.closest('.groupr').data('groupid');
        var grouptype=current.closest('.groupr').data('grouptype');

        
        var parentelement=current.closest('.groupr');

        if($('#sendingdata').data('sending')=='1')
        return;
        $('#sendingdata').data('sending','1');
        $('#defaultloading').removeClass('d-none');

        $.ajax({
            type:"POST",
             dataType: 'json',
             url:base+'admin/ajax/removefromgroup',
             data: {
                _token:csrf_token,
                itemid:itemid,
                groupid:groupid,
                grouptype:grouptype
            },
             success:function(data){

                $('#defaultloading').addClass('d-none');
                $('#sendingdata').data('sending','0');
                
                if(data.status=='success')
                {
                
                    parentelement.fadeOut(500, function(){
                
                        $(this).remove();
                        });

                        if(grouptype=='track' && $('#searchresults').data('groupid')==groupid && $('#searchresults').find('[data-realitemid="'+itemid+'"]').length)
                        {
                            $('#searchresults').find('[data-realitemid="'+itemid+'"]').fadeOut(500, function(){
                
                                $(this).remove();
                                });
                        }
                        else if($('#searchresults').data('groupid')==groupid && $('#searchresults').find("#parent_"+itemid).length)
                        {
                            $('#searchresults').find("#parent_"+itemid).fadeOut(500, function(){
                
                                $(this).remove();
                                });
                        }
                    
                  
                }
                else
                {
                    
                }
            }
            });





    });

    $(document).on("click",".removeitemfrommultiplegroupclick",function(event) {
        event.preventDefault();
    

        var current=$(this);

        var searchstring='';
        searchstring=current.closest('.popupwrap').find('.searchstring').val();

        var groupid=current.closest('.groupr').data('groupid');
        var grouptype=current.closest('.groupr').data('grouptype');

        
        var parentelement=current.closest('.groupr');

        if($('#sendingdata').data('sending')=='1')
        return;
        $('#sendingdata').data('sending','1');
        $('#defaultloading').removeClass('d-none');

        $.ajax({
            type:"POST",
             dataType: 'json',
             url:base+'admin/ajax/removemultiplefromgroup',
             data: {
                _token:csrf_token,
                searchstring:searchstring,
                groupid:groupid,
                grouptype:grouptype
            },
             success:function(data){

                $('#defaultloading').addClass('d-none');
                $('#sendingdata').data('sending','0');
                
                if(data.status=='success')
                {
                
                    parentelement.fadeOut(500, function(){
                
                        $(this).remove();
                        });

                        if(grouptype=='track' && $('#searchresults').data('groupid')==groupid && $('#searchresults').find('[data-realitemid="'+itemid+'"]').length)
                        {
                            $('#searchresults').find('[data-realitemid="'+itemid+'"]').fadeOut(500, function(){
                
                                $(this).remove();
                                });
                        }
                        else if($('#searchresults').data('groupid')==groupid && $('#searchresults').find("#parent_"+itemid).length)
                        {
                            $('#searchresults').find("#parent_"+itemid).fadeOut(500, function(){
                
                                $(this).remove();
                                });
                        }
                    
                  
                }
                else
                {
                    
                }
            }
            });





    });

    $(document).on("click",".addmultipletogroupclick",function(event) {
        event.preventDefault();

        var current=$(this);

        var currenttype=current.data('type');

        var addedgroups=[];
        var lastgroupid='';
        var lastgroupname='';
if($('#sendingdata').data('sending')=='1')
return;
$('#sendingdata').data('sending','1');

$('#defaultloading').removeClass('d-none');

        $.ajax({
            type:"POST",
             dataType: 'json',
             url:base+'admin/ajax/getmultipleitemsgroups',
             data: {
                _token:csrf_token,
                searchstring:search_string_more,
                type:currenttype
            },
             success:function(data){

                $('#defaultloading').addClass('d-none');
                $('#sendingdata').data('sending','0');
                
                if(data.status=='success')
                {
                
                   addedgroups=data.addedgroups;
                   lastgroupid=data.lastgroupid;
                   lastgroupname=data.lastgroupname;
                   showSwal_openMultipleGroupAdd(search_string_more,currenttype,addedgroups,lastgroupid,lastgroupname);
                  
                }
                else
                {
                    
                }
            }
            });



    });

    $(document).on("click",".addsingletogroupclick",function(event) {
        event.preventDefault();

        var current=$(this);

        var itemid=current.closest("[id^='parent_']").data('realitemid');
        if(itemid=='' || itemid===undefined)
        itemid=current.closest("[id^='parent_']").data('itemid');

        var currenttype=current.data('type');
        var itemname=current.data('name');

        var addedgroups=[];
        var lastgroupid='';
        var lastgroupname='';
if($('#sendingdata').data('sending')=='1')
return;
$('#sendingdata').data('sending','1');

$('#defaultloading').removeClass('d-none');

        $.ajax({
            type:"POST",
             dataType: 'json',
             url:base+'admin/ajax/getitemsgroups',
             data: {
                _token:csrf_token,
                itemid:itemid,
                type:currenttype
            },
             success:function(data){

                $('#defaultloading').addClass('d-none');
                $('#sendingdata').data('sending','0');
                
                if(data.status=='success')
                {
                
                   addedgroups=data.addedgroups;
                   lastgroupid=data.lastgroupid;
                   lastgroupname=data.lastgroupname;
                   showSwal_openSingleGroupAdd(itemid,currenttype,itemname,addedgroups,lastgroupid,lastgroupname);
                  
                }
                else
                {
                    
                }
            }
            });



    });


    $(document).on("click",".addnewgroupclick",function(event) {
        event.preventDefault();
    

        var current=$(this);
        let data={
            value:{}
        };
        data.value.grouptype=$('.addgroupdivwrap').find('.grouptype').find('option:selected').val();
        data.value.groupname=$('.addgroupdivwrap').find('.groupname').val();
        data.value.groupdescription=$('.addgroupdivwrap').find('.groupdescription').val();
        
        startNewGroupCreation(data);

    });

    $(document).on("click",".cancelnewgroupclick",function(event) {
        event.preventDefault();
    
        Swal.close();


    });


    function startNewGroupCreation(inputdata)
        {

var failed1div=$('.addgroupdivwrap').find('.errordiv1');
var failed2div=$('.addgroupdivwrap').find('.errordiv2');

if($('#sendingdata').data('sending')=='1')
return;
$('#sendingdata').data('sending','1');


        failed1div.html('');
        failed2div.html('');

     $('#defaultloading').removeClass('d-none');

     failed1div.addClass('d-none');
     failed2div.addClass('d-none');

            $.ajax({
                type:"POST",
                 dataType: 'json',
                 url:base+'admin/ajax/addnewgroup',
                 data: {
                    _token:csrf_token,
                    inputdata:inputdata
                },
                 success:function(data){

                    $('#defaultloading').addClass('d-none');
                    $('#sendingdata').data('sending','0');
                    
                    if(data.status=='success')
                    {
                    
                        if(curpage=='admin/groups')
                       window.location.reload(false);
                       else
                       Swal.close();
                    }
                    else
                    {
                        if(data.status=='failed1')
                        {
                            failed1div.html(data.msg);
                            failed1div.removeClass('d-none');
                        }
                        else if(data.status=='failed2')
                        {
                            failed2div.html(data.msg);
                            failed2div.removeClass('d-none');
                        }
                        else
                        {
                            Swal.close();
                        }
                    }
                }
                });
        }

    showSwal_are_you_sure_to_delete = function(current,customtext,functiontorun) {


        Swal.fire({
            title: 'Are you sure?',
            text: customtext,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes!'
          }).then((result) => {
            if (result.value) {

                functiontorun(current);

              Swal.fire(
                'Deleted!',
                'The item has been removed',
                'success'
              )
            }
          })


    }

    
    showSwal_openSingleGroupAdd = function(itemid,currenttype,itemname,addedgroups,lastgroupid,lastgroupname) {

        var typechoice='';
        
        var selectedgrouphidden='d-none';

        if(currenttype=='artist')
        {
            typechoice='Selected Artist: <b>'+itemname+'</b>';
        }
        else if(currenttype=='playlist')
        {
            typechoice='Selected Playlist: <b>'+itemname+'</b>';
        }
        else if(currenttype=='track')
        {
            typechoice='Selected Track: <b>'+itemname+'</b>';
        }

        if(lastgroupid!='')
        selectedgrouphidden='';

         var topdescription='<h4>Add/Remove Item From Groups</h4><p class="maxwidth300 mx-auto">'+typechoice+'</p>';
         
         var adduglyselectclass="";
         if (navigator.userAgent.match('iPad|iPhone|iPod')) {
            var adduglyselectclass=" uglyselect";
            
        }



        var editgroups='';
        if(Array.isArray(addedgroups) && addedgroups.length)       
        {
                    addedgroups.forEach(function(addedgroupitem){

                        
                editgroups+='<p data-groupid="'+addedgroupitem.id+'" data-grouptype="'+addedgroupitem.type+'" class="groupr pr-4 maxwidth200 border-bottom border-secondary position-relative pb-2" title="'+addedgroupitem.name+'"><a href="'+base+'admin/group/'+addedgroupitem.id+'" target="_blank">'+addedgroupitem.name+'</a><span title="Remove from group" class="removebutton position-absolute right0px removeitemfromgroupclick"><i class="mdi mdi-close"></i></span></p>';                        

               

                      });

                     

        }


        var removefromexistinggroups=`<div class="position-relative p-4 border border-secondary border-top-0 text-left">
    
                     <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Already added to</span>
                            
                    <div class="editgroupinnerwrap">
                     `+editgroups+`
                     </div>

                    </div>`;
        
        
                   
        
        Swal.fire({
            html: `<div class="popupwrap editgroupwrap">`+topdescription+`
    
            <input type="hidden" class="itemidaddtogroup" value="`+itemid+`" />

            <div class="card-body">
           
                    <div class="position-relative p-4 border border-secondary text-left">
    
                     <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Add to a new group</span>
                            
                     
                     
                     <div class="form-group position-relative">
                     <a href="#" class="position-absolute right0px fontsize20 addnewgroup" data-selectedtype="`+currenttype+`" title="Add a new group"><i class="mdi mdi-plus-circle-outline align-middle mr-1"></i><span></span></a>

                          <label for="groupname">Search for group:</label>
                  <input type="text" data-type="`+currenttype+`" class="form-control groupname suggestgroup" name="groupname" placeholder="" value="" />
                  
                  <div class="`+selectedgrouphidden+` mt-2">Add item to group: <b class="selectedwrap text-success">`+lastgroupname+`</b></div>
                  <input type="hidden" class="groupid" name="groupid" value="`+lastgroupid+`" />

                </div>

                <div class="successfulgroupadd text-success mb-2 d-none"></div>
                <div class="failedgroupadd text-danger mb-2 d-none"></div>
                

            
                <div class="text-center">
            <a class="btn btn-primary btn-sm addtogroupnow addtogroupnowclick" href="#">
                      <span>Add to group</span>
                  </a>
            </div>

                    </div>


                    
                    `+removefromexistinggroups+`

                    
    
            </div>

                
            </div>
            `,
            
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            showCancelButton: false,
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


    showSwal_openMultipleGroupAdd = function(searchstring,currenttype,addedgroups,lastgroupid,lastgroupname) {

        var typechoice='';
        
        var selectedgrouphidden='d-none';


        if(lastgroupid!='')
        selectedgrouphidden='';

         var topdescription='<h4>Add/Remove Items From Groups</h4><p class="maxwidth300 mx-auto">The first 5.000 items can be added to a group at once.</p>';
         
         var adduglyselectclass="";
         if (navigator.userAgent.match('iPad|iPhone|iPod')) {
            var adduglyselectclass=" uglyselect";
            
        }



        var editgroups='';
        if(Array.isArray(addedgroups) && addedgroups.length)       
        {
                    addedgroups.forEach(function(addedgroupitem){

                        
                editgroups+='<p data-groupid="'+addedgroupitem.id+'" data-grouptype="'+addedgroupitem.type+'" class="groupr pr-4 maxwidth200 border-bottom border-secondary position-relative pb-2" title="'+addedgroupitem.name+'"><a href="'+base+'admin/group/'+addedgroupitem.id+'" target="_blank">'+addedgroupitem.name+'</a><span title="Remove from group" class="removebutton position-absolute right0px removeitemfrommultiplegroupclick"><i class="mdi mdi-close"></i></span></p>';                        

               

                      });

                     

        }


        var removefromexistinggroups=`<div class="position-relative p-4 border border-secondary border-top-0 text-left">
    
                     <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Already added to</span>
                            
                    <div class="editgroupinnerwrap">
                     `+editgroups+`
                     </div>

                    </div>`;
        
        
                   
        
        Swal.fire({
            html: `<div class="popupwrap editgroupwrap">`+topdescription+`
    
            <input type="hidden" class="searchstring" value="`+searchstring+`" />
      

            <div class="card-body">
           
                    <div class="position-relative p-4 border border-secondary text-left">
    
                     <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Add to a new group</span>
                            
                     
                     
                     <div class="form-group position-relative">
                     <a href="#" class="position-absolute right0px fontsize20 addnewgroup" data-selectedtype="`+currenttype+`" title="Add a new group"><i class="mdi mdi-plus-circle-outline align-middle mr-1"></i><span></span></a>

                          <label for="groupname">Search for group:</label>
                  <input type="text" data-type="`+currenttype+`" class="form-control groupname suggestgroup" name="groupname" placeholder="" value="" />
                  
                  <div class="`+selectedgrouphidden+` mt-2">Add item to group: <b class="selectedwrap text-success">`+lastgroupname+`</b></div>
                  <input type="hidden" class="groupid" name="groupid" value="`+lastgroupid+`" />

                </div>

                <div class="successfulgroupadd text-success mb-2 d-none"></div>
                <div class="failedgroupadd text-danger mb-2 d-none"></div>
                

            
                <div class="text-center">
            <a class="btn btn-primary btn-sm addtogroupnow addtomultiplegroupnowclick" href="#">
                      <span>Add to group</span>
                  </a>
            </div>

                    </div>


                    
                    `+removefromexistinggroups+`

                    
    
            </div>

                
            </div>
            `,
            
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            showCancelButton: false,
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

    

    showSwal_addNewGroup = function(selectedtype) {

        var selected=' selected="selected"';
        var artistselect='';
        var playlistselect='';
        var trackselect='';

        if(selectedtype=='artist')
        artistselect=selected;
        else if(selectedtype=='playlist')
        playlistselect=selected;
        else if(selectedtype=='track')
        trackselect=selected;

         var topdescription='<h4>Add a New Group</h4>';
         
         var adduglyselectclass="";
         if (navigator.userAgent.match('iPad|iPhone|iPod')) {
            var adduglyselectclass=" uglyselect";
            
        }
        
        Swal.fire({
            html: `<div class="popupwrap addgroupdivwrap">`+topdescription+`
    
            <div class="card-body">
            <div class="form-group row">
                <label for="grouptype" class="lineheight24rem pl-2">Group type:*</label>
                                <div class="col-sm-9">
                                    <select class="form-control border-secondary grouptype`+adduglyselectclass+`" name="grouptype" autocomplete="off">
                                        <option value=""> - Choose type - </option>
                                        <option value="artist"`+artistselect+`>Artist Group</option>
                                        <option value="playlist"`+playlistselect+`>Playlist Group</option>
                                        <option value="track"`+trackselect+`>Track Group</option>
                                    </select>
                                </div>
                                <div class="errordiv1 text-danger d-none mt-2 ml-2"></div>
            </div>
    
                    <div id="popuprestwrap" class="position-relative p-4 border border-secondary text-left">
    
                     <span class="position-absolute pl-2 pr-2 text-white mydarkbg mymenutitle">Required Informations</span>
                            

                     <div class="form-group">
                          <label for="groupname">Add a group name*:</label>
                  <input type="text" class="form-control groupname" name="groupname" placeholder="" value="" >
                </div>

                <div class="form-group">
                          <label for="groupdescription">Add a description*:</label>
                          <textarea class="form-control groupdescription" rows="5" name="groupdescription" placeholder=""></textarea>
                 
                </div>

                <div class="errordiv2 text-danger d-none"></div>
                         
                

                    </div>


                    
    
            </div>

            <div class="text-center">
                <a class="btn btn-primary btn-sm addnewgroupclick p-2 mr-2" href="#">
                                  <span class="fontsize18">Add</span>
                              </a><a 
                              class="btn btn-danger btn-sm cancelnewgroupclick p-2" href="#">
                              <span class="fontsize18">Cancel</span>
                          </a>
                              </div>
                
            </div>
            `,
            
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            showCancelButton: false,
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


    $(document).on("click",".addnewgroup",function(event) {
        event.preventDefault();

var current=$(this);
var selectedtype =current.data('selectedtype');

showSwal_addNewGroup(selectedtype);

        
    });




    
    $( ".getsingleclaimstate" ).click(function(event) {
        event.preventDefault();

var current=$(this);
var itemid=current.closest("[id^='parent_']").data('itemid');
var parentelement=current.closest("[id^='parent_']");
var currentclaimstate=parentelement.data('claimed');


if(currentclaimstate=='3')
{

showSwal_confirm_single_refresh(itemid,parentelement);

}
else
{
sendSingleClaimRequest(itemid,parentelement);

}

        
    });



    $( "#orderbychange" ).change(function(event) {
        event.preventDefault();

        var current=$(this);
        var currentval=current.val();
        
        $('#orderby').val(currentval);

        if($('#searchform').length)
        var theform='searchform';
        else
        var theform='currentform';

        $( "#"+theform ).submit();
});


});