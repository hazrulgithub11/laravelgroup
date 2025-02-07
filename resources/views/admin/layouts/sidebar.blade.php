<div class="sidebar" id="sidebar-sidebar">
    
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="{{ url('/') }}" class="simple-text logo-mini">
                LS
            </a>
            <a href="{{ url('/') }}" class="simple-text logo-normal">
                Laundry System
            </a>
        </div>
        <ul class="nav">
            <li class="{{ Request::is('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}">
                    <i class="tim-icons icon-chart-pie-36"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="{{ Request::is('orders*') ? 'active' : '' }}">
                <a href="{{ route('orders.index') }}">
                    <i class="tim-icons icon-delivery-fast"></i>
                    <p>My Orders</p>
                </a>
            </li>
            <li class="{{ Request::is('Favorites') ? 'active' : '' }}">
                <a href="#">
                    <i class="tim-icons icon-pin"></i>
                    <p>Favorites</p>
                </a>
            </li>
            <li class="{{ Request::is('Logout') ? 'active' : '' }}">
                <a href="#">
                    <i class="tim-icons icon-tag"></i>
                    <p>Logout</p>
                </a>
            </li>
        </ul>
    </div>
</div>

