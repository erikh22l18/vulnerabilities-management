<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportRowError extends Model
{
    use HasUlids;

    protected $fillable = [
        'import_batch_id',
        'row_number',
        'error_messages',
        'row_data'
    ];

    protected $casts = [
        'error_messages' => 'array', // Store as JSON, cast to array
        'row_data' => 'array',       // Store as JSON, cast to array
    ];

    /**
     * Get the import batch this row error belongs to.
     */
    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }
}
