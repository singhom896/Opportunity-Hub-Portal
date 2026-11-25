<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Event;
use App\Models\EventMedia;
use App\Traits\UploadAssetTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    use UploadAssetTrait;

    public function list()
    {
        $events = Event::with('creator')
            ->where('is_deleted', 0)
            ->orderByDesc('id')
            ->get();

        return view('backend.event.list', compact('events'));
    }

    public function createEdit($id = null)
    {
        $event = null;

        if ($id !== null) {
            try {
                $eventId = $this->decryptRouteId($id);
            } catch (\Throwable $e) {
                abort(404);
            }

            $event = Event::where('is_deleted', 0)->findOrFail($eventId);

            // Editing is not allowed after the event has ended.
            if ($this->eventHasEnded($event)) {
                return redirect()->route('event.list')
                    ->with('error', 'This event has ended and can no longer be edited.');
            }
        }

        $authRoleId = (int) authUserData('role_id');
        $loggedInUser = Auth::user();

        $countriesQuery = DB::table('countries')
            ->where('status', 1)
            ->orderBy('name');

        // Role 8 can only use their own country.
        if ($authRoleId === 8) {
            abort_unless($loggedInUser && $loggedInUser->country_id, 403);
            $countriesQuery->where('id', $loggedInUser->country_id);
        }

        $countries = $countriesQuery->pluck('name', 'id');

        $countryId = $authRoleId === 8
            ? $loggedInUser->country_id
            : old('country_id', $event->country_id ?? null);

        $regions = DB::table('art_regions')
            ->where('status', 1)
            ->when($countryId, function ($query) use ($countryId) {
                $query->where('country_id', $countryId);
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('backend.event.addEdit', compact(
            'event',
            'countries',
            'regions',
            'authRoleId',
            'countryId'
        ));
    }

    public function saveUpdate(Request $request)
    {
        $id = $request->input('id');
        $event = null;

        if ($id) {
            $event = Event::where('is_deleted', 0)->findOrFail($id);

            // Enforce the same edit restriction on direct POST requests.
            if ($this->eventHasEnded($event)) {
                return redirect()->route('event.list')
                    ->with('error', 'This event has ended and can no longer be edited.');
            }
        }

        $authRoleId = (int) authUserData('role_id');
        $loggedInUser = Auth::user();

        // Role 8 must retain their assigned country; never trust a submitted country_id.
        if ($authRoleId === 8) {
            abort_unless($loggedInUser && $loggedInUser->country_id, 403);
        }

        $countryId = $authRoleId === 8
            ? $loggedInUser->country_id
            : $request->input('country_id');

        $validated = $request->validate([
            'id' => ['nullable', 'integer', 'exists:events,id'],
            'country_id' => [$authRoleId === 8 ? 'nullable' : 'required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'venue' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date_format:Y-m-d'],
            'event_time' => ['required', 'date_format:H:i'],
            'event_end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:event_date'],
            'event_end_time' => ['required', 'date_format:H:i'],
            'organised_by' => ['required', 'string', 'max:150'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'other_mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'city' => ['required', 'string', 'max:255'],
            'pincode' => ['nullable', 'string', 'max:55'],
            'address' => ['nullable', 'string', 'max:255'],
            'event_description' => ['required', 'string'],
            'lang_id' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'in:0,1'],
        ], [
            'event_date.required' => 'Please select the event start date.',
            'event_end_date.required' => 'Please select the event end date.',
            'event_end_date.after_or_equal' => 'Event end date must be on or after the start date.',
            'event_end_date.date_format' => 'Please select a valid event end date.',
            'event_end_time.date_format' => 'Please select a valid event end time.',
            'city.required' => 'Please enter City / Town.',
        ]);

        // Validate the complete start/end date-time range.
        // End date-time must be strictly later than start date-time.
        $startDateTime = $validated['event_date'] . ' ' . $validated['event_time'];
        $endDateTime = $validated['event_end_date'] . ' ' . $validated['event_end_time'];

        if ($endDateTime <= $startDateTime) {
            return back()->withInput()->withErrors([
                'event_end_time' => 'Event end date and time must be later than the event start date and time.',
            ]);
        }

        $country = DB::table('countries')
            ->where('id', $countryId)
            ->where('status', 1)
            ->first();

        if (!$country) {
            return back()->withInput()->withErrors([
                'country_id' => 'Please select a valid active country.',
            ]);
        }

        $request->validate([
            'region_id' => [
                'nullable',
                'integer',
                Rule::exists('art_regions', 'id')
                    ->where('country_id', $countryId)
                    ->where('status', 1),
            ],
        ], [
            'region_id.exists' => 'Please select a valid region for the selected country.',
        ]);

        $data = [
            'country_id' => $countryId,
            'region_id' => $request->input('region_id') ?: null,
            'title' => $validated['title'],
            'venue' => $validated['venue'],
            'city' => trim($validated['city']),
            'event_description' => $validated['event_description'],
            'event_date' => $validated['event_date'] . ' ' . $validated['event_time'] . ':00',
            'event_time' => $validated['event_time'],
            'event_end_date' => $validated['event_end_date'],
            'event_end_time' => $validated['event_end_time'],
            'organised_by' => $validated['organised_by'],
            'mobile' => $validated['mobile'] ?? null,
            'other_mobile' => $validated['other_mobile'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'pincode' => $validated['pincode'] ?? null,
            'lang_id' => $validated['lang_id'] ?? 1,
            'is_active' => array_key_exists('is_active', $validated)
                ? (int) $validated['is_active']
                : ($event ? $event->is_active : 1),
            'updated_by' => Auth::id(),
        ];

        if ($event) {
            $event->update($data);
            $message = 'Event updated successfully.';
        } else {
            $data['created_by'] = Auth::id();
            $data['is_deleted'] = 0;
            $event = Event::create($data);
            $message = 'Event created successfully.';
        }

        return redirect()->route('event.list')->with('success', $message);
    }

    public function changeStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:events,id'],
            'active' => ['required', 'in:0,1'],
        ]);

        $event = Event::where('is_deleted', 0)->findOrFail($validated['id']);
        $event->is_active = (int) $validated['active'];
        $event->updated_by = Auth::id();
        $event->save();

        return response()->json(['success' => true, 'message' => 'Event status updated.']);
    }

    public function delete(Request $request)
    {
        $validated = $request->validate(['id' => ['required', 'integer', 'exists:events,id']]);
        $event = Event::where('is_deleted', 0)->findOrFail($validated['id']);
        $event->is_deleted = 1;
        $event->deleted_by = Auth::id();
        $event->deleted_at = now();
        $event->updated_by = Auth::id();
        $event->save();

        return response()->json(['success' => true, 'message' => 'Event deleted successfully.']);
    }

    public function preview($id)
    {
        try {
            $eventId = $this->decryptRouteId($id);
        } catch (\Throwable $e) {
            abort(404);
        }

        $event = Event::with(['country', 'media.asset'])
            ->where('is_deleted', 0)
            ->findOrFail($eventId);

        $country = $event->country?->name;
        if (!$country) {
            $countryId = DB::table('event_country_states')
                ->where('state_code', $event->state_id)
                ->value('country_id');
            $country = $countryId
                ? DB::table('countries')->where('id', $countryId)->value('name')
                : null;
        }
        $region = $event->region_id
            ? DB::table('art_regions')->where('id', $event->region_id)->value('name')
            : null;

        return view('backend.event.preview', compact('event', 'country', 'region'));
    }

    public function uploadMedia(Request $request)
    {
        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'media_type' => ['required', Rule::in(['photo', 'video', 'youtube'])],
            'caption' => ['required', 'string', 'min:3', 'max:250'],
            'photos' => ['nullable', 'array', 'max:50'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'videos' => ['nullable', 'array', 'max:6'],
            'videos.*' => ['file', 'mimes:mp4', 'max:10240'],
            'youtube_url' => ['nullable', 'url', 'max:500'],
        ]);

        $event = Event::where('is_deleted', 0)->findOrFail($validated['event_id']);
        $type = $validated['media_type'];
        $caption = trim($validated['caption']);
        $userId = Auth::id();

        if ($type === 'photo') {
            $files = $request->file('photos', []);
            if (count($files) < 1) {
                return back()->withInput()->withErrors(['photos' => 'Please select at least one photo.']);
            }
            $currentCount = EventMedia::where('event_id', $event->id)->where('media_type', 'photo')->count();
            if ($currentCount + count($files) > 50) {
                return back()->withInput()->withErrors(['photos' => 'Maximum 50 photos are allowed for one event.']);
            }

            DB::transaction(function () use ($files, $event, $caption, $userId) {
                foreach ($files as $file) {
                    $assetId = $this->uploadAssetToPublic($file, 'events');
                    EventMedia::create([
                        'event_id' => $event->id, 'assets_id' => $assetId,
                        'media_type' => 'photo', 'caption' => $caption,
                        'is_active' => 1, 'created_by' => $userId,
                    ]);
                }
            });
        } elseif ($type === 'video') {
            $files = $request->file('videos', []);
            if (count($files) < 1) {
                return back()->withInput()->withErrors(['videos' => 'Please select at least one MP4 video.']);
            }
            $currentCount = EventMedia::where('event_id', $event->id)->where('media_type', 'video')->count();
            if ($currentCount + count($files) > 6) {
                return back()->withInput()->withErrors(['videos' => 'Maximum 6 videos are allowed for one event.']);
            }

            DB::transaction(function () use ($files, $event, $caption, $userId) {
                foreach ($files as $file) {
                    $assetId = $this->uploadAssetToPublic($file, 'events');
                    EventMedia::create([
                        'event_id' => $event->id, 'assets_id' => $assetId,
                        'media_type' => 'video', 'caption' => $caption,
                        'is_active' => 1, 'created_by' => $userId,
                    ]);
                }
            });
        } else {
            $url = trim($validated['youtube_url'] ?? '');
            if ($url === '') {
                return back()->withInput()->withErrors(['youtube_url' => 'Please enter a YouTube URL.']);
            }
            $videoId = $this->extractYoutubeId($url);
            if (!$videoId) {
                return back()->withInput()->withErrors(['youtube_url' => 'Please enter a valid YouTube URL.']);
            }
            $currentCount = EventMedia::where('event_id', $event->id)->where('media_type', 'youtube')->count();
            if ($currentCount >= 6) {
                return back()->withInput()->withErrors(['youtube_url' => 'Maximum 6 YouTube videos are allowed for one event.']);
            }
            EventMedia::create([
                'event_id' => $event->id, 'media_type' => 'youtube',
                'video_url' => $videoId, 'caption' => $caption,
                'is_active' => 1, 'created_by' => $userId,
            ]);
        }

        return redirect()->route('event.preview', $this->encryptRouteId($event->id))->with('success', 'Event media uploaded successfully.');
    }

    private function extractYoutubeId(string $url): ?string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        $id = null;

        if (str_contains($host, 'youtu.be')) {
            $id = explode('/', $path)[0] ?? null;
        } elseif (str_contains($host, 'youtube.com') || str_contains($host, 'youtube-nocookie.com')) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            if (!empty($query['v'])) {
                $id = $query['v'];
            } elseif (preg_match('~^(embed|shorts|live)/([^/]+)~', $path, $matches)) {
                $id = $matches[2];
            }
        }

        return ($id && preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) ? $id : null;
    }

    public function mediaStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:event_media,id'],
            'active' => ['required', 'in:0,1'],
        ]);

        $media = EventMedia::findOrFail($validated['id']);
        $media->is_active = (int) $validated['active'];
        $media->updated_by = Auth::id();
        $media->save();

        return response()->json(['success' => true, 'message' => 'Media status updated.']);
    }

    public function deleteMedia(Request $request)
    {
        $validated = $request->validate(['id' => ['required', 'integer', 'exists:event_media,id']]);

        $media = EventMedia::with('asset')->findOrFail($validated['id']);
        DB::transaction(function () use ($media) {
            if ($media->asset) {
                $path = public_path($media->asset->url);
                if (File::exists($path)) {
                    File::delete($path);
                }
                $media->asset->delete();
            }
            $media->delete();
        });

        return response()->json(['success' => true, 'message' => 'Media deleted successfully.']);
    }

    public function districts(Request $request)
    {
        $validated = $request->validate([
            'country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id')->where('status', 1),
            ],
            'state_id' => [
                'required',
                'integer',
                Rule::exists('event_country_states', 'state_code')
                    ->where('country_id', $request->input('country_id')),
            ],
        ]);

        $districts = DB::table('districts')
            ->where('StateCode', $validated['state_id'])
            ->orderBy('DistrictName')
            ->get(['DistrictCode', 'DistrictName']);

        return response()->json($districts);
    }

    public function states(Request $request)
    {
        $validated = $request->validate([
            'country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id')->where('status', 1),
            ],
        ]);

        $regions = DB::table('art_regions')
            ->where('country_id', $validated['country_id'])
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($regions);
    }

    private function statesForCountry($countryId)
    {
        return DB::table('states')
            ->join('event_country_states', 'event_country_states.state_code', '=', 'states.StateCode')
            ->where('event_country_states.country_id', $countryId)
            ->orderBy('states.StateName')
            ->get(['states.StateCode', 'states.StateName']);
    }

    /**
     * Determine whether the event's end date and time are in the past.
     * Missing end date/time does not block editing legacy records.
     */
    private function eventHasEnded(Event $event): bool
    {
        if (empty($event->event_end_date) || empty($event->event_end_time)) {
            return false;
        }

        $endDateTime = Carbon::parse($event->event_end_date)->toDateString()
            . ' ' . $event->event_end_time;

        return Carbon::parse($endDateTime)->lte(now());
    }

    private function encryptRouteId($id): string
    {
        return rtrim(strtr(Crypt::encrypt((string) $id), '+/', '-_'), '=');
    }

    private function decryptRouteId(string $id): string
    {
        $payload = strtr($id, '-_', '+/');
        $payload .= str_repeat('=', (4 - strlen($payload) % 4) % 4);

        return Crypt::decrypt($payload);
    }
}
