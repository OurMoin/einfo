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

    
    <!-- Firebase SDK v8 (Legacy) -->
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js"></script>
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
                            
                            $hasPlacedOrders = \App\Models\Order::where('user_id', $userId)->exists();
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
    // Global variables
    window.userAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
    window.Laravel = {
        user: @json(auth()->user())
    };

    // Firebase Configuration
    const firebaseConfig = {
        apiKey: "{{ env('FIREBASE_API_KEY', 'AIzaSyAuFHcuEyq070sM7Pgt4JyriybPnNEq6M4') }}",
        authDomain: "{{ env('FIREBASE_AUTH_DOMAIN', 'einfo-e95ba.firebaseapp.com') }}",
        projectId: "{{ env('FIREBASE_PROJECT_ID', 'einfo-e95ba') }}",
        storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET', 'einfo-e95ba.firebasestorage.app') }}",
        messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID', '438009665395') }}",
        appId: "{{ env('FIREBASE_APP_ID', '1:438009665395:web:d74475efa497609b58d706') }}",
        measurementId: "{{ env('FIREBASE_MEASUREMENT_ID', 'G-DT4NHYZG47') }}"
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    // VAPID Key from Laravel env
    const vapidKey = '{{ env("FIREBASE_VAPID_KEY") }}';

    // Global FCM token variable
    let currentFCMToken = null;

    // Initialize notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeNotifications();
    });

    async function initializeNotifications() {
        try {
            // Check if notifications are supported
            if (!('Notification' in window)) {
                console.log('This browser does not support notifications.');
                return;
            }

            // Request permission immediately when page loads
            const permission = await Notification.requestPermission();
            
            if (permission === 'granted') {
                console.log('Notification permission granted.');
                
                // Register service worker
                if ('serviceWorker' in navigator) {
                    try {
                        const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                        console.log('Service Worker registered successfully');
                        
                        // Get FCM token
                        const token = await messaging.getToken({ 
                            vapidKey: vapidKey,
                            serviceWorkerRegistration: registration
                        });
                        
                        if (token) {
                            console.log('FCM Token generated:', token);
                            currentFCMToken = token;
                            
                            // Save token if user is authenticated
                            if (window.userAuthenticated === 'true') {
                                saveTokenToDatabase(token);
                            } else {
                                // Store token for later use when user logs in
                                localStorage.setItem('fcm_token', token);
                                console.log('Token stored for later use (user not logged in)');
                            }
                        } else {
                            console.log('No registration token available.');
                        }
                    } catch (swError) {
                        console.error('Service Worker registration failed:', swError);
                    }
                }
            } else {
                console.log('Notification permission denied.');
            }
        } catch (error) {
            console.error('Error initializing notifications:', error);
        }
    }

    // Function to save token to database
    function saveTokenToDatabase(token) {
        if (window.userAuthenticated !== 'true') {
            console.log('User not authenticated, cannot save token');
            return;
        }

        fetch('/save-fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ fcm_token: token })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Token saved successfully:', data);
            // Remove from localStorage after successful save
            localStorage.removeItem('fcm_token');
        })
        .catch(error => {
            console.error('Error saving token:', error);
        });
    }

    // Handle foreground messages
    messaging.onMessage((payload) => {
        console.log('Message received in foreground:', payload);
        
        // Show notification manually if page is active
        if (Notification.permission === 'granted') {
            const notification = new Notification(payload.notification.title, {
                body: payload.notification.body,
                icon: payload.notification.icon || 'https://einfo.site/logo.png',
                badge: 'https://einfo.site/logo.png',
                tag: 'firebase-notification'
            });
            
            notification.onclick = function() {
                window.focus();
                notification.close();
            };
        }
    });

    // Check for stored token when user logs in (call this after login)
    function handleUserLogin() {
        const storedToken = localStorage.getItem('fcm_token');
        if (storedToken) {
            console.log('Found stored FCM token, saving to database...');
            saveTokenToDatabase(storedToken);
        }
    }

    // Other utility functions
    function clearCartOnLogout() {
        localStorage.removeItem('cart');
        localStorage.removeItem('fcm_token'); // Also clear FCM token
    }

    function handleSearch(event) {
        event.preventDefault();
        const searchTerm = document.getElementById('searchInput').value;
        if (searchTerm.trim()) {
            window.location.href = '/search?q=' + encodeURIComponent(searchTerm);
        }
    }

    // Make functions globally available
    window.handleUserLogin = handleUserLogin;
    window.saveTokenToDatabase = saveTokenToDatabase;


    // Login form submit এর আগে FCM token set করুন
document.addEventListener('DOMContentLoaded', function() {
    // Existing notification code...
    
    // Login form এ FCM token add করার function
    function setFcmTokenInForm() {
        const fcmTokenField = document.getElementById('fcm_token_field');
        if (fcmTokenField && currentFCMToken) {
            fcmTokenField.value = currentFCMToken;
        } else if (fcmTokenField) {
            // localStorage থেকে নিন
            const storedToken = localStorage.getItem('fcm_token');
            if (storedToken) {
                fcmTokenField.value = storedToken;
            }
        }
    }
    
    // Login form submit এর সময় token set করুন
    const loginForm = document.querySelector('form[method="POST"]');
    if (loginForm && loginForm.action.includes('login')) {
        loginForm.addEventListener('submit', function() {
            setFcmTokenInForm();
        });
    }
});

// Handle foreground messages - এই part add করুন যদি না থাকে
messaging.onMessage((payload) => {
    console.log('Message received in foreground:', payload);
    
    // Show browser notification manually
    if (Notification.permission === 'granted') {
        const notification = new Notification(payload.notification.title, {
            body: payload.notification.body,
            icon: payload.notification.icon || 'https://einfo.site/logo.png',
            badge: 'https://einfo.site/logo.png',
            tag: 'firebase-notification',
            requireInteraction: true
        });
        
        notification.onclick = function() {
            window.focus();
            // Navigate to order page
            if (payload.data && payload.data.click_action) {
                window.location.href = payload.data.click_action;
            }
            notification.close();
        };
    }
});

messaging.onMessage((payload) => {
    console.log('Message received in foreground:', payload);
    // Show notification
});

</script>



<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>