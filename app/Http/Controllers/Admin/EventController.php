<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Speaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of events.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $events = Event::with('speakers')
            ->latest()
            ->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_type' => 'required|in:workshop,seminar,conference,webinar',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string',
            'online_url' => 'nullable|url',
            'registration_url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',

            // Speaker data
            'speakers' => 'nullable|array',
            'speakers.*.name' => 'required|string|max:255',
            'speakers.*.bio' => 'nullable|string',
            'speakers.*.photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'speakers.*.institution' => 'nullable|string|max:255',
            'speakers.*.role' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Create the event
            $event = new Event();
            $event->title = $request->title;
            $event->slug = Str::slug($request->title) . '-' . time();
            $event->description = $request->description;
            $event->event_type = $request->event_type;
            $event->start_date = $request->start_date;
            $event->end_date = $request->end_date;
            $event->location = $request->location;
            $event->online_url = $request->online_url;
            $event->registration_url = $request->registration_url;
            $event->is_featured = $request->has('is_featured');
            $event->is_published = $request->has('is_published');

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('events', 'public');
                $event->image = $path;
            }

            $event->save();

            // Process speakers
            if ($request->has('speakers')) {
                foreach ($request->speakers as $speakerData) {
                    $speaker = new Speaker();
                    $speaker->event_id = $event->id;
                    $speaker->name = $speakerData['name'];
                    $speaker->bio = $speakerData['bio'] ?? null;
                    $speaker->institution = $speakerData['institution'] ?? null;
                    $speaker->role = $speakerData['role'] ?? null;

                    // Process speaker photo if provided
                    if (isset($speakerData['photo']) && $speakerData['photo'] instanceof \Illuminate\Http\UploadedFile) {
                        $path = $speakerData['photo']->store('speakers', 'public');
                        $speaker->photo = $path;
                    }

                    $speaker->save();
                }
            }

            DB::commit();

            return redirect()->route('admin.events.index')
                ->with('success', 'Event created successfully');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Error creating event: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified event.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\View\View
     */
    public function edit(Event $event)
    {
        $event->load('speakers');

        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_type' => 'required|in:workshop,seminar,conference,webinar',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string',
            'online_url' => 'nullable|url',
            'registration_url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',

            // Speaker data
            'speakers' => 'nullable|array',
            'speakers.*.id' => 'nullable|exists:speakers,id',
            'speakers.*.name' => 'required|string|max:255',
            'speakers.*.bio' => 'nullable|string',
            'speakers.*.photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'speakers.*.institution' => 'nullable|string|max:255',
            'speakers.*.role' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Update the event
            $event->title = $request->title;
            if ($event->title !== $request->title) {
                $event->slug = Str::slug($request->title) . '-' . time();
            }
            $event->description = $request->description;
            $event->event_type = $request->event_type;
            $event->start_date = $request->start_date;
            $event->end_date = $request->end_date;
            $event->location = $request->location;
            $event->online_url = $request->online_url;
            $event->registration_url = $request->registration_url;
            $event->is_featured = $request->has('is_featured');
            $event->is_published = $request->has('is_published');

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('events', 'public');
                $event->image = $path;
            }

            $event->save();

            // Get current speaker IDs
            $currentSpeakerIds = $event->speakers->pluck('id')->toArray();
            $updatedSpeakerIds = [];

            // Process speakers
            if ($request->has('speakers')) {
                foreach ($request->speakers as $speakerData) {
                    if (isset($speakerData['id'])) {
                        // Update existing speaker
                        $speaker = Speaker::find($speakerData['id']);
                        $updatedSpeakerIds[] = $speaker->id;
                    } else {
                        // Create new speaker
                        $speaker = new Speaker();
                        $speaker->event_id = $event->id;
                    }

                    $speaker->name = $speakerData['name'];
                    $speaker->bio = $speakerData['bio'] ?? null;
                    $speaker->institution = $speakerData['institution'] ?? null;
                    $speaker->role = $speakerData['role'] ?? null;

                    // Process speaker photo if provided
                    if (isset($speakerData['photo']) && $speakerData['photo'] instanceof \Illuminate\Http\UploadedFile) {
                        $path = $speakerData['photo']->store('speakers', 'public');
                        $speaker->photo = $path;
                    }

                    $speaker->save();

                    if (!isset($speakerData['id'])) {
                        $updatedSpeakerIds[] = $speaker->id;
                    }
                }
            }

            // Delete speakers that were removed
            $speakersToDelete = array_diff($currentSpeakerIds, $updatedSpeakerIds);
            Speaker::whereIn('id', $speakersToDelete)->delete();

            DB::commit();

            return redirect()->route('admin.events.index')
                ->with('success', 'Event updated successfully');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Error updating event: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified event from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Event $event)
    {
        DB::beginTransaction();

        try {
            // Delete all associated speakers
            $event->speakers()->delete();

            // Delete the event
            $event->delete();

            DB::commit();

            return redirect()->route('admin.events.index')
                ->with('success', 'Event deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Error deleting event: ' . $e->getMessage());
        }
    }
}
