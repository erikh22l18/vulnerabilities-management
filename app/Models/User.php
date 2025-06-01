<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Domain\Projects\Models\Project;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Vulnerabilities\Models\Vulnerability;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a user in the system.
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $identification Encrypted identification number.
 * @property string|null $area
 * @property int|null $organization_id
 * @property-read string $profile_photo_url
 * @property-read \App\Domain\Organizations\Models\Organization|null $organization
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Projects\Models\Project[] $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Vulnerabilities\Models\Vulnerability[] $vulnerabilities
 * @property-read int|null $vulnerabilities_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;
    /** @use HasFactory<\Database\Factories\UserFactory> */ // Keep existing factory annotation style
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     * These attributes can be filled using mass assignment methods like `create()` or `update()`.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'identification', // User's identification number (e.g., national ID, employee ID)
        'area',           // Functional area or department of the user
        'organization_id', // ID of the organization the user belongs to
    ];

    /**
     * The attributes that should be hidden for serialization.
     * These attributes will not be included in JSON or array representations of the model.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     * These virtual attributes will be included in JSON or array representations of the model.
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url', // URL of the user's profile photo
    ];

    /**
     * Get the attributes that should be cast.
     * Defines how attributes are converted to common data types.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Automatically hashes passwords when set
            'identification' => 'encrypted', // Encrypts the identification field
        ];
    }

    /**
     * Get the organization that the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Domain\Organizations\Models\Organization, \App\Models\User>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * The projects that the user is associated with.
     * Includes the user's role in the project through the pivot table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Domain\Projects\Models\Project>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withPivot('role')->withTimestamps();
    }

    /**
     * The vulnerabilities that are assigned to or associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Domain\Vulnerabilities\Models\Vulnerability>
     */
    public function vulnerabilities(): BelongsToMany
    {
        return $this->belongsToMany(Vulnerability::class, 'vulnerability_user')
                    ->withTimestamps();
    }

    // Example of a one-to-many relationship (if a user creates vulnerabilities)
    // /**
    //  * Get the vulnerabilities created by this user.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Domain\Vulnerabilities\Models\Vulnerability>
    //  */
    // public function vulnerabilitiesCreated(): HasMany
    // {
    //     return $this->hasMany(Vulnerability::class, 'created_by');
    // }
}
