@extends('layouts.app')

@section('content')
<div class="container-fluid" style="width: 100vw; height: 100vh; background-color: #1e1e2f; display: flex; justify-content: center; align-items: center; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999;">
    <div class="row justify-content-center" style="width: 80%">
        <div class="col-md-7">
            <div class="card" style="background: #27293d;">
                <div class="card-header text-center py-3" style="background: linear-gradient(to bottom right, #e14eca, #ba54f5);">
                    <h3 class="card-title text-white mb-0">{{ __('Login') }}</h3>
                </div>
                <form class="form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="card-body px-4 py-4">
                        <div class="input-group mb-4">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                   placeholder="Email"
                                   style="background: #2b3553; border: 1px solid #e14eca; color: white;">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="input-group mb-4">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="current-password"
                                   placeholder="Password"
                                   style="background: #2b3553; border: 1px solid #e14eca; color: white;">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-check text-left mb-4">
                            <label class="form-check-label text-white d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span class="form-check-sign ml-2">{{ __('Remember Me') }}</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-lg btn-block mb-4"
                                style="background: linear-gradient(to bottom right, #e14eca, #ba54f5); color: white;">
                            {{ __('Login') }}
                        </button>

                        <div class="text-center">
                            <a href="{{ url('register') }}" style="color: #e14eca;">
                                {{ __('Create Account') }}
                            </a>
                            <span class="mx-2 text-white">|</span>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" style="color: #e14eca;">
                                    {{ __('Forgot Password?') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
html, body {
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    height: 100vh !important;
    background: #1e1e2f !important;
    position: fixed !important;
    width: 100% !important;
}

.navbar, .navbar-brand, footer, .footer {
    display: none !important;
}

.container-fluid {
    padding: 0 !important;
    margin: 0 !important;
}

.main-panel, .content {
    margin: 0 !important;
    padding: 0 !important;
    background: #1e1e2f !important;
}

.wrapper {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    overflow: hidden !important;
    background: #1e1e2f !important;
}

* {
    margin-bottom: 0 !important;
}

.form-control {
    height: 45px;
    font-size: 1rem;
}

.form-control:focus {
    background: #2b3553;
    border-color: #e14eca;
    color: white;
    box-shadow: none;
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.card {
    border: 0;
    border-radius: 0.4rem;
    box-shadow: 0 1px 20px 0px rgba(0, 0, 0, 0.1);
}

.btn {
    height: 45px;
    font-size: 1rem;
    transition: opacity 0.3s ease;
}

.btn:hover {
    opacity: 0.9;
}

.form-check-sign {
    font-size: 0.95rem;
}
</style>
@endsection
