
$( document ).ready(function() {

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });


    if(parseFloat(item_count.replace(/,/g, ''))>0)
    {
        $('html, body').animate({
            scrollTop: $("#tracks").offset().top-20
        }, 500);
    }


    $( "#searchform" ).submit(function( event ) {
    
    
        $('#defaultloading').removeClass('d-none');
    
    
        });

    //important cause of cache!!
$(window).bind("pageshow", function(event) {
    $('#defaultloading').addClass('d-none');
});
//important cause of cache!!


});