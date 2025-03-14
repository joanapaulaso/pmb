<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    /**
     * Display a listing of events.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $events = Event::with('speakers')
            ->where('is_published', true)
            ->latest()
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    /**
     * Display the specified event.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        $event = Event::with('speakers')
            ->where('slug', $slug)
            ->firstOrFail();

        if (!$event->is_published) {
            abort(404);
        }

        return view('events.show', compact('event'));
    }
}
