<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('public/js/app.js') }}" defer></script>
    <script src="{{ asset('public/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/js/bootstrap-timepicker.js') }}" defer></script>
    <script src="{{ asset('public/js/bootstrap-datepicker.js') }}" defer></script>
    <script src="{{ asset('public/js/fullcalendar.js') }}" defer></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.25/datatables.min.js" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('public/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/bootstrap-timepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/bootstrap-datepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/fullcalendar.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.25/datatables.min.css"/>
</head>
<style>
    .navbar-brand {
        margin-right: 3rem;
    } 
</style>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                @auth
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <i class="fa fa-home"></i> Home
                    </a>
                    <a class="navbar-brand" href="{{ url('home/integrations') }}">
                        Integration
                    </a>
                    <a class="navbar-brand" href="{{ url('/home/custom_fields/category') }}">
                        Custom Fields
                    </a>
                    <a class="navbar-brand" href="{{ url('/home/calendar') }}">
                        Calendar
                    </a>
                @endauth
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <a class="navbar-brand">
                            <i class="fa fa-clock-o"></i> <span id="time">{{date('h:i A')}}</span>
                        </a>
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    @if(Auth::user()->isImpersonating())
                                        <a class="dropdown-item" href="{{ url('users/stop') }}">
                                            <i class="fa fa-sign-out"></i> Stop Impersonate
                                        </a>
                                    @else
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            <i class="fa fa-sign-out"></i> {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script>
        $(document).ready(function() {
            var base_url = "{{ url('/') }}";
            setInterval(get_timer, 60000);
        });

        function get_timer() {
            var base_url = "{{ url('/') }}";
            $.ajax({
                url: base_url+"/get_timer",
                cache: false,
                type: "GET",
                data: {},
                success: function(data) {
                    $('#time').html(data);
                },
            });
        }
    </script>
</body>
</html>
