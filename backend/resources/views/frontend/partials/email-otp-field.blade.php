{{--
    Reusable email OTP verification field for the artist registration forms.
    Params: $label (string), $placeholder (string, optional), $help (string, optional)
    The field is wired to the OTP handlers in artist-registration.blade.php.
--}}
@php
    $verifiedEmail = (string) session()->get('email_otp_verified.registration', '');
    $currentEmail = (string) old('email', '');
    $emailAlreadyVerified = $verifiedEmail !== ''
        && $currentEmail !== ''
        && strcasecmp($verifiedEmail, $currentEmail) === 0
        && ! $errors->has('email');
@endphp
@if (! config('otp.email_enabled'))
{{-- Email OTP switched off (config/otp.php): plain email field, no OTP steps. --}}
<div class="field">
    <label class="artist-registration-label">{{ $label }} <span>*</span></label>
    <input type="email" name="email" value="{{ old('email') }}" maxlength="60" required
           class="form-control artist-registration-control emailId"
           placeholder="{{ $placeholder ?? 'Enter email address' }}" data-verified="1">
    <span class="artist-registration-help">Primary communication address.</span>
</div>
@else
<div class="field field-otp">
    <label class="artist-registration-label">{{ $label }} <span>*</span></label>
    <div class="d-flex flex-wrap align-items-start">
        <div class="position-relative w-100 flex-grow-1">
            <input type="email" name="email" value="{{ old('email') }}" maxlength="60" required
                   class="form-control artist-registration-control emailId"
                   placeholder="{{ $placeholder ?? 'Enter email address' }}"
                   data-verified="{{ $emailAlreadyVerified ? '1' : '0' }}"
                   {{ $emailAlreadyVerified ? 'readonly' : '' }}>
            <a href="javascript:void(0)" class="text-dark position-absolute top-50 end-0 translate-middle-y me-2 edit_mail d-none" title="Edit email" aria-label="Edit email"><i class="bi bi-pencil-square fs-5"></i></a>
            <span class="position-absolute top-50 end-0 translate-middle-y me-2 verifyEmail text-success {{ $emailAlreadyVerified ? '' : 'd-none' }}" title="Email verified" aria-label="Email verified"><i class="bi bi-patch-check-fill fs-5"></i></span>
        </div>
        <div class="position-relative mx-1">
            <input type="text" name="otp" class="form-control artist-registration-control email_otp d-none" placeholder="Enter OTP" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
        </div>
        <button type="button" class="input-group-text btn my-bg2 otpSendBtn {{ $emailAlreadyVerified ? 'd-none' : '' }}">Send OTP</button>
        <button type="button" class="input-group-text my-bg2 btn btn-sm d-none otpVerifyBtn">Verify OTP</button>
    </div>
    <span class="error fw-bold timer d-none"></span>
    <span class="text-primary reSendBtn fs-7 d-none" style="cursor:pointer">Resend OTP</span>
    <div class="otpMsg fw-bold" aria-live="polite"></div>
    <span class="artist-registration-help">{{ $help ?? 'Click "Send OTP" and enter the one-time password emailed to you. The registration can be submitted only after the email is verified.' }}</span>
</div>
@endif
