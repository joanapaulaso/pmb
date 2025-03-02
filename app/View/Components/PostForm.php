<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PostForm extends Component
{
    public $post;

    /**
     * Create a new component instance.
     *
     * @param \App\Models\Post|null $post
     */
    public function __construct($post = null)
    {
        $this->post = $post;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('components.post-form');
    }
}
