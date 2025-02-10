<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Cinema Booking</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="{{ asset('admin/src/black-stubs/resources/assets/css/nucleo-icons.css') }}" rel="stylesheet">

    <!-- Theme CSS -->
    <link href="{{ asset('admin/src/black-stubs/resources/assets/css/black-dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/src/black-stubs/resources/assets/css/theme.css') }}" rel="stylesheet">

    <!-- Add any additional CSS -->
    @stack('css')

    <style>
    /* Sidebar styles */
    .sidebar {
        background: #1E856D !important;  /* Green color */
    }

    .sidebar .sidebar-wrapper {
        background: #1E856D !important;
    }

    .sidebar .nav li a {
        color: #ffffff !important;
    }

    .sidebar .nav li.active > a,
    .sidebar .nav li > a:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
    }

    .sidebar .logo {
        background: #1E856D !important;
    }

    .sidebar .logo a {
        color: #ffffff !important;
    }

    .sidebar .simple-text {
        color: #ffffff !important;
    }

    /* Active menu item */
    .sidebar .nav li.active > a {
        background: rgba(255, 255, 255, 0.1) !important;
    }

    /* Hover effect */
    .sidebar .nav li > a:hover {
        background: rgba(255, 255, 255, 0.1) !important;
    }
    </style>

</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div id="sidebarToggle">
                <i class="fas fa-chevron-left"></i>
            </div>
            @include('admin.layouts.sidebar')
        </div>
        
        <div class="main-panel">
            <!-- Navbar -->
            @include('admin.layouts.topbar')
            
            <!-- Content -->
            <div class="content">
                @yield('content')
            </div>
            
            <!-- Footer -->
            @include('admin.layouts.footer')
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('admin/src/black-stubs/resources/assets/js/core/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/src/black-stubs/resources/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('admin/src/black-stubs/resources/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin/src/black-stubs/resources/assets/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>

    <!-- Control Center for Black Dashboard -->
    <script src="{{ asset('admin/src/black-stubs/resources/assets/js/black-dashboard.min.js') }}"></script>

    <!-- Add any additional scripts -->
    @stack('scripts')
</body>
</html> 