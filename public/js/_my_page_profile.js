
$( document ).ready(function() {

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
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