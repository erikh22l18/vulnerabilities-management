<?php

namespace App\Domain\Projects\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a project in the system.
 *
 * @package App\Domain\Projects\Models
 * @property int $id
 * @property string $identifier Unique identifier for the project.
 * @property string $name Name of the project.
 * @property string|null $general_objective General objective of the project.
 * @property string|null $status Status of the project (e.g., active, inactive, completed).
 * @property int $organization_id ID of the organization this project belongs to.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Domain\Organizations\Models\Organization $organization
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Vulnerabilities\Models\Vulnerability[] $vulnerabilities
 * @property-read int|null $vulnerabilities_count
 */
class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These attributes can be filled using mass assignment methods like `create()` or `update()`.
     * @var array<int, string>
     */
    protected $fillable = [
        'identifier', // Unique code or identifier for the project
        'name', // Full name of the project
        'general_objective', // Main goal or objective of the project
        'organization_id', // Foreign key for the associated organization
        'status', // Status of the project (e.g., active, inactive)
    ];

    /**
     * Get the organization that this project belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Domain\Organizations\Models\Organization, \App\Domain\Projects\Models\Project>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * The users that are associated with this project.
     * Includes the user's role in the project through the pivot table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\User>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    /**
     * Get the vulnerabilities associated with this project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Domain\Vulnerabilities\Models\Vulnerability>
     */
    public function vulnerabilities(): HasMany
    {
        return $this->hasMany(Vulnerability::class);
    }
}
