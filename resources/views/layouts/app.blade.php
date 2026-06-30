<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Publications</title>

         <!-- Styles -->

        <link href="{{ url('css/milligram.min.css') }}" rel="stylesheet">
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <link href="{{ url('css/profile.css') }}" rel="stylesheet">
        <link href="{{ url('css/publications.css') }}" rel="stylesheet">
        <link href="{{ url('css/home.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <!------------------------------------------>

        <!-- Scripts -->
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src="{{ url('js/app.js') }}" defer></script>
        <script src="https://js.pusher.com/7.0/pusher.min.js" defer></script>
        <script src="{{ asset('js/notifications.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/publications.js') }}" defer></script>


        <script type="text/javascript" src="{{ url('js/editFaq.js') }}" defer></script>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Versão mais recente do jQuery -->
        <script type="text/javascript" src="{{ url('js/search.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/group_search.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/challenges_search.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/invite_members.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/group_filter.js') }}" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            const notificationsRoute = "{{ route('profile.notifications') }}";
        </script>

    </head>
    <body>
        <div id="popup-notification-container" style="position: fixed; top: 10px; right: 10px; z-index: 9999; pointer-events: none;"></div>
        <main>
            <header>
                <script src="{{ asset('js/notifications.js') }}" defer></script>
                <script src="https://js.pusher.com/7.0/pusher.min.js" defer></script>
                <div class="header-top">
                    <div class="logo">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="site-logo">
                            Lumea
                        </a>
                    </div>
                    <div class="header-right-search">
                        <form action="{{ route('users.autocomplete') }}" method="GET" class="search-form">
                            <input type="text" id="username-search" class="search-bar" name="username" placeholder="Search by username" autocomplete="off" required>
                            <div id="search-dropdown" class="dropdown-menu" >
                                <!-- Resultados serão preenchidos aqui -->
                            </div>
                        </form>
                    </div>
                    <div class="user-controls">
                        @if (Auth::check())
                            <div class="user-info">
                                <a href="{{ route('profile.show', Auth::user()->id) }}">
                                    @php
                                        $profilePicture = 'storage/profile_pictures/' . Auth::user()->profile_picture;
                                        $defaultPicture = 'storage/profile_pictures/default.png';
                                    @endphp
                                    <img src="{{ asset(file_exists(public_path($profilePicture)) ? $profilePicture : $defaultPicture) }}" alt="Profile Picture" class="profile-picture">
                                </a>
                                <span class="username">{{ Auth::user()->username }}</span>
                            </div>
                            <div class="header-buttons">
                                <a class="button" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        @else
                            <div class="header-buttons">
                                <a class="button login-register-button" href="{{ route('login') }}">Login</a>
                                <a class="button login-register-button" href="{{ route('register') }}">Register</a>
                            </div>
                        @endif
                    </div>
                </div>
                <nav id="navbar" class="navbar">
                    <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('publications.index') }}" class="{{ request()->is('publications') ? 'active' : '' }}">Publications</a>
                    <a href="{{ route('groups.index') }}" class="{{ request()->is('groups') ? 'active' : '' }}">Groups</a>
                    <a href="{{ route('challenges.index') }}" class="{{ request()->is('challenges') ? 'active' : '' }}">Challenges</a>
                    @if (Auth::check() && Auth::user()->isAdmin())
                        <a href="{{ route('admin.panel') }}" class="{{ request()->is('admin') ? 'active' : '' }}">Admin Panel</a>
                    @endif
                </nav>
                    @if ($errors->any())
                        <div class="alert alert-danger" id="error-alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="header-right-search">
                            <form action="{{ route('users.autocomplete') }}" method="GET" class="search-form">
                                <input type="text" id="username-search" class="search-bar" name="username" placeholder="Search by username" autocomplete="off" required>
                                <div id="search-dropdown" class="dropdown-menu" style="display: none; position: absolute; background: white; border: 1px solid #ccc; z-index: 1000;">
                                    <!-- Resultados serão preenchidos aqui -->
                                </div>
                            </form>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger" id="error-alert">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <script>
                                setTimeout(function() {
                                    document.getElementById('error-alert').style.display = 'none';
                                }, 10000); // 10 seconds
                            </script>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success" id="success-alert">
                                {{ session('success') }}
                            </div>
                            <script>
                                setTimeout(function() {
                                    document.getElementById('success-alert').style.display = 'none';
                                }, 10000); // 10 seconds
                            </script>
                        @endif

                        @if (session('warning'))
                            <div class="alert alert-warning" id="warning-alert">
                                {{ session('warning') }}
                            </div>
                            <script>
                                setTimeout(function() {
                                    document.getElementById('warning-alert').style.display = 'none';
                                }, 10000); // 10 seconds
                            </script>
                        @endif

                        @if (session('info'))
                            <div class="alert alert-info" id="info-alert">
                                {{ session('info') }}
                            </div>
                            <script>
                                setTimeout(function() {
                                    document.getElementById('info-alert').style.display = 'none';
                                }, 10000); // 10 seconds
                            </script>
                        @endif
                    @endif
            </header>
            <section id="content">
                @yield('content')
            </section>
        </main>
        @yield('footer')
    </body>
</html>