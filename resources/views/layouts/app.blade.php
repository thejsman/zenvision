<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="icon" href="{{ url('favicon.png') }}">
    <title>
        @yield('title', config('app.name', 'Zenvision'))
    </title>
    <meta content="Zenvision" name="description" />
    <script src="/js/app.js" defer></script>
    <link href="/css/app-dark.css" rel="stylesheet" id="layout-css">
    <link href="/css/custom.css" rel="stylesheet" id="custom-layout-css">
    <script src="https://cdn.teller.io/connect/connect.js"></script>
    <script src="https://unpkg.com/@shopify/app-bridge@2"></script>
    <script src="https://unpkg.com/@shopify/app-bridge-utils"></script>

    <script>
        (function() {
            var AppBridge = window['app-bridge'];
            var createApp = AppBridge.default;
            var getSessionToken = window['app-bridge-utils'].getSessionToken;
            var app = createApp({
                apiKey: '6475dbe1c3d0b763d819fc4d053d771e',
                shopOrigin: 'https://zenvision-local.myshopify.com'
            });

            getSessionToken(app).then(session_token => getPageContent(session_token)).catch(err => {
                alert('App authentication failed, try refreshing the page');
                console.log(err)
            });

            function getPageContent(session_token) {
                fetch('https://zenvision-local.myshopify.com', {
                    headers: new Headers({
                        'Authorization': 'Bearer ' + session_token
                    })
                }).then(data => data.json()).then(json => {
                    //do stuff
                    console.error(json);
                });
            }
        })();
    </script>
    <!-- Facebook Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '2769187703299918');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=2769187703299918&ev=PageView&noscript=1" /></noscript>
    <!-- End Facebook Pixel Code -->

    <!-- Start of zenvision Zendesk Widget script -->
    <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=c8946605-e2c7-441d-9c27-8435bedb0135"> </script>
    <!-- End of zenvision Zendesk Widget script -->

</head>

<body>
    <noscript>
        <strong>We're sorry but Zenvision doesn't work properly without JavaScript enabled. Please enable it to
            continue.</strong>
    </noscript>
    <div id="app">
        @yield('content')
    </div>
    <!-- built files will be auto injected -->
    @stack('scripts')
</body>

</html>