<?php

namespace App\Domain\Tasks\ViewModels;

use Illuminate\Pagination\LengthAwarePaginator;

class TaskIndexViewModel
{
    public function __construct(
        public string $title,
        public ?string $subtitle = null,
        public ?string $context = null,
        public ?string $createRoute = null,
        public ?string $editRoute = null,
        public ?string $deleteRoute = null,
        public ?string $showRoute = null,
        public ?string $backRoute = null,
        public ?LengthAwarePaginator $tasks = null,
        public bool $can_create = false,
    ) {}
}