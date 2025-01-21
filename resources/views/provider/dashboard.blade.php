@extends('layouts.app')

@section('content')
<div class="container"></div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __('Provider Dashboard') }}
                    <form method="POST" action="{{ route('provider.logout') }}" class="float-right">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('Welcome to your provider dashboard!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 