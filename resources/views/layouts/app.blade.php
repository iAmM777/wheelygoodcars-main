<!doctype html>
<html lang="nl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>WheelyGoodCars</title>
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark d-print-none bg-black">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('home') }}"><strong class="text-primary">Wheely</strong> good cars<strong class="text-primary">!</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link text-light" href="">Alle auto's</a></li>
                            @auth
                                <li class="nav-item"><a class="nav-link text-light" href="">Mijn aanbod</a></li>
                                <li class="nav-item"><a class="nav-link text-light" href="">Aanbod plaatsen</a></li>
                            @endauth
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        @guest
                            <li class="nav-item"><a class="nav-link text-secondary"   href="{{ route('register') }}">Registreren</a></li>
                            <li class="nav-item"><a class="nav-link text-secondary" href="{{ route('login') }}">Inloggen</a></li>
                        @endguest
                        @auth
                            <li class="nav-item"><a class="nav-link text-secondary"   href="{{ route('logout') }}">Uitloggen</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            @yield('content')
        </div>
    </body>
</html>
