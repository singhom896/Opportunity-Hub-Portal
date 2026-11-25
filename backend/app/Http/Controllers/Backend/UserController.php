<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\State;
use Illuminate\Http\Request;
use App\Models\District;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class UserController extends Controller
{

    public function editProfile()
    {
        $user = Auth::user();

        // Country is displayed but cannot be changed from profile settings.
        $countries = DB::table('countries')
            ->orderBy('name')
            ->pluck('name', 'id');

        // Only regions belonging to the user's existing country are shown.
        $selectedCountry = $user->country_id;
        $regions = DB::table('art_regions')
            ->where('status', 1)
            ->when($selectedCountry, function ($query) use ($selectedCountry) {
                $query->where('country_id', $selectedCountry);
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('backend.users.profile', compact(
            'user',
            'countries',
            'regions'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $validatedData = $request->validate([
            'firstname' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\\s]+$/',
            ],
            'lastname' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\\s]+$/',
            ],
            'gender' => [
                'required',
                Rule::in(array_keys(genderOptions())),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'mobile' => ['required', 'digits:10'],
            'designation' => ['nullable', 'string', 'max:255'],
            'region_id' => [
                'nullable',
                'integer',
                Rule::exists('art_regions', 'id')
                    ->where('country_id', $user->country_id)
                    ->where('status', 1),
            ],
            'city' => ['required', 'string', 'max:255'],
            'pincode' => ['nullable', 'string', 'max:55'],
            'address' => ['nullable', 'string', 'max:255'],
        ], [
            'firstname.required' => 'Enter First Name',
            'firstname.regex' => 'First Name must contain letters and spaces only',
            'lastname.required' => 'Enter Last Name',
            'lastname.regex' => 'Last Name must contain letters and spaces only',
            'gender.required' => 'Please select Gender',
            'email.required' => 'Enter Email',
            'email.email' => 'Enter a valid Email',
            'email.unique' => 'This Email is already registered',
            'mobile.required' => 'Enter Mobile Number',
            'mobile.digits' => 'Mobile number must be exactly 10 digits',
            'region_id.exists' => 'Please select a valid region for your country',
            'city.required' => 'Please enter City / Town',
        ]);

        $firstname = trim($validatedData['firstname']);
        $lastname = trim($validatedData['lastname']);

        // Update only editable profile fields. Country, role and regno remain unchanged.
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->full_name = trim($firstname . ' ' . $lastname);
        $user->gender = $validatedData['gender'];
        $user->email = trim($validatedData['email']);
        $user->mobile = $validatedData['mobile'];
        $user->designation = $validatedData['designation'] ?? null;
        $user->region_id = $validatedData['region_id'] ?? null;
        $user->city = trim($validatedData['city']);
        $user->pincode = $validatedData['pincode'] ?? null;
        $user->address = $validatedData['address'] ?? null;
        $user->updated_by = Auth::id();
        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Your profile has been updated successfully.');
    }

    public function editPassword()
    {
        return view('backend.users.changePassword');
    }

    public function updatePassword(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:12', 'max:72', 'confirmed', 'different:current_password'],
        ], [
            'password.different' => 'Your new password must be different from your current password.',
        ]);

        $user = User::findOrFail(Auth::id());

        if (!$user->verifyPassword($validatedData['current_password'])) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->password = Hash::make($validatedData['password']);
        $user->save();
        $request->session()->regenerate();

        return redirect()->route('password.edit')->with('success', 'Your password has been changed successfully.');
    }

