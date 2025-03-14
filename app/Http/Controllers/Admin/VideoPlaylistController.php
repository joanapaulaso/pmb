<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoPlaylistController extends Controller
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
     * Display a listing of videos and playlists.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $videos = Video::latest()->paginate(10);
        $playlists = Playlist::with('videos')->latest()->paginate(10);

        return view('admin.videos.index', compact('videos', 'playlists'));
    }

    /**
     * Show the form for creating a new video.
     *
     * @return \Illuminate\View\View
     */
    public function createVideo()
    {
        $playlists = Playlist::all();
        return view('admin.videos.create', compact('playlists'));
    }

    /**
     * Store a newly created video in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'playlist_id' => 'nullable|exists:playlists,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $video = new Video();
        $video->title = $request->title;
        $video->description = $request->description;
        $video->url = $request->url;
        $video->playlist_id = $request->playlist_id;

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $video->thumbnail = $path;
        }

        $video->save();

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video created successfully');
    }

    /**
     * Show the form for editing the specified video.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\View\View
     */
    public function editVideo(Video $video)
    {
        $playlists = Playlist::all();
        return view('admin.videos.edit', compact('video', 'playlists'));
    }

    /**
     * Update the specified video in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateVideo(Request $request, Video $video)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'playlist_id' => 'nullable|exists:playlists,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $video->title = $request->title;
        $video->description = $request->description;
        $video->url = $request->url;
        $video->playlist_id = $request->playlist_id;

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $video->thumbnail = $path;
        }

        $video->save();

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video updated successfully');
    }

    /**
     * Remove the specified video from storage.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyVideo(Video $video)
    {
        $video->delete();

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video deleted successfully');
    }

    /**
     * Show the form for creating a new playlist.
     *
     * @return \Illuminate\View\View
     */
    public function createPlaylist()
    {
        return view('admin.playlists.create');
    }

    /**
     * Store a newly created playlist in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePlaylist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $playlist = new Playlist();
        $playlist->title = $request->title;
        $playlist->description = $request->description;

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('playlists', 'public');
            $playlist->thumbnail = $path;
        }

        $playlist->save();

        return redirect()->route('admin.videos.index')
            ->with('success', 'Playlist created successfully');
    }

    /**
     * Show the form for editing the specified playlist.
     *
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\View\View
     */
    public function editPlaylist(Playlist $playlist)
    {
        return view('admin.playlists.edit', compact('playlist'));
    }

    /**
     * Update the specified playlist in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePlaylist(Request $request, Playlist $playlist)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $playlist->title = $request->title;
        $playlist->description = $request->description;

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('playlists', 'public');
            $playlist->thumbnail = $path;
        }

        $playlist->save();

        return redirect()->route('admin.videos.index')
            ->with('success', 'Playlist updated successfully');
    }

    /**
     * Remove the specified playlist from storage.
     *
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyPlaylist(Playlist $playlist)
    {
        // Remove all videos from this playlist by setting playlist_id to null
        $playlist->videos()->update(['playlist_id' => null]);

        $playlist->delete();

        return redirect()->route('admin.videos.index')
            ->with('success', 'Playlist deleted successfully');
    }
}
