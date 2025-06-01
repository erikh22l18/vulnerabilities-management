<?php

namespace App\Domain\Tasks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Projects\Models\Project;
use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Added for potential factory usage

/**
 * Represents a task in the system, often related to a vulnerability or project.
 *
 * @package App\Domain\Tasks\Models
 * @property int $id
 * @property string $title Title of the task.
 * @property string|null $description Detailed description of the task.
 * @property int|null $vulnerability_id Foreign key for the associated vulnerability (if any).
 * @property int|null $project_id Foreign key for the associated project (if any).
 * @property int|null $assigned_to Foreign key for the user this task is assigned to.
 * @property int $created_by Foreign key for the user who created this task.
 * @property string $status Current status of the task (e.g., Open, In Progress, Closed).
 * @property string|null $priority Priority of the task (e.g., Low, Medium, High).
 * @property \Illuminate\Support\Carbon|null $due_date Due date for the task.
 * @property \Illuminate\Support\Carbon|null $completed_at Date when the task was completed.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Domain\Vulnerabilities\Models\Vulnerability|null $vulnerability
 * @property-read \App\Domain\Projects\Models\Project|null $project
 * @property-read \App\Models\User|null $assignee
 * @property-read \App\Models\User $creator
 */
class Task extends Model
{
    // If using factories, uncomment the line below
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These attributes can be filled using mass assignment methods like `create()` or `update()`.
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'description',
        'vulnerability_id', // Link to a specific vulnerability
        'project_id',       // Link to a specific project
        'assigned_to',      // User ID of the person responsible for the task
        'created_by',       // User ID of the person who created the task
        'status',           // Current status (e.g., 'Open', 'In Progress', 'Completed')
        'priority',         // Priority level (e.g., 'Low', 'Medium', 'High')
        'due_date'          // Target completion date
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date', // Casts due_date to a Carbon date object
        'completed_at' => 'datetime', // Casts completed_at to a Carbon datetime object
    ];

    /**
     * Get the vulnerability that this task is associated with (if any).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Domain\Vulnerabilities\Models\Vulnerability, \App\Domain\Tasks\Models\Task>
     */
    public function vulnerability(): BelongsTo
    {
        return $this->belongsTo(Vulnerability::class);
    }

    /**
     * Get the project that this task is associated with (if any).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Domain\Projects\Models\Project, \App\Domain\Tasks\Models\Task>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user to whom this task is assigned.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Domain\Tasks\Models\Task>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this task.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Domain\Tasks\Models\Task>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}