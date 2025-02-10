@extends('admin.layouts.master')

@section('title', 'Provider Profile')

@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="title">Profile</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('provider.profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 