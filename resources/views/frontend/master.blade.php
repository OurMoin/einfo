<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom sticky-top">
    <div class="container-fluid d-flex align-items-center">
        <!-- Left: Logo -->
        <a class="navbar-brand" href="/">
            <img src="https://einfo.site/logo.png"
                 class="rounded-circle"
                 alt="User"
                 style="width:32px; height:32px; object-fit:cover;">
        </a>
        <!-- Center: Search -->
        <form class="flex-grow-1 mx-3 container" style="width:180px;" onsubmit="handleSearch(event)">
            <div class="position-relative w-100"> 
                <input id="searchInput" class="form-control text-center"
                       type="search" placeholder="Search" aria-label="Search">
                <button type="submit" id="searchIcon" class="position-absolute end-0 top-50 translate-middle-y pe-3 border-0 bg-transparent" style="display:none;">
                    <i class="bi bi-search"></i>
                </button>
            </div> 
        </form>
        <!-- Right: User / Guest Menu -->
        <ul class="navbar-nav">
            @auth
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center" href="javascript:void(0)" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('profile-image/' . (Auth::user()->image ?? 'default.png')) }}"
                             class="rounded-circle"
                             alt="User"
                             style="width:32px; height:32px; object-fit:cover;">
                    </a>
                    <ul class="dropdown-menu position-absolute" aria-labelledby="userDropdown" style="z-index:1050;">
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">Profile</a></li>
                        
                        @php
                            $userId = Auth::id();
                            
                            // Check if user has placed any orders (Buy section)
                            $hasPlacedOrders = \App\Models\Order::where('user_id', $userId)->exists();
                            
                            // Check if user has received any orders (Sell section)
                            $hasReceivedOrders = \App\Models\Order::where('vendor_id', $userId)->exists();
                        @endphp
                        
                        @if($hasPlacedOrders)
                            <li><a class="dropdown-item" href="{{ route('buy') }}">Buy</a></li>
                        @endif
                        
                        @if($hasReceivedOrders)
                            <li><a class="dropdown-item" href="{{ route('sell') }}">Sell</a></li>
                        @endif
                        
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Settings</a></li>
                        <li>
                            <!-- logoout with cart item remove -->
                            <form method="POST" action="{{ route('logout') }}" onsubmit="clearCartOnLogout()">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth
            @guest
                <li class="nav-item dropdown">
                    <a class="nav-link" href="javascript:void(0)" id="guestDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('profile-image/default.png') }}"
                             class="rounded-circle"
                             alt="User"
                             style="width:32px; height:32px; object-fit:cover;">
                    </a>
                    <ul class="dropdown-menu position-absolute" aria-labelledby="guestDropdown" style="z-index:1050;">
                        <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
                        <li><a class="dropdown-item" href="{{ route('register') }}">Signup</a></li>
                    </ul>
                </li>
            @endguest
        </ul>
    </div>
</nav>

<!-- Main Content -->
@yield('main-content')
@include('frontend.cart')


<script>
    window.userAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

    window.Laravel = {
        user: @json(auth()->user())
    };
</script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>