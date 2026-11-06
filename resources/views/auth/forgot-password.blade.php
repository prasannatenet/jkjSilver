@extends('layouts.admin-guest')

@section('title', 'Forgot Password | Admin')

@section('content')
<div class="text-center mb-4">
    <i class="bi bi-key fs-1 text-primary"></i>
    <h4 class="fw-bold mt-2">Forgot Password</h4>
    <p class="text-muted">Enter your email to receive a reset link</p>
</div>

                        @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
                        @endif
                        @if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="Enter your registered email">
                            </div>
    <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg">Send Reset Link</button>
                    </div>
    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none">Back to login</a>
                </div>
</form>
@endsection
