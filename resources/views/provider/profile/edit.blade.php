@extends('provider.layouts.master')

@section('title', 'Edit Profile')

@push('css')
<style>
/* Override dark theme with white background */
body, 
.wrapper,
.main-panel,
.content {
    background: #ffffff !important;
    color: #2f3033 !important;
}

/* Make Dashboard title black */
.navbar-brand,
.navbar .navbar-brand,
.navbar h4,
.card h4,
.card-title {
    color: #000000 !important;
}

/* Style for the page wrapper */
.page-wrapper {
    background: #ffffff;
    min-height: 100vh;
    padding: 4rem 0;
    position: relative;
}

/* Make all headings black */
h1, h2, h3, h4, h5, h6 {
    color: #000000 !important;
}

/* Override any dark theme text colors */
.text-muted {
    color: #666666 !important;
}

/* Style for cards */
.card {
    background: #ffffff;
    border: 1px solid #e8e8e8;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

/* Form styles */
.form-control {
    background: #ffffff !important;
    border: 1px solid #e8e8e8;
    color: #2f3033 !important;
}

.form-control:focus {
    border-color: #1E856D;
    box-shadow: 0 0 0 0.2rem rgba(30, 133, 109, 0.25);
}

/* Button styles */
.btn-primary {
    background: #1E856D !important;
    border: none;
    color: #ffffff !important;
}

.btn-danger {
    background: #ff5b5b !important;
    border: none;
    color: #ffffff !important;
}

.btn-success {
    background: #1E856D !important;
    border: none;
    color: #ffffff !important;
}

/* Label styles */
label {
    color: #2f3033 !important;
    font-weight: 500;
}

/* Alert styles */
.alert-success {
    background: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-danger {
    background: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

/* Payment methods input group */
.input-group {
    margin-bottom: 1rem;
}

.input-group-append .btn {
    padding: 0.375rem 0.75rem;
}

/* Override sidebar color and text */
.sidebar {
    background: #1E856D !important;
}
.sidebar .nav li a p,
.sidebar .nav li a i,
.sidebar .logo a {
    color: #ffffff !important;
}

/* Override navbar color */
.navbar {
    background: #ffffff !important;
    border-bottom: 1px solid #e8e8e8;
}

/* Profile picture upload styles */
.profile-upload-box {
    width: 150px;
    height: 150px;
    position: relative;
    margin-bottom: 1rem;
}

.profile-input {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}

.profile-label {
    width: 100%;
    height: 100%;
    border: 2px dashed #1E856D;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #ffffff;
}

.profile-label:hover {
    border-color: #156952;
    background: #f8f9fa;
}

.upload-icon {
    font-size: 2rem;
    color: #1E856D;
    margin-bottom: 0.5rem;
}

.preview-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}
</style>
@endpush

@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="title">Edit Profile</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('provider.profile.update') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        
                        <!-- Profile Picture -->
                        <div class="form-group">
                            <label>Profile Picture</label>
                            <div class="profile-upload-box">
                                <input type="file" name="profile_picture" id="profile_picture" class="profile-input @error('profile_picture') is-invalid @enderror" accept="image/*" onchange="console.log('File selected:', this.files[0])">
                                <label for="profile_picture" class="profile-label">
                                    @if($provider->profile_picture)
                                        <img src="{{ asset('storage/' . $provider->profile_picture) }}" alt="Current Profile Picture" class="preview-image">
                                    @else
                                        <div class="upload-icon">
                                            <i class="tim-icons icon-simple-add"></i>
                                        </div>
                                        <span>Upload Photo</span>
                                    @endif
                                </label>
                            </div>
                            @error('profile_picture')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            @if(session('error'))
                                <div class="alert alert-danger mt-2">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>

                        <!-- Introduction -->
                        <div class="form-group">
                            <label>Introduction</label>
                            <textarea name="introduction" rows="4" class="form-control @error('introduction') is-invalid @enderror">{{ old('introduction', $provider->introduction) }}</textarea>
                            @error('introduction')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Years Experience -->
                        <div class="form-group">
                            <label>Years of Experience</label>
                            <input type="number" name="years_experience" value="{{ old('years_experience', $provider->years_experience) }}" class="form-control @error('years_experience') is-invalid @enderror">
                            @error('years_experience')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Payment Methods -->
                        <div class="form-group">
                            <label>Payment Methods</label>
                            <div class="payment-methods">
                                @php
                                    $paymentMethods = old('payment_methods', $provider->payment_methods ?? []);
                                    if (is_string($paymentMethods)) {
                                        $paymentMethods = json_decode($paymentMethods, true) ?? [];
                                    }
                                @endphp
                                
                                @foreach($paymentMethods as $index => $method)
                                    @if(!empty($method))
                                    <div class="input-group mb-2">
                                        <input type="text" name="payment_methods[]" value="{{ $method }}" class="form-control">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger remove-payment">Remove</button>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                                
                                <div class="input-group mb-2">
                                    <input type="text" name="payment_methods[]" class="form-control" placeholder="Add payment method">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success add-payment">Add</button>
                                    </div>
                                </div>
                            </div>
                            @error('payment_methods')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodsDiv = document.querySelector('.payment-methods');
    
    // Add new payment method field
    document.querySelector('.add-payment').addEventListener('click', function() {
        const newInput = document.createElement('div');
        newInput.className = 'input-group mb-2';
        newInput.innerHTML = `
            <input type="text" name="payment_methods[]" class="form-control">
            <div class="input-group-append">
                <button type="button" class="btn btn-danger remove-payment">Remove</button>
            </div>
        `;
        // Insert before the "Add payment method" input group
        const lastInput = paymentMethodsDiv.querySelector('.input-group:last-child');
        paymentMethodsDiv.insertBefore(newInput, lastInput);
    });
    
    // Remove payment method field - using event delegation
    document.addEventListener('click', function(e) {
        if (e.target && e.target.matches('.remove-payment, .remove-payment *')) {
            const button = e.target.closest('.remove-payment');
            if (button) {
                const inputGroup = button.closest('.input-group');
                if (inputGroup) {
                    inputGroup.remove();
                }
            }
        }
    });

    const input = document.getElementById('profile_picture');
    const label = document.querySelector('.profile-label');
    
    input.addEventListener('change', function(e) {
        console.log('File input change event:', this.files);
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                label.innerHTML = `<img src="${e.target.result}" alt="Profile Preview" class="preview-image">`;
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>
@endpush
@endsection
