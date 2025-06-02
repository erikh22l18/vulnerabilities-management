<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Represents an audit log entry in the system.
 * Records actions performed by users on various models.
 *
 * @package App\Models
 * @property int $id
 * @property int|null $user_id ID of the user who performed the action.
 * @property string $action Type of action performed (e.g., 'created', 'updated', 'deleted').
 * @property string|null $description Detailed description of the action.
 * @property int|null $auditable_id ID of the model that was acted upon.
 * @property string|null $auditable_type Type (class name) of the model that was acted upon.
 * @property int|null $project_id ID of the project associated with this audit log (if applicable).
 * @property int|null $organization_id ID of the organization associated with this audit log (if applicable).
 * @property array|null $old_values JSON representation of the model's attributes before the change.
 * @property array|null $new_values JSON representation of the model's attributes after the change.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $auditable The model instance that was audited.
 * @property-read \App\Models\User|null $user The user who performed the action.
 * @property-read \App\Domain\Projects\Models\Project|null $project The project associated with the log.
 * @property-read \App\Domain\Organizations\Models\Organization|null $organization The organization associated with the log.
 */
class AuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array<string>
     */
    protected $fillable = [
        'user_id',          // ID of the user performing the action
        'action',           // Type of action (e.g., 'created', 'updated', 'deleted', 'state_changed')
        'description',      // Detailed description of what happened
        'auditable_id',     // ID of the target model (e.g., Vulnerability ID, Project ID)
        'auditable_type',   // Class name of the target model (e.g., App\Domain\Vulnerabilities\Models\Vulnerability)
        'project_id',       // Optional: Associated project ID for context
        'organization_id',  // Optional: Associated organization ID for context
        'old_values',       // JSON or serialized array of data before change
        'new_values',       // JSON or serialized array of data after change
    ];

    /**
     * The attributes that should be cast to native types.
     * 'old_values' and 'new_values' are cast to arrays for easier handling.
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the parent auditable model (e.g., Vulnerability, Project).
     * This is a polymorphic relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who performed the action.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\AuditLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project associated with this audit log, if any.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Domain\Projects\Models\Project, \App\Models\AuditLog>
     */
    public function project(): BelongsTo
    {
        // Ensures correct namespace for the Project model
        return $this->belongsTo(\App\Domain\Projects\Models\Project::class);
    }

    /**
     * Get the organization associated with this audit log, if any.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Domain\Organizations\Models\Organization, \App\Models\AuditLog>
     */
    public function organization(): BelongsTo
    {
        // Ensures correct namespace for the Organization model
        return $this->belongsTo(\App\Domain\Organizations\Models\Organization::class);
    }
}
