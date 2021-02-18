<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="icon" href="{{ url('favicon.ico') }}">
    <title>
        @yield('title', config('app.name', 'Zenvision'))
    </title>
    <meta
        content="Zenvision"
        name="description" />
    <script src="/js/app.js" defer></script>
    <link href="/css/app-dark.css" rel="stylesheet" id="layout-css">
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
