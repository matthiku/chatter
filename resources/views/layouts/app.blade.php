<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- PWA manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ChatterBox') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('js/manifest.js') }}" defer></script>
    <script src="{{ mix('js/vendor.js') }}" defer></script>
    <script src="{{ mix('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('static/vue2Dropzone.css') }}" rel="stylesheet">

    <!-- Add to home screen for Safari on iOS -->
    <!--meta name="apple-mobile-web-app-capable" content="yes" -->
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Chatter Box">
    <link rel="apple-touch-icon" href="static/icons/icon-152x152.png">

    <!-- tile icon for Windows -->
    <meta name="msapplication-TileImage" content="images/icons/icon-144x144.png">
    <meta name="msapplication-TileColor" content="#2F3BA2">

</head>


<body>
    <div id="app">

        <main class="py-0 py-md-2 py-xl-4">
            @yield('content')
        </main>

    </div>

    <script>
        window.chatter_server_data = {}
        window.chatter_server_data.locale = '{{ App::getLocale() }}'
        window.chatter_server_data.user = "{\"name\":\"guest\"}"
        window.chatter_server_data.frontend_timestamp = "{{ filemtime(base_path().'/public/js/app.js') }}"
        @auth
            window.chatter_server_data.user = "{!! addslashes(json_encode((Auth::user()))) !!}"
            window.chatter_server_data.chatroom_name = '{{ env('MAIN_CHATROOM_NAME', 'chatroom') }}'
        @endauth
    </script>
</body>
</html>
