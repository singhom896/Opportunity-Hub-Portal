<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Minister;
use App\Models\User;
use App\Models\Document;
use App\Models\MenuItem;
use App\Models\LoginHistory;
use App\Models\Content;
use App\Notifications\WelcomeUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password as PasswordRules;



class AuthController extends Controller
{
	  public function showLoginForm()
    {
					
		/* 	$user = User::find(1);

		$user->password = Hash::make('Brics@26$P!');
		$user->updated_by = 1;
		$user->save(); */

        return view('frontend.login');
    }

public function login(Request $request)
{
    // Step 1: Validate input and CAPTCHA
    $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required', 'string'],
        'captcha'  => ['required', 'captcha'],
    ], [
        'email.required'    => 'Please enter your email address.',
        'email.email'       => 'Please enter a valid email address.',
        'password.required' => 'Please enter your password.',
        'captcha.required'  => 'Please enter the CAPTCHA code.',
        'captcha.captcha'   => 'The CAPTCHA code is incorrect. Please try again.',
    ]);

    // Step 2: Find user by email
    $user = User::with('role')
        ->where('email', trim($request->email))
        ->first();

    // Step 3: Check email and password
    if (!$user || !$user->verifyPassword($request->password)) {
        return back()
            ->withErrors([
                'email' => 'Invalid email or password. Please try again.',
            ])
            ->onlyInput('email');
    }

    // Step 4: Check account active status
    if ((int) $user->active !== 1) {
        return back()
            ->withErrors([
                'email' => 'Your account is inactive. Please contact the administrator.',
            ])
            ->onlyInput('email');
    }

    // Step 5: Check verification status
    switch ((int) $user->verified_status) {

        case 0:
            return back()
                ->withErrors([
                    'email' => 'Your account is pending verification. Please wait for approval.',
                ])
                ->onlyInput('email');

        case 2:
            return back()
                ->withErrors([
                    'email' => 'Your account verification has been rejected. Please contact the administrator.',
                ])
                ->onlyInput('email');

        case 1:
            // Verified user - continue
            break;

        default:
            return back()
                ->withErrors([
                    'email' => 'Your account verification status is invalid. Please contact the administrator.',
                ])
                ->onlyInput('email');
    }

    // Step 6: Check assigned role status
    if (!$user->role || (int) $user->role->active !== 1) {
        return back()
            ->withErrors([
                'email' => 'Your assigned role is inactive. Please contact the administrator.',
            ])
            ->onlyInput('email');
    }

    // Step 7: Login
    Auth::login($user, $request->boolean('remember'));

    // Regenerate session
    $request->session()->regenerate();

    // Step 8: Update last login
    $user->forceFill([
        'last_login_at' => now(),
    ])->save();

    // Step 9: Save login history
    try {
        $agent = new Agent();

        LoginHistory::create([
            'user_id'      => $user->id,
            'regno'        => $user->regno,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'device'       => $agent->device(),
            'browser'      => $agent->browser(),
            'platform'     => $agent->platform(),
            'is_success'   => true,
            'logged_in_at' => now(),
        ]);

    } catch (\Throwable $e) {
        Log::error('Login history error: ' . $e->getMessage());
    }

    // Step 10: Redirect
    return redirect()
        ->intended(route('dashboard'))
        ->with('success', 'Logged in successfully!');
}

public function loginold1(Request $request)
{
	   $request->validate([
		'email'    => ['required', 'email'],
		'password' => ['required', 'string'],
		'captcha'  => ['required', 'captcha'],
	], [
		'email.required'    => 'Please enter your email address.',
		'email.email'       => 'Please enter a valid email address.',
		'password.required' => 'Please enter your password.',
		'captcha.required'  => 'Please enter the CAPTCHA code.',
		'captcha.captcha'   => 'The CAPTCHA code is incorrect. Please try again.',
	]);
	
    $user = User::where('email', trim($request->email))
        ->where('active', 1)
        ->whereHas('role', function ($query) {
            $query->where('active', 1);
        })
        ->first();

    if (!$user || !$user->verifyPassword($request->password)) {
        return back()
            ->withErrors([
                'email' => 'Invalid email or password, or your account is inactive.',
            ])
            ->onlyInput('email');
    }

    Auth::login($user, $request->boolean('remember'));

    $request->session()->regenerate();

    $user->forceFill([
        'last_login_at' => now(),
    ])->save();

    $agent = new \Jenssegers\Agent\Agent();

    try {
        LoginHistory::create([
            'user_id'      => $user->id,
            'regno'        => $user->regno,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'device'       => $agent->device(),
            'browser'      => $agent->browser(),
            'platform'     => $agent->platform(),
            'is_success'   => true,
            'logged_in_at' => now(),
        ]);
    } catch (\Throwable $e) {
        \Log::error('Login history error: ' . $e->getMessage());
    }

    return redirect()
        ->intended(route('dashboard'))
        ->with('success', 'Logged in successfully!');
}


