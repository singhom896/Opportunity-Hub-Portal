<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\ArtistRegistration;
use App\Models\Banner;
use App\Models\Content;
use App\Models\Document;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\Video;
use App\Models\LoginHistory;
use App\Models\MenuItem;
use App\Models\Minister;
use App\Models\User;
use App\Notifications\WelcomeUser;
use Jenssegers\Agent\Agent;
use Yajra\DataTables\Facades\DataTables;

class HomeController extends Controller
{
    public function index()
    {
        $activeBanners = Banner::with('asset')->where('active', 1)->orderBy('priority_ordering', 'asc')->get();
        $activeMinisters = Minister::with('asset')->where('active', 1)->orderBy('priority_ordering', 'asc')->get();
        $memberCountries = DB::table('countries')->where('type', 1)->orderBy('name')->get();
        $partnerCountries = DB::table('countries')->where('type', 2)->orderBy('name')->get();
		
        //dd($activeMinisters);
        $upcomingEvents = Event::with(['media' => function ($query) {
            $query->where('is_active', 1)->where('media_type', 'photo')->with('asset');
        }])
            ->leftJoin('countries', 'events.country_id', '=', 'countries.id')
            ->where('events.is_active', 1)
            ->where('events.is_deleted', 0)
            ->where('events.event_date', '>=', now())
            ->select('events.*', 'countries.name as country_name')
            ->orderBy('events.event_date', 'asc')
            ->limit(5)
            ->get();
		
		$latestDocuments = Document::with('asset')
			->where('active', 1)
			->where('publish_start_date', '<=', now())
			->where('publish_end_date', '>=', now())
			->orderBy('sortorder', 'ASC')
			->orderBy('id', 'DESC')
			->limit(4)
			->get();


		$latestArtists = DB::table('users')
			->leftJoin('countries as c', 'c.id', '=', 'users.country_id')
			->whereIn('users.role_id', [5, 6, 7])
			->where('users.verified_status', 1)
			->where(function ($query) {
				$query->where(function ($q) {
					$q->where('users.role_id', 5)
						->whereExists(function ($sub) {
							$sub->selectRaw('1')
								->from('artist_registrations as ar')
								->whereColumn('ar.user_id', 'users.id');
						});
				})->orWhere(function ($q) {
					$q->where('users.role_id', 6)
						->whereExists(function ($sub) {
							$sub->selectRaw('1')
								->from('artist_group_details as ag')
								->whereColumn('ag.user_id', 'users.id');
						});
				})->orWhere(function ($q) {
					$q->where('users.role_id', 7)
						->whereExists(function ($sub) {
							$sub->selectRaw('1')
								->from('artist_institution_details as ai')
								->whereColumn('ai.user_id', 'users.id');
						});
				});
			})
			->select(
				'users.id',
				'users.full_name',
				'users.role_id',
				'users.country_id',
				'c.name as country_name'
			)
			->orderByDesc('users.id')
			->limit(4)
			->get();

		$latestArtists = $latestArtists->map(function ($artist) {

			$details = null;
			$photo = null;

			if ((int) $artist->role_id === 5) {

				$details = DB::table('artist_registrations')
					->where('user_id', $artist->id)
					->first();

				$artist->display_name = $artist->full_name;
				$photo = $details->profile_photo ?? null;

			} elseif ((int) $artist->role_id === 6) {

				$details = DB::table('artist_group_details')
					->where('user_id', $artist->id)
					->first();

				$artist->display_name = $details->group_name
					?? $artist->full_name;

				$photo = $details->profile_photo ?? null;

			} elseif ((int) $artist->role_id === 7) {

				$details = DB::table('artist_institution_details')
					->where('user_id', $artist->id)
					->first();

				$artist->display_name = $details->institution_name
					?? $artist->full_name;

				$photo = $details->institution_logo ?? null;
			}

			$artist->artist_type = match ((int) $artist->role_id) {
				5 => 'Artist',
				6 => 'Artist Group',
				7 => 'Institute / Organization',
			};

			// Profile photo
			$artist->photo_url = $photo ? asset('uploads/' . ltrim($photo, '/')) : '';

			// Art Forms
			$formIds = array_filter(array_map(
				'intval',
				explode(',', (string) ($details->art_form_id ?? ''))
			));

			$formNames = $formIds
				? DB::table('art_forms')
					->whereIn('id', $formIds)
					->pluck('name')
					->implode(', ')
				: '';

			$artist->art_form = $formNames
				?: ($details->other_art_form ?? 'Not provided');

			return $artist;
		});
		
	

        return view('frontend.index', compact('activeBanners', 'activeMinisters', 'memberCountries', 'partnerCountries', 'upcomingEvents','latestArtists','latestDocuments'));
    }

    public function getDistricts($stateId)
    {
        return response()->json(getDistrictsByStateId($stateId));
    }

    public function sitemap()
    {
        return view('frontend.sitemap');
    }

    public function content($slug = null)
    {
        $content = Content::where(['slug' => $slug])->first();
        if (!empty($content)) {
            return view('frontend.content', compact('content'));
        } else {
            abort(404);
        }
    }

 
    public function document()
    {
		$documents = Document::with('asset')
			->where('active', 1)
			->where('publish_start_date', '<=', now())
			->where('publish_end_date', '>=', now())
			->orderBy('sortorder', 'ASC')
			->orderBy('id', 'DESC')
			//->limit(3)
			->get();
			
        return view('frontend.document', compact('documents'));
    }

    public function aboutbrics()
    {
        return view('frontend.about-brics');
    }	
		
	 public function contactus(){

        $menu=Content::where(['slug'=>Route::currentRouteName()])->first();
        return view('frontend.contact',compact('menu'));
    }

