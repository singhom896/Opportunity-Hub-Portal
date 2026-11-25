<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ArtFromController extends Controller
{
    public function list(Request $request, $country_id = null)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');

        $countries = DB::table('countries')->where('status', 1)->orderBy('name')->pluck('name', 'id');

        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
            $selectedCountry = (int) $authCountryId;
        } else {
            $selectedCountry = $request->input('country_id', $country_id);
        }

        $artForms = DB::table('art_forms as af')
            ->leftJoin('countries as c', 'c.id', '=', 'af.country_id')
            ->leftJoin('art_forms as parent', 'parent.id', '=', 'af.parent_id')
            ->select('af.*', 'c.name as country_name', 'parent.name as parent_name')
            ->when($selectedCountry, fn ($q) => $q->where('af.country_id', $selectedCountry))
            ->orderBy('af.country_id')
            ->orderByRaw('CASE WHEN af.parent_id = 0 THEN 0 ELSE 1 END')
            ->orderBy('parent.name')
            ->orderBy('af.name')
            ->get();

        return view('backend.art-forms.list', compact('artForms', 'countries', 'selectedCountry', 'authRoleId', 'authCountryId'));
    }

    public function createEditData($id = null)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');
        $artForm = null;

        if ($id) {
            $artFormId = decrypt($id);
            $query = DB::table('art_forms')->where('id', $artFormId);
            if ($authRoleId === 8) {
                abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
                $query->where('country_id', $authCountryId);
            }
            $artForm = $query->first();
            abort_unless($artForm, 404);
        }

        $countries = DB::table('countries')->where('status', 1)->orderBy('name')->pluck('name', 'id');
        $selectedCountry = $authRoleId === 8 ? $authCountryId : old('country_id', $artForm->country_id ?? '');
        $parentCategories = DB::table('art_forms')
            ->where('parent_id', 0)
            ->when($selectedCountry, fn ($q) => $q->where('country_id', $selectedCountry))
            ->when($artForm, fn ($q) => $q->where('id', '!=', $artForm->id))
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('backend.art-forms.createEdit', compact('artForm', 'countries', 'parentCategories', 'authRoleId', 'authCountryId'));
    }

    public function parentCategories($countryId)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');

        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
            abort_unless((int) $countryId === (int) $authCountryId, 403);
        }

        return response()->json(
            DB::table('art_forms')
                ->where('country_id', $countryId)
                ->where('parent_id', 0)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    public function saveUpdateData(Request $request)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');
        $id = $request->input('id');

        $validated = $request->validate([
            'id' => ['nullable', 'integer', 'exists:art_forms,id'],
            'country_id' => [$authRoleId === 8 ? 'nullable' : 'required', 'integer', Rule::exists('countries', 'id')->where('status', 1)],
            'parent_id' => ['required', 'integer', 'min:0'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:0,1'],
        ], [
            'country_id.required' => 'Please select a country.',
            'parent_id.required' => 'Please select category type.',
            'name.required' => 'Please enter art form name.',
        ]);

        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
            $countryId = (int) $authCountryId;
        } else {
            $countryId = (int) $validated['country_id'];
        }

        $parentId = (int) $validated['parent_id'];
        if ($parentId > 0) {
            $parent = DB::table('art_forms')->where('id', $parentId)->where('parent_id', 0)->where('country_id', $countryId)->first();
            abort_unless($parent, 422, 'Please select a valid main art form for the subcategory.');
            abort_if($id && (int) $id === $parentId, 422, 'An art form cannot be its own parent.');
        }

        $data = [
            'parent_id' => $parentId,
            'country_id' => $countryId,
            'name' => trim($validated['name']),
            'description' => isset($validated['description']) ? trim($validated['description']) : null,
            'status' => (int) $validated['status'],
        ];

        if ($id) {
            $query = DB::table('art_forms')->where('id', $id);
            if ($authRoleId === 8) $query->where('country_id', $authCountryId);
            abort_unless($query->exists(), 404);
            // A main category with existing children cannot be converted into a subcategory.
            if ($parentId > 0) {
                abort_if(DB::table('art_forms')->where('parent_id', $id)->exists(), 422, 'This main art form has subcategories and cannot be moved under another category.');
            }
            $data['updated_at'] = now();
            $query->update($data);
            $message = 'Art form updated successfully.';
        } else {
            $data['created_at'] = now();
            if (Schema::hasColumn('art_forms', 'updated_at')) $data['updated_at'] = now();
            DB::table('art_forms')->insert($data);
            $message = 'Art form created successfully.';
        }

        return redirect()->route('cp.art-forms.list')->with('success', $message);
    }

    public function statusChange(Request $request)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:art_forms,id'],
            'status' => ['required', 'in:0,1'],
        ]);

        $query = DB::table('art_forms')->where('id', $validated['id']);
        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
            $query->where('country_id', $authCountryId);
        }
        abort_unless($query->exists(), 404);

        $query->update(['status' => (int) $validated['status'], 'updated_at' => now()]);
        return response()->json([
            'success' => true,
            'status' => (int) $validated['status'],
            'message' => $validated['status'] ? 'Art form activated successfully.' : 'Art form deactivated successfully.',
        ]);
    }
}
