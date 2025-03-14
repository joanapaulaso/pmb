<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Playlist;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Display a listing of videos and playlists.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $videos = Video::whereNull('playlist_id')->latest()->paginate(12);
        $playlists = Playlist::with('videos')->latest()->paginate(12);

        return view('videos.index', compact('videos', 'playlists'));
    }

    /**
     * Display the specified playlist.
     *
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\View\View
     */
    public function showPlaylist(Playlist $playlist)
    {
        $playlist->load('videos');
        return view('videos.show-playlist', compact('playlist'));
    }
}