public function users($role_id = null)
{
    $roles = Role::where('active', 1)
        ->whereNotIn('role_id', [1, 5, 6, 7])
        ->get();

    $selectedRole = $role_id ? Crypt::decrypt($role_id) : null;

    $users = User::query()
        ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
        ->whereNotIn('users.role_id', [1, 5, 6, 7])
        ->when($selectedRole, function ($query) use ($selectedRole) {
            $query->where('users.role_id', $selectedRole);
        })
        ->select('users.*', 'countries.name as country_name')
        ->with('role')
        ->orderByDesc('users.id')
        ->get();

    return view('backend.users.list', compact(
        'users',
        'roles',
        'selectedRole'
    ));
}

    public function userstatus(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'id' => 'required|exists:users,id',
            'status' => 'required|boolean'
        ]);
        $user = User::findOrFail($request->id);
        $user->active = $request->status;
        $user->save();
        if ($user->active) {
            return response()->json([
                'success' => true,
                'status' => $user->active,
                'message' => 'User activated successfully.'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'status' => $user->active,
                'message' => 'User deactivated successfully.'
            ]);
        }
    }


    public function createEditUser($id = null)
    {
        $roles = Role::whereNotIn('role_id', [1, 5, 6, 7])
            ->where('active', 1)
            ->pluck('name', 'role_id');

        $user = $id ? User::findOrFail(decrypt($id)) : null;

        $countries = DB::table('countries')
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        $selectedCountry = old('country_id', $user->country_id ?? '');

        $regions = DB::table('art_regions')
            ->where('status', 1)
            ->when($selectedCountry, function ($query) use ($selectedCountry) {
                $query->where('country_id', $selectedCountry);
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('backend.users.createEdit', compact(
            'roles',
            'countries',
            'regions',
            'user'
        ));
    }

    /** Return active regions/provinces for the selected country. */
    public function regionsByCountry($country)
    {
        $regions = DB::table('art_regions')
            ->where('country_id', $country)
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return response()->json($regions);
    }


public function saveUpdateUser(Request $request)
{
    $validatedData = $request->validate([
        'id' => ['nullable', 'integer', 'exists:users,id'],

        'firstname' => [
            'required',
            'string',
            'max:100',
            'regex:/^[a-zA-Z\s]+$/',
        ],
        'lastname' => [
            'required',
            'string',
            'max:100',
            'regex:/^[a-zA-Z\s]+$/',
        ],

        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($request->input('id')),
        ],

        'mobile' => ['required', 'digits:10'],

        'gender' => [
            'required',
            'in:' . implode(',', array_keys(genderOptions())),
        ],

        'role' => ['required', 'exists:roles,role_id'],

        'country_id' => [
            'required',
            'integer',
            'exists:countries,id',
        ],

        'region_id' => [
            'nullable',
            'integer',
            Rule::exists('art_regions', 'id')
                ->where('country_id', $request->input('country_id'))
                ->where('status', 1),
        ],

        'city' => ['required', 'string', 'max:255'],
        'designation' => ['nullable', 'string', 'max:255'],
        'address' => ['nullable', 'string', 'max:255'],
        'pincode' => ['nullable', 'string', 'max:55'],

        'status' => [
            'required',
            'in:' . implode(',', array_keys(statusoptions())),
        ],
    ], [
        'firstname.required' => 'Enter First Name',
        'firstname.regex' => 'First Name must contain letters and spaces only',

        'lastname.required' => 'Enter Last Name',
        'lastname.regex' => 'Last Name must contain letters and spaces only',

        'email.required' => 'Enter Email',
        'email.email' => 'Enter a valid Email',
        'email.unique' => 'This Email is already registered',

        'mobile.required' => 'Enter Mobile Number',
        'mobile.digits' => 'Mobile number must be exactly 10 digits',

        'gender.required' => 'Please select Gender',

        'role.required' => 'Please choose Role',
        'role.exists' => 'Invalid Role selected',

        'country_id.required' => 'Please select Country',
        'country_id.exists' => 'Please select a valid country',

        'region_id.exists' => 'Please select a valid region for the selected country',

        'city.required' => 'Please enter City / Town',

        'status.required' => 'Please choose Status',
        'status.in' => 'Invalid Status selected',
    ]);

    // Validate selected active country.
    $country = DB::table('countries')
        ->where('id', $validatedData['country_id'])
        ->where('status', 1)
        ->first();

    if (!$country) {
        return back()
            ->withInput()
            ->withErrors([
                'country_id' => 'Please select a valid active country.',
            ]);
    }

    // Alpha-3 code is required for new registration numbers.
    if (empty($country->alpha_3)) {
        return back()
            ->withInput()
            ->withErrors([
                'country_id' => 'The selected country is missing its Alpha-3 code.',
            ]);
    }

    $firstname = trim($validatedData['firstname']);
    $lastname  = trim($validatedData['lastname']);

    $data = [
        'role_id'     => $validatedData['role'],
        'country_id'  => $validatedData['country_id'],
        'region_id'   => $validatedData['region_id'] ?? null,

        'firstname'   => $firstname,
        'lastname'    => $lastname,
        'full_name'   => trim($firstname . ' ' . $lastname),

        'email'       => trim($validatedData['email']),
        'mobile'      => $validatedData['mobile'],
        'gender'      => $validatedData['gender'],
        'city'        => trim($validatedData['city']),
        'designation' => $validatedData['designation'] ?? null,
        'address'     => $validatedData['address'] ?? null,
        'pincode'     => $validatedData['pincode'] ?? null,
        'active'      => $validatedData['status'],
    ];

    $message = '';

    DB::transaction(function () use (
        $validatedData,
        $data,
        $country,
        &$message
    ) {
        // Update existing user.
        if (!empty($validatedData['id'])) {
            $user = User::findOrFail($validatedData['id']);

            foreach ($data as $key => $value) {
                $user->{$key} = $value;
            }

            $user->updated_by = Auth::id();
            $user->save();

            // Do not change the existing registration number.
            $message = 'User updated successfully!';

            return;
        }

        /*
         * Find the highest trailing numeric sequence across ALL
         * registration number formats, including N, I, G and legacy IDs.
         *
         * Examples:
         * REG1791632016-000007 -> 7
         * IND-N-00001          -> 1
         * IND-I-00016          -> 16
         * IND-G-00014          -> 14
         */

        $lastSequence = DB::table('users')
            ->whereNotNull('regno')
            ->lockForUpdate()
            ->pluck('regno')
            ->map(function ($regno) {
                return preg_match(
                    '/-(\d+)$/',
                    trim((string) $regno),
                    $matches
                ) ? (int) $matches[1] : 0;
            })
            ->max() ?? 0;

        $nextSequence = $lastSequence + 1;

        $regno = strtoupper(trim($country->alpha_3))
            . '-N-'
            . str_pad(
                (string) $nextSequence,
                5,
                '0',
                STR_PAD_LEFT
            );

        // Create the user with registration number assigned first.
        $user = new User();

        foreach ($data as $key => $value) {
            $user->{$key} = $value;
        }

        $user->regno = $regno;
        $user->password = Hash::make('Brics@26$P!');
        $user->created_by = Auth::id();
        $user->save();

        $message = 'User created successfully!';
    });

    return redirect()
        ->route('users.list')
        ->with('success', $message);
}

}
