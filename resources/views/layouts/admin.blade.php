<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    
    <!-- Include your CSS files from public/admin -->
    <link rel="stylesheet" href="{{ asset('admin/css/your-admin-style.css') }}">
    <!-- Add other CSS files as needed -->
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        @include('layouts.partials.sidebar')
    </div>

    <!-- Main content -->
    <div class="main-content">
        <!-- Header -->
        @include('layouts.partials.header')

        <!-- Page content -->
        <div class="content">
            @yield('content')
        </div>
    </div>

    <!-- Include your JavaScript files from public/admin -->
    <script src="{{ asset('admin/js/your-admin-script.js') }}"></script>
    <!-- Add other JS files as needed -->
</body>
</html> 