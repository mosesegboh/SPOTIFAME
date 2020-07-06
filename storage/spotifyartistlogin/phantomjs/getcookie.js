var webPage = require('webpage');
var page = webPage.create();

page.onInitialized = function() {
  page.injectJs('request-animation-frame.js');
};

// 20 seconds
page.settings.resourceTimeout = 20000;

page.open('https://accounts.spotify.com/en/login', function (status) {
  var cookies = page.cookies;
  /*
  console.log('Listing cookies:');
  for(var i in cookies) {
    console.log(cookies[i].name + '=' + cookies[i].value);
  }
  */

  var cookiestr='';
  var delim='';
 for(var i in cookies) {
    cookiestr+=delim+cookies[i].name + '=' + cookies[i].value;
    delim=';';
  }
  console.log(cookiestr);
  
  phantom.exit();
});

