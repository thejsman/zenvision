<?php
return [
    //Environment=> test/production
    'env' => 'test',
    //Google Ads
    'production' => [
        'developerToken' => "d7bZvh9HH1hVLFn2kvR_Og",
        'clientCustomerId' => "CLIENT-CUSTOMER-ID",
        'userAgent' => "Zenvision Dev",
        'clientId' => "736720769834-eko04svp2029fgs0nt386ic87jakk154.apps.googleusercontent.com",
        'clientSecret' => "QQJhehLGIELpTcrV7t2MKij7",
        'refreshToken' => "1//0g3SK1-f4Ej04CgYIARAAGBASNwF-L9Irx8auEJyeNynrqYFzsVBK0Gj44KJU2OWSOklBGRJrkoxs_j8IcVpq-vncWOHrBAYj4gg"
    ],
    'test' => [
        'developerToken' => "d7bZvh9HH1hVLFn2kvR_Og",
        'clientCustomerId' => "CLIENT-CUSTOMER-ID",
        'userAgent' => "Zenvision Dev",
        'clientId' => "736720769834-eko04svp2029fgs0nt386ic87jakk154.apps.googleusercontent.com",
        'clientSecret' => "QQJhehLGIELpTcrV7t2MKij7",
        'refreshToken' => "1//0g3SK1-f4Ej04CgYIARAAGBASNwF-L9Irx8auEJyeNynrqYFzsVBK0Gj44KJU2OWSOklBGRJrkoxs_j8IcVpq-vncWOHrBAYj4gg"
    ],
    'oAuth2' => [
        'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
        'redirectUri' => 'http://localhost:8000/google-connect',
        'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
        'scope' => 'https://www.googleapis.com/auth/adwords'
    ]
];
