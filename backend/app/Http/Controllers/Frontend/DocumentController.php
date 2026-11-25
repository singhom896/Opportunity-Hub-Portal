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
use Yajra\DataTables\Facades\DataTables;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Route;


class DocumentController extends Controller
{
	
	
	
	
	   public function whatsnew()
    {
        return view('frontend.whatsnew');
    }


	public function yajraData(Request $request)
	{
		$menu = MenuItem::where('title', 'Bulletin')
			->where('type', 3)
			->where('active', 1)
			->first();

		if (!$menu) {
			return response()->json([
				'draw' => (int) $request->input('draw', 0),
				'recordsTotal' => 0,
				'recordsFiltered' => 0,
				'data' => [],
			]);
		}

		$query = Document::with('asset')
			->where('parent_menu_id', $menu->id)
			->where('active', 1)
			->where('publish_start_date', '<=', now())
			->where('publish_end_date', '>=', now());

		return DataTables::of($query)
			->addIndexColumn()

			->addColumn('title', function ($doc) {
				return $doc->title ?? '—';
			})

			->addColumn('published_date', function ($doc) {
				return $doc->publish_start_date
					? $doc->publish_start_date->format('d-m-Y')
					: '—';
			})

			->addColumn('size', function ($doc) {
				if ((int) $doc->document_type === 1) {
					return optional($doc->asset)->size_mb
						? $doc->asset->size_mb . ' MB'
						: '—';
				}

				return 'External Link';
			})

			->addColumn('actions', function ($doc) {

				if ((int) $doc->document_type === 1) {

					// PDF document
					$url = optional($doc->asset)->url;

					if (!$url) {
						return 'Not Available';
					}

					$link = asset($url);

				} elseif ((int) $doc->document_type === 2) {

					// External URL
					$link = $doc->document_url;

					if (!$link || !filter_var($link, FILTER_VALIDATE_URL)) {
						return 'Not Available';
					}

				} else {
					return 'Not Available';
				}

				return '<a href="' . e($link) . '"
							target="_blank"
							rel="noopener noreferrer"
							class="view-btn">
							<i class="bi bi-eye me-1"></i> View
						</a>';
			})

			->filter(function ($query) use ($request) {

				if ($search = trim($request->input('search', ''))) {
					$query->where('title', 'like', "%{$search}%");
				}

				$sortby = strtoupper($request->input('sortby', ''));

				if (in_array($sortby, ['ASC', 'DESC'], true)) {
					$query->orderBy('id', $sortby);
				} else {
					$query->orderBy('sortorder', 'ASC')
						  ->orderBy('id', 'DESC');
				}
			})

			->rawColumns(['actions'])
			->toJson();
	}


	public function yajraData_old(Request $request)
    {
        $menu=MenuItem::where(['url'=>$request->route])->first();
        $query = Document::with('asset')->where(['parent_menu_id' => $menu->id, 'active' => 1]);
        if (!$menu) return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('title', fn($doc) => $doc->title)
            ->addColumn('published_date', fn($doc) => $doc->created_at->format('d-m-Y'))
            ->addColumn('size', fn($doc) => optional($doc->asset)->size_mb . ' MB')
            ->addColumn('actions', fn($doc) => '<a href="' . asset($doc->asset?->url) . '" target="_blank" class="view-btn"><i class="bi bi-eye me-1" aria-hidden="true"></i> View</a>')
            ->filter(function ($query) use ($request) {
                if ($search = $request->input('search')) {
                    $query->where('title', 'like', "%{$search}%");
                }
                if ($sortby = $request->input('sortby')) {
                    $query->orderBy('id', $sortby);
                }
            })
            ->rawColumns(['actions'])
            ->toJson();
    }


}
