<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .card-img-top {
            height: 100px;
            object-fit: cover;
            width: 100%;
        }
        .card-title {
            display: inline-block;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .price-tag {
            display: inline-block;
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        a {
            text-decoration: none;
        }
        @media (max-width: 767.98px) {
            .card-title {
                font-size: 0.9rem;
            }
            .price-tag {
                font-size: 0.7rem;
            }
        }
        @media (max-width: 575.98px) {
            .card-title {
                font-size: 0.8rem;
            }
            .price-tag {
                font-size: 0.7rem;
                max-width: 70px;
            }
        }
        .cart-badge {
            float: right;
        }
        @media (max-width: 400px) {
            .cart-badge {
                float: none;
                display: block;
                width: 100%;
                text-align: center;
                margin-top: 8px;
            }
        }

        /* Floating Cart Styles */
        .floating-cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            cursor: pointer;
        }

        .cart-icon {
            background: #007bff;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            position: relative;
        }

        .cart-icon:hover {
            background: #0056b3;
            transform: scale(1.1);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .cart-count.show {
            display: flex;
        }

        /* Cart Item Styles */
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .cart-item-price {
            color: #007bff;
            font-weight: 600;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .quantity-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 4px;
        }

        .quantity-btn:hover {
            background: #e9ecef;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-left: none;
            border-right: none;
            height: 30px;
        }

        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
        }

        .remove-btn:hover {
            background: #c82333;
        }

        /* Animation */
        @keyframes bounceIn {
            0% { transform: scale(0.3) rotate(180deg); opacity: 0; }
            50% { transform: scale(1.05) rotate(0deg); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }

        .cart-animate {
            animation: bounceIn 0.6s ease;
        }

        /* Input styles */
        input {
            outline: none !important;
            box-shadow: none !important;
        }

        input:focus {
            outline: none !important;
            box-shadow: none !important;
            border-color: inherit !important;
        }

        .navbar {
            --bs-navbar-padding-x: 0;
            --bs-navbar-padding-y: 0 !important;
        }

        .navbar-expand-lg .navbar-nav .nav-link {
            padding-right: 0;
            padding-left: 0;
        }

        .navbar-brand {
            margin-right: 0;
        }

        .navbar .dropdown-menu {
            right: 0 !important;
            left: auto !important;
            transform: none !important;
        }
    </style>
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
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Settings</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth
            @guest
                <li class="nav-item dropdown">
                    <a class="nav-link" href="javascript:void(0)" id="guestDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('profile-image/' . (Auth::user()->image ?? 'default.png')) }}"
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>