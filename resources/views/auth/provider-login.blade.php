@extends('layouts.app', ['class' => 'login-page', 'page' => __('Provider Login'), 'contentClass' => 'login-page'])

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6">
            <form class="form" method="post" action="{{ route('provider.login.submit') }}">
                @csrf
                <div class="card" style="background: #27293d;">
                    <div class="card-header text-center py-3" style="background: linear-gradient(to bottom right, #e14eca, #ba54f5);">
                        <h3 class="card-title text-white mb-0">{{ __('Provider Login') }}</h3>
                    </div>
                    <div class="card-body px-4 py-4">
                        @if ($errors->has('email'))
                            <div class="alert alert-danger mb-4">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                        
                        <div class="input-group mb-4">
                            <div class="input-group-prepend">
                                <div class="input-group-text" style="background: linear-gradient(to bottom right, #e14eca, #ba54f5);">
                                    <i class="tim-icons icon-email-85 text-white"></i>
                                </div>
                            </div>
                            <input type="email" name="email" class="form-control" placeholder="Email" 
                                   style="background: #2b3553; border: 1px solid #e14eca; color: white;">
                        </div>

                        <div class="input-group mb-4">
                            <div class="input-group-prepend">
                                <div class="input-group-text" style="background: linear-gradient(to bottom right, #e14eca, #ba54f5);">
                                    <i class="tim-icons icon-lock-circle text-white"></i>
                                </div>
                            </div>
                            <input type="password" name="password" class="form-control" placeholder="Password"
                                   style="background: #2b3553; border: 1px solid #e14eca; color: white;">
                        </div>

                        <div class="form-check text-left mb-4">
                            <label class="form-check-label text-white d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" name="remember">
                                <span class="form-check-sign ml-2">{{ __('Remember me') }}</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-lg btn-block mb-4" 
                                style="background: linear-gradient(to bottom right, #e14eca, #ba54f5); color: white;">
                            {{ __('Login') }}
                        </button>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('provider.register') }}" style="color: #e14eca;">
                                {{ __('Create Account') }}
                            </a>
                            <a href="{{ route('password.request') }}" style="color: #e14eca;">
                                {{ __('Forgot Password?') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-control {
    height: 45px;
    font-size: 1rem;
}

.input-group-text {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 0;
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

body {
    background: #1e1e2f;
}

.form-check-sign {
    font-size: 0.95rem;
}
</style>
@endsection 