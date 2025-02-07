@extends('admin.layouts.master')

@section('title', 'Provider Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Update Profile</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('provider.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->guard('provider')->user()->name }}" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->guard('provider')->user()->email }}" required>
                        </div>

                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ auth()->guard('provider')->user()->phone }}" required>
                        </div>

                        <div class="form-group">
                            <label>Telegram Username</label>
                            <input type="text" name="telegram_username" class="form-control" value="{{ auth()->guard('provider')->user()->telegram_username }}" required>
                        </div>

                        <div class="form-group">
                            <label>Telegram Chat ID</label>
                            <input type="text" name="telegram_chat_id" class="form-control" value="{{ auth()->guard('provider')->user()->telegram_chat_id }}" required>
                            <small class="form-text text-muted">
                                To get your Chat ID:
                                <ol>
                                    <li>Open Telegram</li>
                                    <li>Search for @LaundrySystem_bot</li>
                                    <li>Send the /start command</li>
                                    <li>Copy the Chat ID from the bot's response</li>
                                </ol>
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control" value="{{ auth()->guard('provider')->user()->address }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 