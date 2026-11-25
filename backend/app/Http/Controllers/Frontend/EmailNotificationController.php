<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\EmailOtpMail;
use App\Services\EmailOtpService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EmailNotificationController extends Controller
{
    /**
     * Only these OTP purposes are accepted by this controller.
     */
    private const ALLOWED_PURPOSES = EmailOtpService::ALLOWED_PURPOSES;

    private const OTP_VALIDITY_MINUTES = 10;
    private const RESEND_COOLDOWN_SECONDS = 60;
    private const MAX_ATTEMPTS = 5;

    public function __construct(
        private readonly EmailOtpService $otpService,
        private readonly EmailService $emailService
    ) {
    }

    /**
     * Send an OTP to the supplied email address.
     * POST /email/otp/send
     */
    public function sendOtp(Request $request)
    {
        return $this->sendOtpResponse($request);
    }

    /**
     * Resend an OTP. The same cooldown and rate limits apply.
     * POST /email/otp/resend
     */
    public function resendOtp(Request $request)
    {
        return $this->sendOtpResponse($request);
    }

    /**
     * Verify an OTP and mark the email as verified for this purpose
     * in both the database and the current session.
     * POST /email/otp/verify
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'   => ['required', 'email:rfc', 'max:255'],
            'purpose' => ['required', 'string', 'in:' . implode(',', self::ALLOWED_PURPOSES)],
            'otp'     => ['required', 'digits:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $email = Str::lower(trim($request->input('email')));
        $purpose = $request->input('purpose');
        $otp = (string) $request->input('otp');

        $record = DB::table('email_otps')
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'OTP not found. Please request a new OTP.',
            ], 404);
        }

        if (!empty($record->verified_at)) {
            session()->put("email_otp_verified.{$purpose}", $email);

            return response()->json([
                'success' => true,
                'message' => 'Email has already been verified.',
            ]);
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            DB::table('email_otps')->where('id', $record->id)->delete();

            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new OTP.',
            ], 422);
        }

        if ((int) $record->attempts >= self::MAX_ATTEMPTS) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum OTP verification attempts reached. Please request a new OTP.',
            ], 429);
        }

        if (!Hash::check($otp, $record->otp_hash)) {
            $attempts = (int) $record->attempts + 1;

            DB::table('email_otps')
                ->where('id', $record->id)
                ->update([
                    'attempts'   => $attempts,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. ' . max(0, self::MAX_ATTEMPTS - $attempts) . ' attempt(s) remaining.',
            ], 422);
        }

        DB::table('email_otps')
            ->where('id', $record->id)
            ->update([
                'verified_at' => now(),
                'updated_at'  => now(),
            ]);

        session()->put("email_otp_verified.{$purpose}", $email);

        return response()->json([
            'success' => true,
            'message' => 'Email OTP verified successfully.',
            'email'   => $email,
            'purpose' => $purpose,
        ]);
    }

    /**
     * Check whether the current session has verified this email/purpose.
     * Use this from the relevant registration/login controller before saving.
     */
    public function isEmailVerified(string $email, string $purpose): bool
    {
        return $this->otpService->isVerified($email, $purpose);
    }

    /**
     * Consume a successful verification after the related action is completed.
     * This makes the verification one-time-use.
     */
    public function consumeVerification(string $email, string $purpose): bool
    {
        return $this->otpService->consume($email, $purpose);
    }

    /**
     * Shared send/resend implementation.
     */
    private function sendOtpResponse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'   => ['required', 'email:rfc', 'max:255'],
            'purpose' => ['required', 'string', 'in:' . implode(',', self::ALLOWED_PURPOSES)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $email = Str::lower(trim($request->input('email')));
        $purpose = $request->input('purpose');

        $existing = DB::table('email_otps')
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->first();

        if ($existing && !empty($existing->last_sent_at)) {
            $lastSentAt = Carbon::parse($existing->last_sent_at);
            $secondsPassed = $lastSentAt->diffInSeconds(now());

            if ($secondsPassed < self::RESEND_COOLDOWN_SECONDS) {
                $wait = self::RESEND_COOLDOWN_SECONDS - $secondsPassed;

                return response()->json([
                    'success' => false,
                    'message' => "Please wait {$wait} seconds before requesting another OTP.",
                    'retry_after' => $wait,
                ], 429);
            }
        }

        $otp = (string) random_int(100000, 999999);
        $now = now();

        DB::table('email_otps')->updateOrInsert(
            [
                'email'   => $email,
                'purpose' => $purpose,
            ],
            [
                'otp_hash'    => Hash::make($otp),
                'attempts'    => 0,
                'expires_at'  => $now->copy()->addMinutes(self::OTP_VALIDITY_MINUTES),
                'last_sent_at'=> $now,
                'verified_at' => null,
                'created_at'  => $existing->created_at ?? $now,
                'updated_at'  => $now,
            ]
        );

        // A new OTP invalidates any previous verification in this browser session.
        session()->forget("email_otp_verified.{$purpose}");
		


      /*  
	  $sent = $this->emailService->send(
            $email,
            EmailOtpMail::class,
            [$otp, $purpose, self::OTP_VALIDITY_MINUTES],
            config('mail_notifications.otp', true)
        );
       if (! $sent) {
            DB::table('email_otps')
                ->where('email', $email)
                ->where('purpose', $purpose)
                ->delete();

            return response()->json([
                'success' => false,
                'message' => 'Unable to send OTP email right now. Please try again later.',
            ], 500);
        } */

        return response()->json([
            'success' => true,
            'message' => 'OTP has been sent to your email address.',
            'expires_in' => self::OTP_VALIDITY_MINUTES * 60,
            'resend_after' => self::RESEND_COOLDOWN_SECONDS,
			'otp'     => $otp,
        ]);
    }

}
