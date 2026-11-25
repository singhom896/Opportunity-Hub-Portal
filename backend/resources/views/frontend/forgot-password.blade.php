@extends('frontend.layouts.app')

@section('title', 'Forgot Password')

@section('content')
<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <a class="brx-page-header__crumb-link" href="{{ route('login') }}">Login</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Forgot Password</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>
        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Forgot Password</span>
        </h1>
        <small class="brx-page-header__description">
            Reset your password to securely access your registered profile.
        </small>
    </div>
</section>

<main class="auth-main py-4 py-lg-5">
    <div class="container">
        <div class="auth-shell">
            <div class="card auth-card border-0">
                <div class="auth-head text-center">
                    <h2 class="auth-title">Forgot Your Password?</h2>
                    <p class="text-muted small mb-0">
                        Enter your registered email address. If an account exists, you will receive a password reset link.
                    </p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success py-2 px-3 mb-3" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger py-2 px-3 mb-3" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="forgotPasswordForm"
                      class="login-auth-form"
                      action="{{ route('frontend.password.email') }}"
                      method="POST"
                      autocomplete="off">
                    @csrf

                    <div class="mb-3">
                        <label for="resetEmail" class="form-label fw-semibold">Email Address</label>
                        <input type="email"
                               name="email"
                               id="resetEmail"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="you@example.com"
                               value="{{ old('email') }}"
                               autocomplete="email"
                               maxlength="255"
                               required
                               autofocus>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" id="resetButton" class="btn auth-submit w-100">
                        Send Password Reset Link
                    </button>

                    <p class="auth-switch text-center mb-0 mt-4 small">
                        Remember your password?
                        <a href="{{ route('login') }}" class="fw-semibold">Back to Login</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $("#forgotPasswordForm").validate({
        ignore: [],
        errorClass: "text-danger",
        errorElement: "small",
        rules: {
            email: {
                required: true,
                email: true,
                maxlength: 255
            }
        },
        messages: {
            email: {
                required: "Please enter your registered email address.",
                email: "Please enter a valid email address.",
                maxlength: "Email address cannot exceed 255 characters."
            }
        },
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        },
        submitHandler: function (form) {
            $("#resetButton").prop("disabled", true).text("Please wait...");
            form.submit();
        }
    });

    window.addEventListener("pageshow", function () {
        $("#resetButton").prop("disabled", false).text("Send Password Reset Link");
    });
});
</script>
@endpush
