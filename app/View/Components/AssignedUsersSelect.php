<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AssignedUsersSelect extends Component
{
    public $projects;
    public $selectedProject;
    public $assignedUsers;
    public $responsibleId;

    /**
     * Create a new component instance.
     */
    public function __construct($projects, $selectedProject = null, $assignedUsers = [], $responsibleId = null)
    {
        $this->projects = $projects;
        $this->selectedProject = $selectedProject;
        $this->assignedUsers = $assignedUsers;
        $this->responsibleId = $responsibleId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.assigned-users-select');
    }
}
