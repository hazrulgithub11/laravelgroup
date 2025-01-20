<div class="sidebar">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="{{ url('/') }}" class="simple-text logo-mini">
                CB
            </a>
            <a href="{{ url('/') }}" class="simple-text logo-normal">
                Cinema Booking
            </a>
        </div>
        <ul class="nav">
            <li class="{{ Request::is('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}">
                    <i class="tim-icons icon-chart-pie-36"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li>
                <a href="{{ route('movies.index') }}">
                    <i class="tim-icons icon-video-66"></i>
                    <p>Movies</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="tim-icons icon-tag"></i>
                    <p>Bookings</p>
                </a>
            </li>
        </ul>
    </div>
</div> 