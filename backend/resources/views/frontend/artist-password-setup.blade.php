@extends('frontend.layouts.app')

@section('title', 'Set Account Password')

@section('content')
<section class="auth-banner" aria-labelledby="authBannerTitle">
    <div class="container">
        <nav class="auth-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ url('/') }}">Home</a>
            <span class="auth-sep" aria-hidden="true">/</span>
            <span class="auth-current" aria-current="page">Set Password</span>
        </nav>
        <span class="auth-banner-kicker">BRICS ARTIST REGISTRY</span>
        <h1 class="auth-banner-title" id="authBannerTitle">Set Account Password</h1>
        <p class="auth-banner-sub mb-0">Activate your BRICS Artist Registry account to access your portfolio.</p>
    </div>
</section>

<main class="auth-main py-5">
    <div class="container">
        <div class="auth-shell">
            <div class="card auth-card border-0">
                <div class="auth-head text-center">
                    <span class="auth-label">ACCOUNT ACTIVATION</span>
                    <h2 class="auth-title">Create Password</h2>
                    <p class="auth-sub">Choose a secure password of at least 8 characters to activate your account.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="post" action="{{ route('artist-account.password.update', ['token' => $token]) }}" class="login-auth-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="password">Password</label>
                        <input class="form-control" id="password" name="password" type="password" minlength="8" autocomplete="new-password" placeholder="Enter new password (min. 8 characters)" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="password_confirmation">Confirm Password</label>
                        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" placeholder="Confirm your new password" required>
                    </div>
                    <button class="btn auth-submit w-100" type="submit">Activate Account &amp; Proceed</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
