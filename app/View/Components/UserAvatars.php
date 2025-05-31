<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Database\Eloquent\Collection;

class UserAvatars extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?Collection $users = null,
        public int $limit = 3
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-avatars');
    }
}
