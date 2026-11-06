@extends('layouts.frontend')

@section('title', 'Create Account | ' . (optional($appSetting)->site_name ?: 'Maanyas'))

@section('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/auth-register.min.css') }}">
@endsection

@section('content')
<section class="auth-page auth-page--register">
    <div class="auth-frame">
        <div class="auth-showcase">
            <a href="{{ route('storefront') }}" class="auth-brand">{{ optional($appSetting)->site_name ?: 'Maanyas' }}<span>.</span></a>
            <div class="auth-showcase-copy">
                <p class="auth-eyebrow">Join the community</p>
                <h1>Find the pieces you love.</h1>
                <p>Create your account for faster checkout, order tracking, and member-only offers.</p>
            </div>
            <div class="auth-showcase-mark"><i class="bi bi-arrow-up-right"></i></div>
        </div>

        <div class="auth-form-panel">
            <div class="auth-heading">
                <p class="auth-eyebrow">Get started</p>
                <h2>Sign up</h2>
                <p>It is free to sign up and only takes a minute.</p>
            </div>

            @if($errors->any())
                <div class="auth-alert auth-alert--error">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf
                <div class="auth-two-fields">
                    <div class="auth-field">
                        <label for="name">Full name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Your full name">
                    </div>
                    <div class="auth-field">
                        <label for="email">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com">
                    </div>
                </div>
                <div class="auth-two-fields">
                    <div class="auth-field">
                        <label for="phone">Mobile number</label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" inputmode="tel" maxlength="20" placeholder="98765 43210">
                    </div>
                    <div class="auth-field">
                        <label for="password">Password</label>
                        <div class="auth-password-wrap">
                            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Create password">
                            <button type="button" class="auth-password-toggle" onclick="togglePass('password', this)" aria-controls="password" aria-pressed="false">Show</button>
                        </div>
                    </div>
                </div>
                <div class="auth-two-fields">
                    <div class="auth-field">
                        <label for="password_confirmation">Confirm password</label>
                        <div class="auth-password-wrap">
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                            <button type="button" class="auth-password-toggle" onclick="togglePass('password_confirmation', this)" aria-controls="password_confirmation" aria-pressed="false">Show</button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="auth-submit">Register now <i class="bi bi-arrow-right"></i></button>
            </form>

            <p class="auth-switch">Already a member? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function togglePass(inputId, toggle) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
    toggle.textContent = input.type === 'password' ? 'Show' : 'Hide';
    toggle.setAttribute('aria-pressed', input.type === 'text' ? 'true' : 'false');
    toggle.setAttribute('aria-label', input.type === 'text' ? 'Hide password' : 'Show password');
}
</script>
@endpush
