@extends('layouts.frontend')

@section('title', 'Sign In | ' . (optional($appSetting)->site_name ?: 'Maanyas'))

@section('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/auth-login.min.css') }}">
@endsection

@section('content')
<section class="auth-page auth-page--login">
    <div class="auth-frame">
        <div class="auth-showcase">
            <a href="{{ route('storefront') }}" class="auth-brand">{{ optional($appSetting)->site_name ?: 'Maanyas' }}<span>.</span></a>
            <div class="auth-showcase-copy">
                <p class="auth-eyebrow">Welcome back</p>
                <h1>Your jewellery journey continues.</h1>
                <p>Sign in to manage your orders and enjoy a faster checkout.</p>
            </div>
            <div class="auth-showcase-mark"><i class="bi bi-arrow-up-right"></i></div>
        </div>

        <div class="auth-form-panel">
            <div class="auth-heading">
                <p class="auth-eyebrow">Member access</p>
                <h2>Sign in</h2>
                <p>Enter your account details to continue.</p>
            </div>

            @if(session('error'))
                <div class="auth-alert auth-alert--error">{{ session('error') }}</div>
            @endif
            @if(session('status'))
                <div class="auth-alert auth-alert--success">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="auth-alert auth-alert--error">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf
                <div class="auth-field">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="you@example.com">
                </div>
                <div class="auth-field">
                    <label for="password">Password</label>
                    <div class="auth-password-wrap">
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                        <button type="button" class="auth-password-toggle" onclick="togglePass('password', this)" aria-controls="password" aria-pressed="false">Show</button>
                    </div>
                </div>
                <div class="auth-form-options">
                    <label class="auth-check"><input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> <span>Remember me</span></label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>
                <button type="submit" class="auth-submit">Sign in <i class="bi bi-arrow-right"></i></button>
            </form>

            <p class="auth-switch">New here? <a href="{{ route('register') }}">Create an account</a></p>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
    iconId.textContent = input.type === 'password' ? 'Show' : 'Hide';
    iconId.setAttribute('aria-pressed', input.type === 'text' ? 'true' : 'false');
    iconId.setAttribute('aria-label', input.type === 'text' ? 'Hide password' : 'Show password');
}
</script>
@endpush
