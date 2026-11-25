<?php

use App\Models\MenuItem;
use App\Models\State;
use App\Models\District;
use App\Models\Module;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

if (! function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
if (! function_exists('getMenu')) {
    function getMenu()
    {
        return MenuItem::whereNull('parent_id')->where(['active' => 1])->orderBy('order_index')->with('childrenRecursive')->get();
    }
}
if (!function_exists('categoryOptions')) {
    function categoryOptions()
    {
        return [
            'SC' => 'Scheduled Caste',
            'DN' => 'Denotified Nomadic/Semi-Nomadic Tribes',
            'TA' => 'Traditional Artisans',
            'LA' => 'Landless Agriculture Labourer'
        ];
    }
}
if (!function_exists('getAllState')) {
    function getAllState()
    {
        $state = State::orderBy('StateName', 'ASC')->pluck('StateName', 'StateCode');;
        return $state;
    }
}
if (!function_exists('getDistrictsByStateId')) {
    function getDistrictsByStateId($StateCode)
    {
        $districts = District::where(['StateCode' => $StateCode])->orderBy('DistrictName', 'ASC')->pluck('DistrictName', 'DistrictCode');;
        return $districts;
    }
}

if (!function_exists('genderOptions')) {
    function genderOptions()
    {
        return [
            '1' => 'Male',
            '2' => 'Female',
            '3' => 'Others'
        ];
    }
}
if (!function_exists('statusoptions')) {
    function statusoptions()
    {
        return [
            '0' => 'Save and Unpublish',
            '1' => 'Save and Publish',
        ];
    }
}
if (!function_exists('pagetype')) {
    function pagetype()
    {
        return [
            '1' => 'Custom',
            '2' => 'Content',
            '3' => 'Document',
        ];
    }
}
if (!function_exists('targettype')) {
    function targettype()
    {
        return [
            '_self' => '_self',
            '_blank' => '_blank',
        ];
    }
}
if (! function_exists('generateRegistrationNumber')) {
    /**
     * Registration ID for a new account: OH-AI / OH-AG / OH-AO / OH-EP-NNNNN
     * (App\Support\RegistrationNumber) for artist and partner accounts; other
     * roles keep the older "REG..." format.
     */
    function generateRegistrationNumber(int $userId, ?int $roleId = null): string
    {
        $roleId ??= (int) \Illuminate\Support\Facades\DB::table('users')->where('id', $userId)->value('role_id');

        return \App\Support\RegistrationNumber::next($roleId)
            ?? 'REG' . now()->timestamp . '-' . str_pad((string) $userId, 6, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('lastUpdated')) {
    function lastUpdated()
    {
        $latestFile = true;
        return $latestFile ? [
            'name' => 'Test',
            'path' => '/test',
            'updated_at' => '3 August 2026',
        ] : null;

        $latestFile = collect(File::allFiles(base_path()))
            ->reject(
                fn($file) =>
                str_contains($file->getRealPath(), 'vendor') ||
                    str_contains($file->getRealPath(), 'storage') ||
                    str_contains($file->getRealPath(), 'bootstrap/cache')
            )
            ->sortByDesc(fn($file) => $file->getMTime())
            ->first();

        return $latestFile ? [
            'name' => $latestFile->getFilename(),
            'path' => $latestFile->getRealPath(),
            'updated_at' => Carbon::createFromTimestamp($latestFile->getMTime())
                ->format('d F Y'),
        ] : null;
    }
}
if (!function_exists('getSidebar')) {
    function getSidebar()
    {
        $allowedModuleIds =  Auth::user()->role->modules()->wherePivot('can_view', 1)->pluck('modules.module_id')->toArray();
        $modules = Module::with(['children' => function ($query) use ($allowedModuleIds) {
            $query->whereIn('module_id', $allowedModuleIds);
        }])
            ->whereNull('parent_id')
            ->where('active', 1)
            ->whereIn('module_id', $allowedModuleIds)
            ->orderBy('position')
            ->get();
        //dd($modules);
        return $modules;
    }
}
if (!function_exists('marital_status')) {
    function marital_status()
    {
        return [
            '1' => 'Married',
            '2' => 'Unmarried',
        ];
    }
}
if (!function_exists('scoring_system')) {
    function scoring_system()
    {
        return [
            '1' => 'Grading',
            '2' => 'Percentage',
        ];
    }
}
if (!function_exists('getApplicationTab')) {
    function getApplicationTab(int $step)
    {
        $tabs = [
            1 => 'pills-personal-tab',
            2 => 'pills-foreign-tab',
            3 => 'pills-employment-tab',
            4 => 'pills-visa-tab',
            5 => 'pills-documents-tab',
            6 => 'pills-preview-tab',
        ];
        $nextStep = $step + 1;
        return $tabs[$nextStep] ?? $tabs[6];
    }
}

if (!function_exists('visa_types')) {
    function visa_types()
    {
        return [
            'Tourist'      => 'Tourist Visa',
            'Student'      => 'Student Visa',
            'Work'         => 'Work Visa',
            'Business'     => 'Business Visa',
            'Medical'      => 'Medical Visa',
            'Transit'      => 'Transit Visa',
            'Diplomatic'   => 'Diplomatic Visa',
            'Other'        => 'Other',
        ];
    }
}
