<?php

namespace App\Domain\Projects\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Models\User;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'name',
        'general_objective',
        'organization_id',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function vulnerabilities()
    {
        return $this->hasMany(Vulnerability::class);
    }
}
