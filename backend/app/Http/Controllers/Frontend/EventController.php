<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EventController extends Controller
{
    /**
     * Public events listing: upcoming and past events.
     */
    public function events(Request $request)
    {
        $page = ['title' => 'Events'];

        $type = $request->input('type', 'upcoming');
        $type = in_array($type, ['upcoming', 'past'], true) ? $type : 'upcoming';

        $baseQuery = Event::query()
            ->with(['country:id,name', 'region:id,name'])
            ->where('events.is_active', 1)
            ->where('events.is_deleted', 0);

        $applyFilters = function ($query) use ($request) {
            $query->when($request->filled('eventTitle'), function ($q) use ($request) {
                $q->where('events.title', 'like', '%' . trim($request->eventTitle) . '%');
            });

            $query->when($request->filled('country_id'), function ($q) use ($request) {
                $q->where('events.country_id', $request->country_id);
            });

            $query->when($request->filled('region_id'), function ($q) use ($request) {
                $q->where('events.region_id', $request->region_id);
            });

            $query->when($request->filled('city'), function ($q) use ($request) {
                $q->where('events.city', 'like', '%' . trim($request->city) . '%');
            });

            $query->when($request->filled('eventDate'), function ($q) use ($request) {
                $q->whereDate('events.event_date', $request->eventDate);
            });
        };

        $eventUpcomingData = (clone $baseQuery)
            ->where('events.event_date', '>=', now())
            ->when($type === 'upcoming', $applyFilters)
            ->orderBy('events.event_date')
            ->get();

        $eventPastData = (clone $baseQuery)
            ->where('events.event_date', '<', now())
            ->when($type === 'past', $applyFilters)
            ->orderByDesc('events.event_date')
            ->get();

        // Active country list for the new portal's country filter.
        $countries = \DB::table('countries')
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        // Region list is optional; it is populated from the existing art_regions master.
        $regions = \DB::table('art_regions')
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('frontend.events', compact(
            'page',
            'eventUpcomingData',
            'eventPastData',
            'countries',
            'regions',
            'type'
        ));
    }

    /**
     * Public event detail with media from event_media and assets.
     */
    public function eventDetail(string $id)
    {
        try {
            $eventId = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $eventdata = Event::with([
                'country:id,name',
                'region:id,name',
                'activeMedia.asset:id,url,size',
            ])
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->findOrFail($eventId);

        $photoData = $eventdata->activeMedia->where('media_type', 'photo')->values();
        $videoData = $eventdata->activeMedia
            ->whereIn('media_type', ['video', 'youtube'])
            ->values();

        return view('frontend.event-detail', compact('eventdata', 'photoData', 'videoData'));
    }
}
