$( document ).ready(function() {

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });


if(parseFloat(item_count.replace(/,/g, ''))>0 && searchset=='1')
{
    $('html, body').animate({
        scrollTop: $("#searchresults").offset().top-20
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