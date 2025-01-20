<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') - Cinema Booking</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet">

    <!-- Icons -->
    <link href="{{ asset('admin/src/black-stubs/resources/assets/css/nucleo-icons.css') }}" rel="stylesheet">

    <!-- Theme CSS -->
    <link href="{{ asset('admin/src/black-stubs/resources/assets/css/black-dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/src/black-stubs/resources/assets/css/theme.css') }}" rel="stylesheet">

    <!-- Add any additional CSS -->
    @stack('css')
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        @include('admin.layouts.sidebar')
        
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