public function loginold(Request $request)
{
	


    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'captcha' => 'required|captcha',
    ]);

    $user = User::where([
        'email' => $request->email,
        'active' => 1
    ])
    ->whereHas('role', function ($query) {
        $query->where('active', 1);
    })
    ->first();

    $agent = new Agent();

    if ($user && $user->verifyPassword($request->password)) {

        Auth::login($user);

        LoginHistory::create([
            'user_id'      => Auth::id(),
            'regno'        => Auth::user()->regno ?? null,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'device'       => $agent->device(),
            'browser'      => $agent->browser(),
            'platform'     => $agent->platform(),
            'is_success'   => true,
            'logged_in_at' => now(),
        ]);

        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', 'Logged in successfully!');
    }

    return back()
        ->withErrors([
            'email' => 'Invalid Credentials',
        ])
        ->onlyInput('email');
}

	  public function logout(Request $request)
    {
        LoginHistory::where('user_id', Auth::id())
        ->whereNull('logged_out_at')
        ->latest()
        ->update([
            'logged_out_at' => now()
        ]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }

	public function showRegistrationForm()
    {
        return view('frontend.register');
    }

	public function register(Request $request)
    {
        //dd($request->all());
        $validatedData = $request->validate([
            'firstname'  => 'required|alpha|max:50',
            'middlename' => 'nullable|alpha|max:50',
            'lastname'   => 'required|alpha|max:50',
            'father_name' => ['required', 'regex:/^[A-Za-z ]+$/', 'max:50'],
            'gender'   => 'required|in:' . implode(',', array_keys(genderOptions())),
            'dob' => 'required|date|before:' . now()->subYears(18)->format('Y-m-d'),
            'mobile'     => 'required|numeric|digits:10',
            'email'      => 'required|email|unique:users,email',
            'category'   => 'required|in:' . implode(',', array_keys(categoryOptions())),
            'state'      => 'required|exists:states,StateCode',
            'district'   => 'required|exists:districts,DistrictCode',
            'password'   => 'required|string',
            //'captcha'    => 'required',
        ], [
            'firstname.alpha' => 'Firstname can only contain letters.',
            'middlename.alpha' => 'Middlename can only contain letters.',
            'lastname.alpha' => 'Lastname can only contain letters.',
            'father_name.regex' => 'Father name can only contain letters and spaces.',
            'dob.before' => 'You must be at least 18 years old.',
            'mobile.digits' => 'Mobile number must be exactly 10 digits.',
        ]);
        $validatedData['role_id'] = 2;
        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);
        User::where('id', $user->id)->update(['regno' => generateRegistrationNumber($user->id)]);
        $user->notify(new WelcomeUser());
        return redirect()->route('login')->with('success', 'Registration successful!Please Activate Your Link');
    }

    public function activate($token)
    {
        $user = User::where('email', $token)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid activation link.');
        }

        if (in_array((int) $user->role_id, [5, 6, 7], true)) {
            return redirect()->route('login')->withErrors([
                'activation' => 'Artist accounts must be activated using the secure password setup link sent to the registered email address.',
            ]);
        }

        $user->update(['active' => 1,]);
        return redirect()->route('login')->with('success', 'Account activated successfully! You can now login.');
    }
	

	public function showForgotPasswordForm()
	{
		return view('frontend.forgot-password');
	}

	public function sendResetLinkEmail(Request $request)
	{
		$validated = $request->validate([
			'email' => ['required', 'email', 'max:255'],
		], [
			'email.required' => 'Please enter your registered email address.',
			'email.email' => 'Please enter a valid email address.',
			'email.max' => 'Email address cannot exceed 255 characters.',
		]);

		$status = Password::sendResetLink([
			'email' => trim($validated['email']),
		]);

		if ($status === Password::RESET_LINK_SENT) {
			return back()->with('status', __($status));
		}

		return back()
			->withErrors(['email' => __($status)])
			->withInput($request->only('email'));
	}

	public function showResetForm(Request $request, string $token)
	{
		$email = $request->query('email');

		return view('frontend.reset-password', [
			'token' => $token,
			'email' => $email,
		]);
	}

	public function resetPassword(Request $request)
	{
		$validated = $request->validate([
			'token' => ['required', 'string'],
			'email' => ['required', 'email', 'max:255'],
			'password' => [
				'required',
				'string',
				'confirmed',
				PasswordRules::min(8)->letters()->mixedCase()->numbers()->symbols(),
			],
		], [
			'token.required' => 'The password reset token is missing.',
			'email.required' => 'The registered email address is required.',
			'email.email' => 'Please enter a valid email address.',
			'password.required' => 'Please enter your new password.',
			'password.confirmed' => 'New password and confirmation do not match.',
		]);
		

		$status = Password::reset(
			[
				'email' => trim($validated['email']),
				'password' => $validated['password'],
				'password_confirmation' => $request->input('password_confirmation'),
				'token' => $validated['token'],
			],
			function ($user, $password) {
				$user->forceFill([
					'password' => Hash::make($password),
					'remember_token' => Str::random(60),
				])->save();

				event(new PasswordReset($user));
			}
		);

		if ($status === Password::PASSWORD_RESET) {
			return redirect()
				->route('login')
				->with('success', 'Your password has been reset successfully. Please login with your new password.');
		}

		return back()
			->withErrors(['email' => __($status)])
			->withInput($request->only('email'));
	}		

}
