<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\User;

class PublicProfile extends Component
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('components.public-profile');
    }
}
