
$( document ).ready(function() {

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });


    $( ".backstep" ).click(function(event) {
        event.preventDefault();
        
        var current=$(this);

            location.href = current.data('href');

      });


      $( ".addanotherartist" ).click(function(event) {
        event.preventDefault();
        
        var current=$(this);
        
        var currentartistid=$('#artistaddwrap').data('artistid');
        var nextartistid=currentartistid+1;
        
        var addartistdiv='<div id="artistitemwrap_'+nextartistid+'" class="border-top border-secondary mt-4 pt-2">';
        addartistdiv+='<div class="form-group">';
        addartistdiv+='<label for="artistlink_'+nextartistid+'">Spotify Artist Link:</label><div class="pr-4 position-relative">';
        addartistdiv+='<input type="text" class="form-control" name="artistlink[]" placeholder="Spotify Artist Link" id="artistlink_'+nextartistid+'" value="" required="" autocomplete="off">';
        addartistdiv+='';
        addartistdiv+='</div></div>';


        addartistdiv+='<div class="form-group">';
        addartistdiv+='<div class="pr-4 position-relative"><label for="artistgenre_'+nextartistid+'">Artist Genre (choose most relevant):</label>';
        addartistdiv+='<div style="top:0px;" class="deletebuttonwrapper deleteartistclick" data-itemid="'+nextartistid+'">';
        addartistdiv+='<i class="mdi mdi-delete-forever btn-icon-append align-middle"></i></div></div>';
        addartistdiv+='<div class="pr-4 position-relative">';

        addartistdiv+='<input type="text" class="suggestgenre form-control" name="artistgenre[]" placeholder="...start typing" id="artistgenre_'+nextartistid+'" value="" required="" autocomplete="off">';
        addartistdiv+='</div>';
        addartistdiv+='</div>';
        addartistdiv+='</div>';
            

        
        

        $('#artistaddwrap').append(addartistdiv);
        $('#artistaddwrap').data('artistid',nextartistid);

        
          

      });


      $( "#logreg-forms" ).on( "click", ".deleteartistclick", function(event) {
        event.preventDefault();
        
        var current=$(this);

        var artistid=current.data('itemid');

        $('#artistitemwrap_'+artistid).remove();


      });

      $( ".addanotherplaylist" ).click(function(event) {
        event.preventDefault();
        
        var current=$(this);

        var currentplaylistid=$('#playlistaddwrap').data('playlistid');
        var nextplaylistid=currentplaylistid+1;

        var addplaylistdiv='<div id="playlistitemwrap_'+nextplaylistid+'" class="border-top border-secondary mt-4 pt-2 form-group">';

        addplaylistdiv+='<label for="playlistlink_'+nextplaylistid+'">Spotify Playlist Link:</label>';
        addplaylistdiv+='<div class="pr-4 position-relative">';
        addplaylistdiv+='<input type="text" class="form-control" name="playlistlink[]" placeholder="Spotify Playlist Link" id="playlistlink_'+nextplaylistid+'" value="" required="" autocomplete="off">';
        addplaylistdiv+='<div class="deletebuttonwrapper deleteplaylistclick" data-itemid="'+nextplaylistid+'">';
        addplaylistdiv+='<i class="mdi mdi-delete-forever btn-icon-append align-middle"></i></div></div></div>';
        


        $('#playlistaddwrap').append(addplaylistdiv);
        $('#playlistaddwrap').data('playlistid',nextplaylistid);



      });
     


      $( "#logreg-forms" ).on( "click", ".deleteplaylistclick", function(event) {
        event.preventDefault();
        
        var current=$(this);

        var playlistid=current.data('itemid');

        $('#playlistitemwrap_'+playlistid).remove();


      });


      $("#logreg-forms").on('focus', '.suggestgenre' ,function(){

      $( this).each(function(i, el) {

        $(el).autocomplete({
      
		
        minLength: 2,
        delay:5,
        source: function (request, response) {

            request._token=csrf_token;
            $.ajax({
          type: "POST",
          url:base+'ajax/suggestgenre',
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
			
			$( "#logreg-forms" ).find('.suggestgenre:focus').val(ui.item.label);
			
			
			
            return false;
        }
    }).on('focus', function() { $(this).keydown(); })
    .data('ui-autocomplete')._renderItem = function (ul, item) {
		
		ul.addClass('suggestionsearch-table'); //Ul custom class here
		
		
		return $("<li class='brandsearchwrap'></li>")
            .data( "item.autocomplete", item )
            .append( "<a><div class='infowrap infowrap2'><span>Genre: </span><span class='title brandtitle'>" + item.label + "</span>" + "</div></a>" )
            .appendTo( ul );
		
    };

});
});	
  


});