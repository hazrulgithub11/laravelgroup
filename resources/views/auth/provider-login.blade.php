@extends('layouts.app')

@section('content')
<!-- Add Back Button -->
<div class="position-fixed" style="top: 20px; left: 20px; z-index: 9999;">
    <a href="{{ url('/') }}" class="btn btn-link text-dark" style="text-decoration: none;">
        <i class="fas fa-arrow-left"></i>
        <span class="ms-2">Back</span>
    </a>
</div>

<div class="container-fluid" style="width: 100vw; height: 100vh; display: flex; justify-content: center; align-items: center; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9998;">
    <div class="row justify-content-center" style="width: 400px">
        <div class="col-12">
            <div class="card" style="border: none; border-radius: 1rem; box-shadow: 0 2px 15px rgba(0,0,0,0.1); background: white;">
                <div class="text-center pt-4 pb-2">
                    <h2 style="color: #1E856D; font-size: 2rem; font-weight: bold;">Provider Login</h2>
                </div>

                <form class="form" method="post" action="{{ route('provider.login.submit') }}">
                    @csrf
                    <div class="card-body px-4 py-2">
                        @if ($errors->has('email'))
                            <div class="alert alert-danger mb-4">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                        
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email"
                                   style="height: 45px; background: white; border: 2px solid #1E856D; border-radius: 0.5rem; color: #333; padding: 0.8rem;">
                        </div>

                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password"
                                   style="height: 45px; background: white; border: 2px solid #1E856D; border-radius: 0.5rem; color: #333; padding: 0.8rem;">
                        </div>

                        <div class="form-check text-left mb-3">
                            <label class="form-check-label d-flex align-items-center" style="color: #666;">
                                <input class="form-check-input" type="checkbox" name="remember">
                                <span class="form-check-sign ms-2">{{ __('Remember me') }}</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-lg btn-block mb-3"
                                style="background: #1E856D; color: white; height: 45px; width: 100%; border-radius: 0.5rem; border: none; transition: all 0.3s ease;">
                            {{ __('Log in') }}
                        </button>

                        <a href="{{ route('provider.register') }}" class="btn btn-lg btn-block mb-3"
                           style="background: white; color: #1E856D; height: 45px; width: 100%; border-radius: 0.5rem; border: 2px solid #1E856D; transition: all 0.3s ease;">
                            {{ __('Sign up') }}
                        </a>

                        <div class="text-center" style="font-size: 0.9rem; color: #666;">
                            By signing up you agree to our 
                            <a href="#" style="color: #1E856D; text-decoration: none;">Terms of Use</a> and
                            <a href="#" style="color: #1E856D; text-decoration: none;">Privacy Policy</a>.
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
        background: rgba(0, 0, 0, 0.5) !important;
        position: fixed !important;
        width: 100% !important;
    }

    .navbar, .navbar-brand, footer, .footer {
        display: none !important;
    }

    .form-control:focus {
        border-color: #1E856D;
        box-shadow: 0 0 0 0.2rem rgba(30, 133, 109, 0.25);
    }

    .btn:hover {
        opacity: 0.9;
    }
</style>
@endsection 