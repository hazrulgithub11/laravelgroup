<nav class="navbar">
    <div class="navbar-left">
        <button class="sidebar-toggle">☰</button>
    </div>
    <div class="navbar-right">
        <div class="user-menu">
            <span>Welcome, {{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</nav> 