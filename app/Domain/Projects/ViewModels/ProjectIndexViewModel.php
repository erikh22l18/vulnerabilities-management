<?php

namespace App\Domain\Projects\ViewModels;

use Illuminate\Pagination\LengthAwarePaginator;

class ProjectIndexViewModel
{
    public function __construct(
        public string $title,
        public ?string $subtitle = null,
        public ?string $context = null,
        public ?string $backRoute = null,
        public ?LengthAwarePaginator $projects = null,
        public bool $can_create = false,
        public bool $can_import = false,
        public ?string $createRoute = null
    ) {}
}