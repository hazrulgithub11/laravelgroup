<div class="sidebar">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="{{ route('provider.dashboard') }}" class="simple-text logo-mini">
                LP
            </a>
            <a href="{{ route('provider.dashboard') }}" class="simple-text logo-normal">
                Laundry Provider
            </a>
        </div>
        <ul class="nav">
            <li class="{{ Request::is('provider/dashboard') ? 'active' : '' }}">
                <a href="{{ route('provider.dashboard') }}">
                    <i class="tim-icons icon-chart-pie-36"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="{{ Request::is('provider/orders*') ? 'active' : '' }}">
                <a href="{{ route('provider.orders.index') }}">
                    <i class="tim-icons icon-basket-simple"></i>
                    <p>Orders</p>
                </a>
            </li>
            <li class="{{ Request::is('provider/profile*') ? 'active' : '' }}">
                <a href="{{ route('provider.profile.edit') }}">
                    <i class="tim-icons icon-single-02"></i>
                    <p>My Profile</p>
                </a>
            </li>
            <li class="{{ Request::is('provider/settings*') ? 'active' : '' }}">
                <a href="#">
                    <i class="tim-icons icon-settings"></i>
                    <p>Settings</p>
                </a>
            </li>
        </ul>
    </div>
</div> 