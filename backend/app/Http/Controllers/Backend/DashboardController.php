<?php


namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $agency = auth()->user();
        $roleId = (int) $agency->role_id;

        // Role permissions for dashboard display
        $isNodalAgency = $roleId === 8;
        $isAdminRole = in_array($roleId, [1, 2, 3, 4], true);
        $isArtistRole = in_array($roleId, [5, 6, 7], true);

        // Nodal Agency must have an assigned country
        abort_if(
            $isNodalAgency && !$agency->country_id,
            403,
            'Your Nodal Agency account has no assigned country.'
        );

        $country = $isNodalAgency
            ? Country::query()->findOrFail($agency->country_id)
            : null;

        /*
         * Artist accounts come only from users table.
         * Role 5 = Individual Artist
         * Role 6 = Artist Group
         * Role 7 = Artist Institution
         */
        $artists = User::query()
            ->whereIn('role_id', [5, 6, 7])
            ->when($isNodalAgency, function ($query) use ($country) {
                $query->where('country_id', $country->id);
            });

        // Total artist registrations/accounts
        $artistRegistrationCount = (clone $artists)->count();

        // Retained for compatibility with the existing Blade
        $registeredUserCount = $artistRegistrationCount;

        // verified_status: 0 = Pending
        $pendingRegistrationCount = (clone $artists)
            ->where('verified_status', 0)
            ->count();

        // verified_status: 1 = Verified
        // Count only active verified accounts as approved artists
        $approvedArtistCount = (clone $artists)
            ->where('verified_status', 1)
           // ->where('active', 1)
            ->count();

        // verified_status: 2 = Rejected
        $rejectedApplicationCount = (clone $artists)
            ->where('verified_status', 2)
            ->count();

        // Logged-in agency name
        $agencyName = $agency->full_name
            ?: ($agency->firstname ?? null)
            ?: 'Nodal Agency';

        // Existing sidebar modules logic
        $modules = getSidebar()
            ->flatMap(static function ($module) {
                return $module->children->isEmpty()
                    ? collect([$module])
                    : $module->children;
            })
            ->filter(static function ($module) {
                return filled($module->page_url);
            })
            ->values();

        return view('backend.dashboard.dashboard', compact(
            'artistRegistrationCount',
            'registeredUserCount',
            'pendingRegistrationCount',
            'approvedArtistCount',
            'rejectedApplicationCount',
            'agencyName',
            'country',
            'isNodalAgency',
            'isAdminRole',
            'isArtistRole',
            'modules'
        ));
    }
}