 public function artforms(Request $request)
{
    // Active BRICS Countries
    $countries = DB::table('countries')
        ->where('status', 1)
        ->where('brics_flag', 1)
        ->orderBy('name')
        ->get();

    $selectedCountryId = $request->input('country_id');

    // Art Forms
    $artForms = DB::table('art_forms')
        ->where('status', 1)
        ->where('parent_id', 0)
        ->when($selectedCountryId, function ($query) use ($selectedCountryId) {
            $query->where('country_id', $selectedCountryId);
        })
        ->orderBy('name')
        ->get()
        ->map(function ($form) {
            $form->examples = DB::table('art_forms')
                ->where('status', 1)
                ->where('parent_id', $form->id)
                ->orderBy('name')
                ->pluck('name');

            return $form;
        });

    return view('frontend.art-forms', compact(
        'artForms',
        'countries',
        'selectedCountryId'
    ));
}

	public function artformsddd()
	{
		$artForms = \DB::table('art_forms')
			->where('status', 1)
			->where('parent_id', 0)
			->orderBy('name')
			->get()
			->map(function ($form) {
				$form->examples = \DB::table('art_forms')
					->where('status', 1)
					->where('parent_id', $form->id)
					->orderBy('name')
					->pluck('name');

				return $form;
			});

		return view('frontend.art-forms', compact('artForms'));
	}

    public function bulletin()
    {
		$documents = Document::with('asset')
			->where('active', 1)
			->where('publish_start_date', '<=', now())
			->where('publish_end_date', '>=', now())
			->orderBy('sortorder', 'ASC')
			->orderBy('id', 'DESC')
			//->limit(3)
			->get();
        return view('frontend.bulletin', compact('documents'));
    }
	
	public function gallery()
	{
		// Photo Albums
		$albums = Gallery::with('asset')
			->where('active', 1)
			->where('is_deleted', 0)
			->orderBy('sortorder', 'ASC')
			->orderBy('id', 'DESC')
			->get();

		// Videos
		$videos = Video::with('asset')
			->where('active', 1)
			->where('is_deleted', 0)
			->orderBy('sortorder', 'ASC')
			->orderBy('id', 'DESC')
			->get();

		// Event Photos
		$eventPhotos = Event::with([
			'activeMedia' => function ($query) {
				$query->with('asset')
					->where('media_type', 'photo');
			}
		])
			->where('is_active', 1)
			->where('is_deleted', 0)
			->whereHas('activeMedia', function ($query) {
				$query->where('media_type', 'photo');
			})
			->orderBy('event_date', 'DESC')
			->get();

		// Event Videos
		$eventVideos = Event::with([
			'activeMedia' => function ($query) {
				$query->with('asset')
					->whereIn('media_type', ['video', 'youtube']);
			}
		])
			->where('is_active', 1)
			->where('is_deleted', 0)
			->whereHas('activeMedia', function ($query) {
				$query->whereIn('media_type', ['video', 'youtube']);
			})
			->orderBy('event_date', 'DESC')
			->get();

		// Counts
		$eventPhotoCount = $eventPhotos->sum(function ($event) {
			return $event->activeMedia->count();
		});

		$eventVideoCount = $eventVideos->sum(function ($event) {
			return $event->activeMedia->count();
		});

		return view('frontend.gallery', compact(
			'albums',
			'videos',
			'eventPhotos',
			'eventVideos',
			'eventPhotoCount',
			'eventVideoCount'
		));
	}


    public function countries()
    {
        $countries = DB::table('countries')
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        return view('frontend.countries', compact('countries'));
    }
	
	

    public function clearit()
    {       
        $apps1 = Artisan::call('route:clear');
        $apps2 = Artisan::call('view:clear');
        $apps3 = Artisan::call('cache:clear');
        $apps4 = Artisan::call('config:cache');
        $apps5 = Artisan::call('storage:link');

	    return response()->make('
			<div style="text-align:center; margin-top:100px; font-family:Arial;">
				<h2>All Commands Executed Successfully!</h2>
				<p>Cache cleared successfully.</p>
				<a href="' . url('/') . '"
				   style="display:inline-block; padding:12px 25px;
				   background:#0d6efd; color:#fff; text-decoration:none;
				   border-radius:5px;">
					Back to Home
				</a>
			</div>
		');
		
        /* return response()->json([          
            'route_clear'  => $apps1,
            'view_clear'   => $apps2,
            'cache_clear'  => $apps3,
			'config_cache' => $apps4,
            'storage_link' => $apps5,
            'message'        => 'All commands executed successfully.',
			'home_url'       => url('/'),
        ]); */
    }
	


	public function clearitAll()
	{
		// Clear all application caches
		$optimizeClear = Artisan::call('optimize:clear');

		// Rebuild configuration cache
		$configCache = Artisan::call('config:cache');

		// Create storage symbolic link
		$storageLink = Artisan::call('storage:link');
		
		  return response()->make('
				<div style="text-align:center; margin-top:100px; font-family:Arial;">
					<h2>All Commands Executed Successfully!</h2>
					<p>Application optimization, configuration cache and storage link commands executed.</p>

					<a href="' . url('/') . '"
					   style="display:inline-block; padding:12px 25px;
					   background:#0d6efd; color:#fff; text-decoration:none;
					   border-radius:5px;">
						Back to Home
					</a>
				</div>
			');

		/* return response()->json([
			'optimize_clear' => $optimizeClear,
			'config_cache'   => $configCache,
			'storage_link'   => $storageLink,
			'message'        => 'All commands executed successfully.',
			'home_url'       => url('/'),
		]); */
	}
	

}
