$( document ).ready(function() {


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


}); 