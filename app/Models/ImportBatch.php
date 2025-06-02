<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportBatch extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'original_filename',
        'status',
        'total_rows',
        'successful_rows',
        'failed_rows',
        'started_at',
        'completed_at',
        'error_summary',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user who initiated this import batch.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all row errors associated with this import batch.
     */
    public function rowErrors(): HasMany
    {
        return $this->hasMany(ImportRowError::class);
    }
}
