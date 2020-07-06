<?php


return [

    'istestsite' => (bool) env('ISTESTSITE', true),

    'config' => [
        'server_url' => env('SERVER_URL', null),
        'image_url' => env('FACEBOOK_APP_SECRET', null),
        'sitename_caps' => env('SITENAME_CAPS', null),
    ],
	
	'captcha' => [
        'captcha_sitekey' => env('NOCAPTCHA_SITEKEY', null),
        'captcha_secret' => env('NOCAPTCHA_SECRET', null),
    ],
	'youtube' => [
        'youtube_api_key' => env('YOUTUBE_API_KEY', null),
    ],
	'contact' => [
        'sendto' => env('CONTACT_SEND_TO', null),
		'noreply' => env('CONTACT_NO_REPLY', null),
    ],
    'spotify' => [
        'clientid' => env('SPOTIFY_CLIENTID', null),
        'secret' => env('SPOTIFY_SECRET', null),
    ],

    'solve_recaptcha' => [
        'key' => env('SOLVE_RECAPTCHA_KEY', null),
    ],

    'phantom_js_location' => env('PHANTOMJS_LOCATION', null),

    'userlevels' => [
        'admin'=>1,
        'editor'=>2,
        'assistant'=>5,
        'default'=>10,
        'playlister'=>11,
        'artistmanager'=>12,
        'promoter'=>13,
        'follower'=>14,
    ],
];
