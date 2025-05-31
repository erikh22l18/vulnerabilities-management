<?php

namespace App\Domain\Tasks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Projects\Models\Project;
use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Models\User;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'vulnerability_id',
        'project_id',
        'assigned_to',
        'created_by',
        'status',
        'priority',
        'due_date'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function vulnerability(): BelongsTo
    {
        return $this->belongsTo(Vulnerability::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}