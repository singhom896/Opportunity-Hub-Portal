@extends('frontend.layouts.app')

@section('title', 'Reset Password')

@section('content')
<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <a class="brx-page-header__crumb-link" href="{{ route('login') }}">Login</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Reset Password</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>
        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Reset Password</span>
        </h1>
        <small class="brx-page-header__description">
            Create a new password for your registered account.
        </small>
    </div>
</section>

<main class="auth-main py-4 py-lg-5">
    <div class="container">
        <div class="auth-shell">
            <div class="card auth-card border-0">
                <div class="auth-head text-center">
                    <h2 class="auth-title">Create New Password</h2>
                    <p class="text-muted small mb-0">
                        Choose a strong password that you do not use on other websites.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 px-3 mb-3" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="resetPasswordForm"
                      class="login-auth-form"
                      method="POST"
                      action="{{ route('frontend.password.update') }}"
                      autocomplete="off">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ old('email', $email) }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="password">New Password</label>
                        <div class="input-group">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Enter new password"
                                   autocomplete="new-password"
                                   minlength="8"
                                   required>
                            <button type="button" class="btn btn-outline-secondary" id="toggleNewPassword"
                                    aria-label="Show password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            Minimum 8 characters, including uppercase, lowercase, number and special character.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="password_confirmation">
                            Confirm New Password
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   class="form-control"
                                   placeholder="Confirm new password"
                                   autocomplete="new-password"
                                   required>
                            <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword"
                                    aria-label="Show confirm password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="resetButton" class="btn auth-submit w-100">
                        Reset Password
                    </button>

                    <p class="auth-switch text-center mb-0 mt-4 small">
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
    $.validator.addMethod(
        "strongPassword",
        function (value, element) {
            return this.optional(element) ||
                /^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).+$/.test(value);
        },
        "Use uppercase, lowercase, a number and a special character."
    );

    $("#resetPasswordForm").validate({
        errorClass: "text-danger",
        errorElement: "small",
        rules: {
            password: {
                required: true,
                minlength: 8,
                pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/
            },
            password_confirmation: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            password: {
                required: "Please enter your new password.",
                minlength: "Password must be at least 8 characters.",
                strongPassword: "Use uppercase, lowercase, a number and a special character."
            },
            password_confirmation: {
                required: "Please confirm your new password.",
                equalTo: "Passwords do not match."
            }
        },
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },
        errorPlacement: function (error, element) {
            if (element.closest(".input-group").length) {
                error.insertAfter(element.closest(".input-group"));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function (form) {
            $("#resetButton").prop("disabled", true).text("Please wait...");
            form.submit();
        }
    });

    $("#toggleNewPassword, #toggleConfirmPassword").on("click", function () {
        const input = $(this).attr("id") === "toggleNewPassword"
            ? $("#password")
            : $("#password_confirmation");
        const icon = $(this).find("i");
        const show = input.attr("type") === "password";

        input.attr("type", show ? "text" : "password");
        icon.toggleClass("bi-eye", !show).toggleClass("bi-eye-slash", show);
        $(this).attr("aria-label", show ? "Hide password" : "Show password");
    });

    window.addEventListener("pageshow", function () {
        $("#resetButton").prop("disabled", false).text("Reset Password");
    });
});
</script>
@endpush
