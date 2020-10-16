<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}?<?php echo rand(); ?>=<?php echo rand(); ?>" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

@auth
    <script src="https://www.gstatic.com/firebasejs/7.18.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.18.0/firebase-database.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.18.0/firebase-analytics.js"></script>
    <script type="text/javascript">

        var session_id = "{!! (Session::getId())?Session::getId():'' !!}";
        var user_id = "{!! (Auth::user())?Auth::user()->id:'' !!}";

        // Your web app's Firebase configuration
        var firebaseConfig = {
            apiKey: '{{config('firebase.firebaseConfig.apiKey')}}',
            authDomain: '{{config('firebase.firebaseConfig.authDomain')}}',
            databaseURL: '{{config('firebase.firebaseConfig.databaseURL')}}',
            projectId: '{{config('firebase.firebaseConfig.projectId')}}',
            storageBucket: '{{config('firebase.firebaseConfig.storageBucket')}}',
            messagingSenderId: {{config('firebase.firebaseConfig.messagingSenderId')}},
            appId: '{{config('firebase.firebaseConfig.appId')}}',
            measurementId: '{{config('firebase.firebaseConfig.measurementId')}}'
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);

        var database = firebase.database();
        firebase.analytics();


        if ({!! Auth::user() !!}) {
            firebase.database().ref('/users/' + user_id + '/session_id').set(session_id);
        }

        firebase.database().ref('/users/' + user_id).on('value', function (snapshot2) {
            var v = snapshot2.val();

            if (v.session_id !== session_id) {

                toastr.error('Oturum Kapatılıyor...',"Hesabınıza başka bir yerden oturum açıldı!");

                setTimeout(function () {
                    window.location = '/logout';
                }, 4000);
            }
        });
    </script>
@endauth

<body>
    <div id="app">
        <main class="">
            @yield('content')
        </main>
    </div>
</body>
</html>
