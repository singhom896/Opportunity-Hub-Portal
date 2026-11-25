@extends('frontend.layouts.app')

@section('title', 'Login')

@section('content')

<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Login</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Account Login</span>
        </h1>
        <small class="brx-page-header__description">Access your registered profile, opportunities, and international network.</small>
    </div>
</section>


<main class="auth-main py-4 py-lg-5">
    <div class="container">
        <div class="auth-shell">
            <div class="card auth-card border-0">
                <div class="auth-head text-center">
                    <h2 class="auth-title">Sign In</h2>
                    <!--p class="auth-sub">Enter your credentials to access your BRICS Artist Registry account.</p-->
                </div>

                @if(session('success'))
                    <div class="alert alert-success py-2 px-3 mb-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger py-2 px-3 mb-3">
                        {{ session('error') }}
                    </div>
                @endif
				
				@if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="loginForm" class="login-auth-form" action="{{ route('login') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="mb-3">
                        <label for="loginEmail" class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" id="loginEmail" class="form-control" placeholder="you@example.com" value="{{ old('email') }}" autocomplete="new-email" required>
                    </div>

                    <div class="mb-3">
                        <label for="loginPassword" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="loginPassword" class="form-control" placeholder="Enter your password" autocomplete="new-password" required>
                            <button type="button" class="btn btn-outline-secondary password-toggle" id="togglePassword" aria-label="Show password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Captcha Verification</label>
                        <div class="captcha-row d-flex align-items-center gap-2 mb-2">
                            <div class="captcha-image flex-grow-1" id="captcha-image">
                                {!! captcha_img() !!}
                            </div>
                            <button type="button" id="refresh-captcha" class="btn btn-outline-secondary captcha-refresh" title="Refresh Captcha" aria-label="Refresh Captcha">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                        <input type="text" name="captcha" id="captcha" class="form-control" placeholder="Enter captcha code" autocomplete="off" required>
                    </div>

                    <!--div class="auth-options d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                            <label class="form-check-label small" for="remember">Remember me</label>
                        </div>
                        <a href="#" class="auth-forgot small text-rigt">Forgot password?</a>
                    </div-->
					
					<div class="text-end mb-2">
						<a href="#" class="auth-forgot small">Forgot password?</a>
					</div>

                    <button type="submit" class="btn auth-submit w-100">
                        Login 
                    </button>

                    <p class="auth-switch text-center mb-0 mt-4 small">
                        New to the BRICS Artist Registry?
                        <a href="{{ url('artist-registration') }}" class="fw-semibold">Create an account</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/frontend/v1/js/core.js') }}"></script>
<script src="{{ asset('assets/frontend/v1/js/sha256.js') }}"></script>
<script>
$(document).ready(function(){
    $("#loginForm").validate({
        ignore:[],
        errorClass:"text-danger",
        errorElement:"small",
        rules:{
            email:{required:true,email:true},
            password:{required:true},
            captcha:{required:true}
        },
        messages:{
            email:{required:"Please enter your email.",email:"Please enter a valid email address."},
            password:{required:"Please enter your password."},
            captcha:{required:"Please enter captcha."}
        },
        highlight:function(element){
            $(element).addClass("is-invalid");
        },
        unhighlight:function(element){
            $(element).removeClass("is-invalid");
        },
        errorPlacement:function(error,element){
            if(element.closest(".input-group").length){
                error.insertAfter(element.closest(".input-group"));
            }else{
                error.insertAfter(element);
            }
        },
        submitHandler:function(form){
            form.submit();
        }
    });

    $("#refresh-captcha").on("click",function(){
        $("#captcha-image img").attr("src","{{ captcha_src('default') }}?"+Date.now());
        $('input[name="captcha"]').val("").removeClass("is-invalid");
    });

    $("#togglePassword").on("click",function(){
        var input=$("#loginPassword");
        var icon=$(this).find("i");

        if(input.attr("type")==="password"){
            input.attr("type","text");
            icon.removeClass("bi-eye").addClass("bi-eye-slash");
            $(this).attr("aria-label","Hide password");
        }else{
            input.attr("type","password");
            icon.removeClass("bi-eye-slash").addClass("bi-eye");
            $(this).attr("aria-label","Show password");
        }
    });
});
</script>
@endpush
