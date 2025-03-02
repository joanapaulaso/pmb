<?php

namespace App\View\Components;

use App\Models\Post;
use Illuminate\View\Component;

class PostList extends Component
{
    public $posts;

    public function __construct($posts)
    {
        $this->posts = $posts;
    }

    public function render()
    {
        return view('components.post-list');
    }
}
