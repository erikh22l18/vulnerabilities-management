<?php

namespace App\Domain\Tasks\ViewModels;

use App\Domain\Vulnerabilities\Models\Vulnerability;
use Illuminate\Database\Eloquent\Collection;

class TaskCreateViewModel
{
    public function __construct(
        public string $title,
        public Vulnerability $vulnerability,
        public Collection $users,
        public array $priorities = ['baja', 'media', 'alta'],
        public ?string $backRoute = null,
        public ?string $createRoute = null,
    ) {}
}