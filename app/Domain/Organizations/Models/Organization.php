<?php

namespace App\Domain\Organizations\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Projects\Models\Project;
use App\Models\User;

/**
 * Represents an organization in the system.
 *
 * @package App\Domain\Organizations\Models
 * @property int $id
 * @property string $name Name of the organization.
 * @property string|null $location Physical or geographical location of the organization.
 * @property string|null $business_model Description of the organization's business model.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Projects\Models\Project[] $projects
 * @property-read int|null $projects_count
 */
class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */ // Keep existing factory annotation style
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These attributes can be filled using mass assignment methods like `create()` or `update()`.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location',
        'business_model',
    ];

    /**
     * Get the users that belong to this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\User>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the projects associated with this organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Domain\Projects\Models\Project>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
