<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action', // Cambiado de action_type
        'description',
        'auditable_id',   // Cambiado de target_id
        'auditable_type', // Cambiado de target_type
        'project_id',       // Nuevo
        'organization_id',  // Nuevo
        'old_values',       // Nuevo
        'new_values',       // Nuevo
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Obtener el modelo padre del log de auditoría (Vulnerability, Project, etc.).
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * El usuario que realizó la acción.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * El proyecto asociado con el log de auditoría.
     */
    public function project()
    {
        // Asegúrate de que el namespace del modelo Project es correcto
        return $this->belongsTo(\App\Domain\Projects\Models\Project::class);
    }

    /**
     * La organización asociada con el log de auditoría.
     */
    public function organization()
    {
        // Asegúrate de que el namespace del modelo Organization es correcto
        return $this->belongsTo(\App\Domain\Organizations\Models\Organization::class);
    }
}
